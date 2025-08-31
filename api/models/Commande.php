<?php

require_once __DIR__.'/../config/db.php';

class CommandeModel {
    public static function all(): array {
        return db()->query('SELECT id, client_id, status, paye, total_ht, total_ttc FROM commandes ORDER BY id DESC')->fetchAll();
    }

    public static function create(array $data): array {
        $pdo = db();
        $now = date('Y-m-d H:i:s');
        $clientId = (int)($data['client_id'] ?? 0);
        $stmt = $pdo->prepare('INSERT INTO commandes(client_id,status,paye,total_ht,total_ttc,created_at,updated_at) VALUES(?,?,?,?,?,?,?)');
        $stmt->execute([$clientId,'brouillon',0,0,0,$now,$now]);
        $id = (int)$pdo->lastInsertId();
        return ['id'=>$id,'client_id'=>$clientId,'status'=>'brouillon','paye'=>0,'total_ht'=>'0.00','total_ttc'=>'0.00'];
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM commandes WHERE id = ?');
        $stmt->execute([$id]);
    }
}


