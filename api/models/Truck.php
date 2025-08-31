<?php

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../auth.php';

class TruckModel {
    public static function all(): array {
        $pdo = db();
        return $pdo->query('SELECT id, plate, model, status, last_service_at, franchisee_id, warehouse_id FROM trucks ORDER BY id ASC')->fetchAll();
    }

    public static function allScoped(array $user): array {
        $pdo = db();
        [$where, $params] = auth_scope_clause($user, 'franchisee_id');
        $sql = 'SELECT id, plate, model, status, last_service_at, franchisee_id, warehouse_id FROM trucks WHERE 1=1'.$where.' ORDER BY id ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO trucks(plate,model,status,last_service_at,franchisee_id,warehouse_id) VALUES(?,?,?,?,?,?)');
        $stmt->execute([
            $data['plate'] ?? '',
            $data['model'] ?? null,
            $data['status'] ?? 'available',
            $data['last_service_at'] ?? null,
            $data['franchisee_id'] ?? null,
            $data['warehouse_id'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
}


