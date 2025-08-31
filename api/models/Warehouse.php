<?php

require_once __DIR__.'/../config/db.php';

class WarehouseModel {
    public static function all(): array {
        $pdo = db();
        return $pdo->query('SELECT id, name, address FROM warehouses ORDER BY id ASC')->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO warehouses(name,address) VALUES(?,?)');
        $stmt->execute([
            $data['name'] ?? '',
            $data['address'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
}


