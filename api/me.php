<?php

header('Content-Type: application/json');
require_once __DIR__.'/auth.php';

$user = auth_require();
echo json_encode([
    'success' => true,
    'data' => [
        'id' => (int)($user['id'] ?? 0),
        'name' => $user['name'] ?? null,
        'email' => $user['email'] ?? null,
        'role' => $user['role'] ?? null,
        'franchisee_id' => $user['franchisee_id'] ?? null,
        'client_id' => $user['client_id'] ?? null,
    ],
    'error' => null,
]);


