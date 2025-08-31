<?php

require_once __DIR__.'/../models/Warehouse.php';
require_once __DIR__.'/../auth.php';

class WarehousesController {
    public static function index(): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $q = trim($_GET['q'] ?? '');
        $items = $q !== '' ? WarehouseModel::search($q) : WarehouseModel::all();
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function show(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $row = WarehouseModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        self::json(['success'=>true,'data'=>$row,'error'=>null]);
    }

    public static function store(array $input): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $name = trim($input['name'] ?? '');
        if ($name === '') { self::json(['success'=>false,'data'=>null,'error'=>'name requis'],422); return; }
        $id = WarehouseModel::create([
            'name' => $name,
            'address' => $input['address'] ?? null,
            'capacity' => isset($input['capacity']) ? (int)$input['capacity'] : null,
        ]);
        $created = WarehouseModel::find($id);
        self::json(['success'=>true,'data'=>$created,'error'=>null], 201);
    }

    public static function update(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $input = self::readInput();
        $updated = WarehouseModel::update($id, $input);
        if (!$updated) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        self::json(['success'=>true,'data'=>$updated,'error'=>null]);
    }

    public static function destroy(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $row = WarehouseModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        WarehouseModel::delete($id);
        self::json(['success'=>true,'data'=>['deleted'=>true],'error'=>null]);
    }

    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private static function readInput(): array {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        if (is_array($data)) return $data;
        if (!empty($_POST)) return $_POST;
        $parsed = [];
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }
}


