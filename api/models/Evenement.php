<?php

require_once __DIR__.'/../config/db.php';

class EvenementModel {
    public static function all(): array {
        return db()->query('SELECT id, titre, description, date_evenement FROM evenements ORDER BY date_evenement DESC')->fetchAll();
    }

    public static function create(array $data): array {
        $pdo = db();
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO evenements(titre,description,date_evenement,created_at,updated_at) VALUES(?,?,?,?,?)');
        $stmt->execute([
            trim($data['titre'] ?? ''),
            $data['description'] ?? null,
            $data['date_evenement'] ?? '',
            $now,
            $now,
        ]);
        $id = (int)$pdo->lastInsertId();
        return ['id'=>$id,'titre'=>$data['titre'] ?? '', 'description'=>$data['description'] ?? null, 'date_evenement'=>$data['date_evenement'] ?? ''];
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM evenements WHERE id = ?');
        $stmt->execute([$id]);
    }
}


