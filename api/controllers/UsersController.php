<?php

require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../auth.php';

class UsersController {
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
        if ($role === 'franchisee') {
            if (empty($franchiseeId)) { self::json(['success'=>false,'data'=>null,'error'=>'franchisee_id requis pour role franchisé'],422); return; }
            // Récupérer email du franchisé et l'imposer
            $stmt = db()->prepare('SELECT email FROM franchisees WHERE id = ?');
            $stmt->execute([$franchiseeId]);
            $fr = $stmt->fetch();
            if (!$fr) { self::json(['success'=>false,'data'=>null,'error'=>'franchisee_id invalide'],422); return; }
            $input['email'] = $fr['email'];
        } else {
            if (empty($input['email'])) { self::json(['success'=>false,'data'=>null,'error'=>'email requis'],422); return; }
        }

        $id = UserModel::create([
            'name' => $name,
            'email' => $input['email'],
            'password' => $password,
            'role' => $role,
            'franchisee_id' => $franchiseeId,
        ]);
        self::json(['success'=>true,'data'=>['id'=>$id,'name'=>$name,'email'=>$input['email'],'role'=>$role,'franchisee_id'=>$franchiseeId],'error'=>null],201);
    }

    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}



