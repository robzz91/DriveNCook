<?php

require_once __DIR__.'/../config/db.php';

class FranchiseeModel {
    public static function paginate(int $limit, int $offset): array {
        $pdo = db();
        $count = (int)$pdo->query('SELECT COUNT(*) AS c FROM franchisees')->fetch()['c'];
        $stmt = $pdo->prepare('SELECT id, name, email, phone, joined_at, status FROM franchisees ORDER BY id ASC LIMIT ? OFFSET ?');
        // LIMIT/OFFSET doivent être passés en entiers (PDO::PARAM_INT)
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        return [
            'items' => $items,
            'total' => $count,
        ];
    }

    public static function create(array $data): array {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO franchisees(name,email,phone,joined_at,status) VALUES(?,?,?,?,?)');
        $stmt->execute([
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? null,
            $data['joined_at'] ?? null,
            $data['status'] ?? 'active',
        ]);
        $id = (int)$pdo->lastInsertId();
        return self::find($id) ?: ['id' => $id] + $data;
    }

    public static function update(int $id, array $data): ?array {
        $pdo = db();
        $fields = [];
        $params = [];
        foreach (['name','email','phone','joined_at','status'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (!$fields) return self::find($id);
        $params[] = $id;
        $sql = 'UPDATE franchisees SET '.implode(', ', $fields).' WHERE id = ?';
        $pdo->prepare($sql)->execute($params);
        return self::find($id);
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM franchisees WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare('SELECT id, name, email, phone, joined_at, status FROM franchisees WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}


