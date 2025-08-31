<?php

header('Content-Type: application/json');
require_once __DIR__.'/auth.php';

$token = auth_get_bearer_token();
if (!$token) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Token manquant']); exit; }

auth_delete_token($token);
echo json_encode(['success'=>true,'data'=>['logged_out'=>true],'error'=>null]);


