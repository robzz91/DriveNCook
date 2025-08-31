<?php

require_once __DIR__.'/../config/db.php';

class TruckMaintenanceModel {
    public static function listForTruck(int $truckId): array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, truck_id, title, description, cost, serviced_at FROM truck_maintenances WHERE truck_id = ? ORDER BY serviced_at DESC, id DESC');
        $stmt->execute([$truckId]);
        return $stmt->fetchAll();
    }

    public static function create(int $truckId, array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO truck_maintenances(truck_id,title,description,cost,serviced_at) VALUES(?,?,?,?,?)');
        $stmt->execute([
            $truckId,
            $data['title'] ?? '',
            $data['description'] ?? null,
            isset($data['cost']) ? (float)$data['cost'] : 0,
            $data['serviced_at'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
}



