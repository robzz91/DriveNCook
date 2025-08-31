<?php

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../auth.php';

class TruckModel {
    public static function all(): array {
        $pdo = db();
        return $pdo->query('SELECT id, plate, status, last_service_at, franchisee_id FROM trucks ORDER BY id ASC')->fetchAll();
    }

    public static function allScoped(array $user): array {
        $pdo = db();
        [$where, $params] = auth_scope_clause($user, 'franchisee_id');
        $sql = 'SELECT id, plate, status, last_service_at, franchisee_id FROM trucks WHERE 1=1'.$where.' ORDER BY id ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, plate, status, last_service_at, franchisee_id FROM trucks WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO trucks(plate,status,last_service_at,franchisee_id) VALUES(?,?,?,?)');
        $stmt->execute([
            $data['plate'] ?? '',
            $data['status'] ?? 'active',
            $data['last_service_at'] ?? null,
            $data['franchisee_id'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): ?array {
        $pdo = db();
        $existing = self::find($id);
        if (!$existing) return null;
        $plate = $data['plate'] ?? $existing['plate'];
        $status = $data['status'] ?? $existing['status'];
        $last = $data['last_service_at'] ?? $existing['last_service_at'];
        $franchiseeId = array_key_exists('franchisee_id', $data) ? ($data['franchisee_id'] === null ? null : (int)$data['franchisee_id']) : $existing['franchisee_id'];
        $stmt = $pdo->prepare('UPDATE trucks SET plate = ?, status = ?, last_service_at = ?, franchisee_id = ? WHERE id = ?');
        $stmt->execute([$plate, $status, $last, $franchiseeId, $id]);
        return self::find($id);
    }

    public static function delete(int $id): void {
        $pdo = db();
        $stmt = $pdo->prepare('DELETE FROM trucks WHERE id = ?');
        $stmt->execute([$id]);
    }
}


