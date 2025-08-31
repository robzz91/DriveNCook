<?php

require_once __DIR__.'/../config/db.php';

class ClientModel {
    public static function all(): array {
        return db()->query('SELECT id, nom, email FROM clients ORDER BY id ASC')->fetchAll();
    }

    public static function create(array $data): array {
        $pdo = db();
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO clients(nom,email,created_at,updated_at) VALUES(?,?,?,?)');
        $stmt->execute([
            trim($data['nom'] ?? ''),
            trim($data['email'] ?? ''),
            $now,
            $now,
        ]);
        $id = (int)$pdo->lastInsertId();
        return ['id'=>$id,'nom'=>$data['nom'] ?? '', 'email'=>$data['email'] ?? ''];
    }

    public static function update(int $id, array $data): ?array {
        $pdo = db();
        $fields = [];
        $params = [];
        if (isset($data['nom'])) { $fields[] = 'nom = ?'; $params[] = trim($data['nom']); }
        if (isset($data['email'])) { $fields[] = 'email = ?'; $params[] = trim($data['email']); }
        if (empty($fields)) return self::find($id);
        $fields[] = 'updated_at = ?';
        $params[] = date('Y-m-d H:i:s');
        $params[] = $id;
        $sql = 'UPDATE clients SET '.implode(', ', $fields).' WHERE id = ?';
        $pdo->prepare($sql)->execute($params);
        return self::find($id);
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare('SELECT id, nom, email FROM clients WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM clients WHERE id = ?');
        $stmt->execute([$id]);
    }
}


