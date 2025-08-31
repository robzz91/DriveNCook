<?php

require_once __DIR__.'/../config/db.php';

class SaleModel {
    public static function listFiltered(?int $franchiseeId, ?string $from, ?string $to, bool $admin, ?int $userFranchiseeId): array {
        $pdo = db();
        $clauses = [];
        $params = [];
        if (!$admin) {
            $clauses[] = 'franchisee_id = ?';
            $params[] = (int)($userFranchiseeId ?? 0);
        } else if (!empty($franchiseeId)) {
            $clauses[] = 'franchisee_id = ?';
            $params[] = (int)$franchiseeId;
        }
        if (!empty($from)) { $clauses[] = 'sold_at >= ?'; $params[] = $from.' 00:00:00'; }
        if (!empty($to))   { $clauses[] = 'sold_at <= ?'; $params[] = $to.' 23:59:59'; }
        $where = count($clauses) ? ('WHERE '.implode(' AND ', $clauses)) : '';
        $stmt = $pdo->prepare('SELECT id, franchisee_id, amount, sold_at FROM sales '.$where.' ORDER BY sold_at DESC, id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data, bool $admin, ?int $userFranchiseeId): int {
        $pdo = db();
        $fid = $admin ? (int)($data['franchisee_id'] ?? 0) : (int)($userFranchiseeId ?? 0);
        $stmt = $pdo->prepare('INSERT INTO sales(franchisee_id, amount, sold_at) VALUES(?,?,?)');
        $stmt->execute([
            $fid,
            isset($data['amount']) ? (float)$data['amount'] : 0,
            $data['sold_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$pdo->lastInsertId();
    }
}



