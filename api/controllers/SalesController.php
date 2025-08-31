<?php

require_once __DIR__.'/../models/Sale.php';
require_once __DIR__.'/../auth.php';

class SalesController {
    public static function index(): void {
        $user = auth_require();
        $fid = isset($_GET['franchisee_id']) ? (int)$_GET['franchisee_id'] : null;
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $items = SaleModel::listFiltered($fid, $from, $to, auth_is_admin($user), (int)($user['franchisee_id'] ?? 0));
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function store(array $input): void {
        $user = auth_require();
        if (!auth_is_admin($user)) {
            // ignorer franchisee_id fourni
            $input['franchisee_id'] = (int)($user['franchisee_id'] ?? 0);
        }
        if (empty($input['franchisee_id']) || !isset($input['amount'])) {
            self::json(['success'=>false,'data'=>null,'error'=>'franchisee_id et amount requis'],422); return;
        }
        $id = SaleModel::create($input, auth_is_admin($user), (int)($user['franchisee_id'] ?? 0));
        $data = [
            'id' => $id,
            'franchisee_id' => (int)$input['franchisee_id'],
            'amount' => (float)$input['amount'],
            'sold_at' => $input['sold_at'] ?? date('Y-m-d H:i:s'),
        ];
        self::json(['success'=>true,'data'=>$data,'error'=>null],201);
    }

    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}



