<?php

header('Content-Type: application/json');
require_once __DIR__.'/config/db.php';

function read_body(): array {
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
    if (!empty($_POST)) return $_POST;
    $parsed = [];
    parse_str($raw, $parsed);
    return is_array($parsed) ? $parsed : [];
}

try {
    $in = read_body();
    $nom = trim($in['nom'] ?? '');
    $prenom = trim($in['prenom'] ?? '');
    $email = trim($in['email'] ?? '');
    $password = (string)($in['password'] ?? '');

    if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'nom, prenom, email, password requis']);
        exit;
    }

    $pdo = db();

    // Vérifier unicité email côté users et clients
    $chk = $pdo->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
    $chk->execute([$email]);
    if ($chk->fetch()) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Email déjà utilisé']); exit; }
    $chk2 = $pdo->prepare('SELECT 1 FROM clients WHERE email = ? LIMIT 1');
    $chk2->execute([$email]);
    if ($chk2->fetch()) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Email déjà utilisé']); exit; }

    $pdo->beginTransaction();
    // Créer le client
    $stmtC = $pdo->prepare('INSERT INTO clients (nom, prenom, email, created_at, updated_at) VALUES (?,?,?,?,?)');
    $now = date('Y-m-d H:i:s');
    $stmtC->execute([$nom, $prenom, $email, $now, $now]);
    $clientId = (int)$pdo->lastInsertId();

    // Créer l'utilisateur lié (role=client, client_id)
    $fullName = trim($prenom.' '.$nom);
    $stmtU = $pdo->prepare('INSERT INTO users (name, email, password, role, client_id, created_at, updated_at) VALUES (?,?,?,?,?,?,?)');
    // Stocker tel quel (login accepte hash ou clair)
    $stmtU->execute([$fullName === '' ? null : $fullName, $email, $password, 'client', $clientId, $now, $now]);
    $userId = (int)$pdo->lastInsertId();

    $pdo->commit();
    echo json_encode(['success'=>true,'data'=>['client_id'=>$clientId,'user_id'=>$userId],'error'=>null]);
} catch (Throwable $e) {
    try { if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); } } catch (Throwable $e2) {}
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erreur serveur']);
}


