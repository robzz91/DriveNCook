<?php

require_once __DIR__.'/../config/db.php';

class SupplyModel {
    public static function allAdmin(): array {
        $pdo = db();
        return $pdo->query('SELECT id, franchisee_id, warehouse_id, total_amount FROM supplies ORDER BY id DESC')->fetchAll();
    }

    public static function allScoped(int $franchiseeId): array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, franchisee_id, warehouse_id, total_amount FROM supplies WHERE franchisee_id = ? ORDER BY id DESC');
        $stmt->execute([$franchiseeId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, franchisee_id, warehouse_id, total_amount FROM supplies WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO supplies(franchisee_id, warehouse_id, total_amount) VALUES(?,?,0)');
        $stmt->execute([
            isset($data['franchisee_id']) ? (int)$data['franchisee_id'] : null,
            isset($data['warehouse_id']) ? (int)$data['warehouse_id'] : null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): ?array {
        $pdo = db();
        $existing = self::find($id);
        if (!$existing) return null;
        $franchiseeId = array_key_exists('franchisee_id', $data) ? ($data['franchisee_id'] === null ? null : (int)$data['franchisee_id']) : $existing['franchisee_id'];
        $warehouseId = array_key_exists('warehouse_id', $data) ? ($data['warehouse_id'] === null ? null : (int)$data['warehouse_id']) : $existing['warehouse_id'];
        $stmt = $pdo->prepare('UPDATE supplies SET franchisee_id = ?, warehouse_id = ? WHERE id = ?');
        $stmt->execute([$franchiseeId, $warehouseId, $id]);
        return self::find($id);
    }

    public static function delete(int $id): void {
        $pdo = db();
        $stmt = $pdo->prepare('DELETE FROM supplies WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function recalcTotal(int $supplyId): void {
        $pdo = db();
        $stmt = $pdo->prepare('UPDATE supplies s SET total_amount = (
            SELECT COALESCE(SUM(si.quantity * si.unit_price),0)
            FROM supply_items si WHERE si.supply_id = s.id
        ) WHERE s.id = ?');
        $stmt->execute([$supplyId]);
    }
}



