<?php

require_once __DIR__.'/../config/db.php';

class WarehouseModel {
    public static function all(): array {
        $pdo = db();
        return $pdo->query('SELECT id, name, address, capacity FROM warehouses ORDER BY id ASC')->fetchAll();
    }

    public static function search(string $q): array {
        $pdo = db();
        $like = '%'.$q.'%';
        $stmt = $pdo->prepare('SELECT id, name, address, capacity FROM warehouses WHERE name LIKE ? OR address LIKE ? ORDER BY id ASC');
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, name, address, capacity FROM warehouses WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO warehouses(name,address,capacity) VALUES(?,?,?)');
        $stmt->execute([
            $data['name'] ?? '',
            $data['address'] ?? null,
            isset($data['capacity']) ? (int)$data['capacity'] : null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): ?array {
        $pdo = db();
        $existing = self::find($id);
        if (!$existing) return null;
        $name = $data['name'] ?? $existing['name'];
        $address = $data['address'] ?? $existing['address'];
        $capacity = array_key_exists('capacity', $data) ? (is_null($data['capacity']) ? null : (int)$data['capacity']) : $existing['capacity'];
        $stmt = $pdo->prepare('UPDATE warehouses SET name = ?, address = ?, capacity = ? WHERE id = ?');
        $stmt->execute([$name, $address, $capacity, $id]);
        return self::find($id);
    }

    public static function delete(int $id): void {
        $pdo = db();
        $stmt = $pdo->prepare('DELETE FROM warehouses WHERE id = ?');
        $stmt->execute([$id]);
    }
}


