<?php

require_once __DIR__.'/../models/Warehouse.php';
require_once __DIR__.'/../auth.php';

class WarehousesController {
    public static function index(): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $items = WarehouseModel::all();
        self::json($items);
    }

    public static function store(array $input): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        if (empty($input['name'])) {
            self::json(['message' => 'name requis'], 422);
            return;
        }
        $id = WarehouseModel::create($input);
        self::json(['id' => $id] + $input, 201);
    }

    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}


