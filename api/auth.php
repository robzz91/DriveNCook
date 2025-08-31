<?php

require_once __DIR__.'/config/db.php';

// Auth minimale: Basic Auth (email:password) depuis l'en-tête Authorization.
// Rôles pris en charge: 'admin' (accès global) et 'franchisee' (accès limité à son franchisee_id).

function auth_get_authorization_header(): ?string {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) return $_SERVER['HTTP_AUTHORIZATION'];
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $key => $value) {
            if (strcasecmp($key, 'Authorization') === 0) return $value;
        }
    }
    return null;
}

function auth_parse_basic(): ?array {
    // Utilise PHP_AUTH_USER/PHP_AUTH_PW si fournis par le serveur
    if (!empty($_SERVER['PHP_AUTH_USER'])) {
        return [$_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ?? ''];
    }
    $hdr = auth_get_authorization_header();
    if (!$hdr || stripos($hdr, 'Basic ') !== 0) return null;
    $b64 = substr($hdr, 6);
    $decoded = base64_decode($b64, true);
    if ($decoded === false) return null;
    $parts = explode(':', $decoded, 2);
    if (count($parts) !== 2) return null;
    return [$parts[0], $parts[1]];
}

/**
 * Retourne l'utilisateur authentifié (array id, email, role, franchisee_id, name, password) ou null.
 */
function auth_user(): ?array {
    [$email, $password] = auth_parse_basic() ?? [null, null];
    if (!$email) return null;

    $stmt = db()->prepare('SELECT id, name, email, password, role, franchisee_id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) return null;

    $hash = (string)($user['password'] ?? '');
    // Tolérer mot de passe en clair si password_verify échoue
    $ok = false;
    if ($hash !== '') {
        if (function_exists('password_verify')) {
            $ok = password_verify($password, $hash);
        }
        if (!$ok && hash_equals($hash, $password)) {
            $ok = true;
        }
    }
    if (!$ok) return null;

    // Normaliser les clés/valeurs
    $user['role'] = $user['role'] ?? 'franchisee';
    $user['franchisee_id'] = $user['franchisee_id'] ?? null;
    return $user;
}

/** Force auth, sinon 401. Retourne l'utilisateur. */
function auth_require(): array {
    $user = auth_user();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Non authentifié']);
        exit;
    }
    return $user;
}

/** Vérifie que l'utilisateur a l'un des rôles requis, sinon 403. */
function auth_require_role(array $user, array $roles): void {
    $role = (string)($user['role'] ?? '');
    foreach ($roles as $r) {
        if (strcasecmp($role, $r) === 0) return;
    }
    http_response_code(403);
    echo json_encode(['message' => 'Accès interdit']);
    exit;
}

/** True si admin. */
function auth_is_admin(array $user): bool {
    return strcasecmp((string)($user['role'] ?? ''), 'admin') === 0;
}

/**
 * Retourne un fragment WHERE et paramètres pour restreindre par franchisee_id si rôle franchisé.
 * Exemple d'utilisation:
 *   [$where, $params] = auth_scope_clause($user, 't.franchisee_id');
 *   $sql = 'SELECT * FROM trucks t WHERE 1=1'.$where;
 *   $stmt = db()->prepare($sql); $stmt->execute($params);
 */
function auth_scope_clause(array $user, string $column = 'franchisee_id'): array {
    if (auth_is_admin($user)) return ['', []];
    $fid = $user['franchisee_id'] ?? null;
    if ($fid === null || $fid === '') return [' AND 1=0', []]; // pas de données
    return [' AND '.$column.' = ?', [(int)$fid]];
}


