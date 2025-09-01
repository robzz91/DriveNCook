<?php

require_once __DIR__.'/../config/db.php';

class UserModel {
    public static function create(array $data): int {
        $pdo = db();
        $stmt = $pdo->prepare('INSERT INTO users(name,email,password,role,franchisee_id,created_at,updated_at) VALUES(?,?,?,?,?,NOW(),NOW())');
        $stmt->execute([
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['role'] ?? 'franchisee',
            isset($data['franchisee_id']) ? (int)$data['franchisee_id'] : null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function find(int $id): ?array {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, name, email, role, franchisee_id, client_id FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function list(array $filters = []): array {
        $pdo = db();
        $where = [];
        $params = [];
        if (!empty($filters['role'])) { $where[] = 'role = ?'; $params[] = $filters['role']; }
        if (!empty($filters['q'])) { $where[] = '(email LIKE ? OR name LIKE ?)'; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; }
        $sql = 'SELECT id, name, email, role, franchisee_id, client_id FROM users';
        if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
        $sql .= ' ORDER BY id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function update(int $id, array $data): ?array {
        $pdo = db();
        $existing = self::find($id);
        if (!$existing) return null;
        $name = array_key_exists('name', $data) ? $data['name'] : $existing['name'];
        $email = array_key_exists('email', $data) ? $data['email'] : $existing['email'];
        $role = array_key_exists('role', $data) ? $data['role'] : $existing['role'];
        $franchiseeId = null;
        $clientId = null;
        if ($role === 'franchisee') { $franchiseeId = isset($data['franchisee_id']) ? (int)$data['franchisee_id'] : null; }
        if ($role === 'client') { $clientId = isset($data['client_id']) ? (int)$data['client_id'] : null; }
        $password = array_key_exists('password', $data) ? (string)$data['password'] : null;
        if ($password !== null && $password !== '') {
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, password=?, role=?, franchisee_id=?, client_id=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $email, $password, $role, $franchiseeId, $clientId, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, role=?, franchisee_id=?, client_id=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $email, $role, $franchiseeId, $clientId, $id]);
        }
        return self::find($id);
    }

    public static function delete(int $id): void {
        $pdo = db();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
}



