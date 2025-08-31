<?php

require_once __DIR__.'/../models/Truck.php';
require_once __DIR__.'/../auth.php';

class TrucksController {
    public static function index(): void {
        $user = auth_require();
        // admin voit tout, franchisé voit ses camions
        $items = auth_is_admin($user) ? TruckModel::all() : TruckModel::allScoped($user);
        self::json($items);
    }

    public static function store(array $input): void {
        if (empty($input['plate'])) {
            self::json(['message' => 'plate requis'], 422);
        }
        $id = TruckModel::create($input);
        self::json(['id' => $id] + $input, 201);
    }

    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}


