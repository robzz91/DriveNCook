<?php

require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../auth.php';

class UsersController {
    public static function index(): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $filters = [
            'role' => isset($_GET['role']) && $_GET['role'] !== '' ? $_GET['role'] : null,
            'q' => isset($_GET['q']) ? trim($_GET['q']) : null,
        ];
        $items = UserModel::list($filters);
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function store(array $input): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);

        $role = strtolower(trim($input['role'] ?? 'franchisee'));
        $name = trim($input['name'] ?? '');
        $password = (string)($input['password'] ?? 'password');
        $franchiseeId = isset($input['franchisee_id']) ? (int)$input['franchisee_id'] : null;

        if ($name === '' || $role === '') {
            self::json(['success'=>false,'data'=>null,'error'=>'name et role requis'],422); return;
        }
        try {
            $pdo = db();
            // Email requis pour toute création
            if (empty($input['email'])) { self::json(['success'=>false,'data'=>null,'error'=>'email requis'],422); return; }

            // Si role=franchisee et pas d'id fourni: créer automatiquement le franchisé minimal
            if ($role === 'franchisee' && empty($franchiseeId)) {
                $stmt = $pdo->prepare('INSERT INTO franchisees(name,email,created_at,updated_at) VALUES(?,?,NOW(),NOW())');
                $stmt->execute([$name, $input['email']]);
                $franchiseeId = (int)$pdo->lastInsertId();
            }
            // Si role=franchisee avec id fourni: imposer l'email à celui du franchisé
            if ($role === 'franchisee' && !empty($franchiseeId)) {
                $stmt = $pdo->prepare('SELECT email FROM franchisees WHERE id = ?');
                $stmt->execute([$franchiseeId]);
                $fr = $stmt->fetch();
                if (!$fr) { self::json(['success'=>false,'data'=>null,'error'=>'franchisee_id invalide'],422); return; }
                $input['email'] = $fr['email'];
            }

            // Si role=client et pas d'id fourni: créer automatiquement le client minimal
            $clientId = isset($input['client_id']) ? (int)$input['client_id'] : null;
            if ($role === 'client' && empty($clientId)) {
                // Découper nom/prénom simplement
                $prenom = '';
                $nomVal = trim($name);
                if (strpos($name, ' ') !== false) {
                    [$prenom, $nomPart] = [trim(strtok($name, ' ')), trim(substr($name, strlen(strtok($name, ' '))))];
                    if ($nomPart !== '') $nomVal = $nomPart; else $nomVal = $name;
                }
                $stmt = $pdo->prepare('INSERT INTO clients(nom,prenom,email,created_at,updated_at) VALUES(?,?,?,?,?)');
                $now = date('Y-m-d H:i:s');
                $stmt->execute([$nomVal, $prenom, $input['email'], $now, $now]);
                $clientId = (int)$pdo->lastInsertId();
            }

            $data = [ 'name'=>$name, 'email'=>$input['email'], 'password'=>$password, 'role'=>$role ];
            if ($role === 'franchisee') $data['franchisee_id'] = $franchiseeId;
            if ($role === 'client') $data['client_id'] = $clientId;
            $id = UserModel::create($data);
            self::json(['success'=>true,'data'=>['id'=>$id,'name'=>$name,'email'=>$input['email'],'role'=>$role,'franchisee_id'=>$franchiseeId,'client_id'=>$clientId],'error'=>null],201);
        } catch (Throwable $e) {
            self::json(['success'=>false,'data'=>null,'error'=>'Erreur: '.$e->getMessage()],422);
        }
    }

    public static function update(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $input = self::readInput();
        try {
            $pdo = db();
            // Récupérer existant
            $existing = UserModel::find($id);
            if (!$existing) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
            $role = $input['role'] ?? $existing['role'];
            $email = $input['email'] ?? $existing['email'];
            $name = $input['name'] ?? ($existing['name'] ?? '');
            // Auto-création si passage en franchisee/client sans ID fourni
            if ($role === 'franchisee' && empty($existing['franchisee_id']) && empty($input['franchisee_id'])) {
                $stmt = $pdo->prepare('INSERT INTO franchisees(name,email,created_at,updated_at) VALUES(?,?,NOW(),NOW())');
                $stmt->execute([$name, $email]);
                $input['franchisee_id'] = (int)$pdo->lastInsertId();
            }
            if ($role === 'client' && empty($existing['client_id']) && empty($input['client_id'])) {
                $prenom = '';
                $nomVal = trim((string)$name);
                if (strpos((string)$name, ' ') !== false) {
                    $first = strtok($name, ' ');
                    $rest = trim(substr($name, strlen($first)));
                    if ($rest !== '') { $prenom = $first; $nomVal = $rest; }
                }
                $now = date('Y-m-d H:i:s');
                $stmt = $pdo->prepare('INSERT INTO clients(nom,prenom,email,created_at,updated_at) VALUES(?,?,?,?,?)');
                $stmt->execute([$nomVal, $prenom, $email, $now, $now]);
                $input['client_id'] = (int)$pdo->lastInsertId();
            }
            $updated = UserModel::update($id, $input);
        } catch (Throwable $e) {
            self::json(['success'=>false,'data'=>null,'error'=>'Erreur: '.$e->getMessage()],422); return;
        }
        if (!$updated) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        self::json(['success'=>true,'data'=>$updated,'error'=>null]);
    }

    public static function destroy(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        UserModel::delete($id);
        self::json(['success'=>true,'data'=>['deleted'=>true],'error'=>null]);
    }

    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private static function readInput(): array {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        if (is_array($data)) return $data;
        if (!empty($_POST)) return $_POST;
        $parsed = [];
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }
}



