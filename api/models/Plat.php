<?php

require_once __DIR__.'/../config/db.php';

class PlatModel {
    public static function all(): array {
        return db()->query('SELECT id, nom, description, prix FROM plats ORDER BY id ASC')->fetchAll();
    }

    public static function create(array $data): array {
        $pdo = db();
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO plats(nom,description,prix,created_at,updated_at) VALUES(?,?,?,?,?)');
        $stmt->execute([
            trim($data['nom'] ?? ''),
            $data['description'] ?? null,
            (float)($data['prix'] ?? 0),
            $now,
            $now,
        ]);
        $id = (int)$pdo->lastInsertId();
        return ['id'=>$id,'nom'=>$data['nom'] ?? '', 'description'=>$data['description'] ?? null, 'prix'=>(string)($data['prix'] ?? 0)];
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM plats WHERE id = ?');
        $stmt->execute([$id]);
    }
}


