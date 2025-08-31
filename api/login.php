<?php

header('Content-Type: application/json');
require_once __DIR__.'/config/db.php';

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) { $data = $_POST ?? []; }
$email = trim($data['email'] ?? '');
$password = (string)($data['password'] ?? '');

if ($email === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'email et password requis']);
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id, password, role, franchisee_id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Identifiants invalides']); exit; }

$hash = (string)($user['password'] ?? '');
$ok = false;
if ($hash !== '') {
    if (function_exists('password_verify')) { $ok = password_verify($password, $hash); }
    if (!$ok && hash_equals($hash, $password)) { $ok = true; }
}
if (!$ok) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Identifiants invalides']); exit; }

// Générer un token simple
$token = bin2hex(random_bytes(24));
$stmtIns = $pdo->prepare('INSERT INTO personal_access_tokens(tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at) VALUES(?, ?, ?, ?, ?, NOW(), NOW())');
$stmtIns->execute(['user', (int)$user['id'], 'api', $token, '["*"]']);

echo json_encode(['success'=>true,'data'=>['token'=>$token,'role'=>$user['role'],'franchisee_id'=>$user['franchisee_id']],'error'=>null]);


