<?php

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/Supply.php';

class SupplyItemModel {
    public static function listForSupply(int $supplyId): array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, supply_id, sku, label, quantity, unit_price, source FROM supply_items WHERE supply_id = ? ORDER BY id ASC');
        $stmt->execute([$supplyId]);
        return $stmt->fetchAll();
    }

    public static function create(int $supplyId, array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO supply_items(supply_id, sku, label, quantity, unit_price, source) VALUES(?,?,?,?,?,?)');
        $stmt->execute([
            $supplyId,
            $data['sku'] ?? '',
            $data['label'] ?? '',
            isset($data['quantity']) ? (int)$data['quantity'] : 1,
            isset($data['unit_price']) ? (float)$data['unit_price'] : 0,
            in_array(($data['source'] ?? 'warehouse'), ['warehouse','free'], true) ? $data['source'] : 'warehouse',
        ]);
        $id = (int)$pdo->lastInsertId();
        SupplyModel::recalcTotal($supplyId);
        return $id;
    }

    public static function delete(int $id): void {
        $pdo = db();
        // récupérer supply_id pour recalcul
        $stmt = $pdo->prepare('SELECT supply_id FROM supply_items WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $supplyId = $row ? (int)$row['supply_id'] : null;

        $stmt = $pdo->prepare('DELETE FROM supply_items WHERE id = ?');
        $stmt->execute([$id]);

        if ($supplyId !== null) {
            SupplyModel::recalcTotal($supplyId);
        }
    }
}



