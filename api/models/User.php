<?php

require_once __DIR__.'/../config/db.php';

class UserModel {
    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO users(name,email,password,role,franchisee_id,created_at,updated_at) VALUES(?,?,?,?,?,NOW(),NOW())');
        $stmt->execute([
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['role'] ?? 'franchisee',
            isset($data['franchisee_id']) ? (int)$data['franchisee_id'] : null,
        ]);
        return (int)$pdo->lastInsertId();
    }
}



