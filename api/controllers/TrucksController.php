<?php

require_once __DIR__.'/../models/Truck.php';
require_once __DIR__.'/../models/TruckMaintenance.php';
require_once __DIR__.'/../auth.php';

class TrucksController {
    public static function index(): void {
        $user = auth_require();
        // admin voit tout, franchisé voit ses camions
        $items = auth_is_admin($user) ? TruckModel::all() : TruckModel::allScoped($user);
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function store(array $input): void {
        $user = auth_require();
        // franchisé peut créer seulement pour lui-même
        if (!empty($input['franchisee_id']) && !auth_is_admin($user)) {
            if ((int)$input['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
                self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
            }
        }
        if (empty($input['plate'])) {
            self::json(['success'=>false,'data'=>null,'error'=>'plate requis'], 422);
            return;
        }
        $id = TruckModel::create($input);
        $created = TruckModel::find($id);
        self::json(['success'=>true,'data'=>$created,'error'=>null], 201);
    }

    public static function show(int $id): void {
        $user = auth_require();
        $row = TruckModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user)) {
            $fid = (int)($user['franchisee_id'] ?? 0);
            if ((int)($row['franchisee_id'] ?? 0) !== $fid) { self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return; }
        }
        self::json(['success'=>true,'data'=>$row,'error'=>null]);
    }

    public static function update(int $id): void {
        $user = auth_require();
        $row = TruckModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user)) {
            $fid = (int)($user['franchisee_id'] ?? 0);
            if ((int)($row['franchisee_id'] ?? 0) !== $fid) { self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return; }
        }
        $input = self::readInput();
        $updated = TruckModel::update($id, $input);
        self::json(['success'=>true,'data'=>$updated,'error'=>null]);
    }

    public static function destroy(int $id): void {
        $user = auth_require();
        $row = TruckModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user)) {
            $fid = (int)($user['franchisee_id'] ?? 0);
            if ((int)($row['franchisee_id'] ?? 0) !== $fid) { self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return; }
        }
        TruckModel::delete($id);
        self::json(['success'=>true,'data'=>['deleted'=>true],'error'=>null]);
    }

    public static function listMaintenance(int $truckId): void {
        $user = auth_require();
        $row = TruckModel::find($truckId);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user)) {
            $fid = (int)($user['franchisee_id'] ?? 0);
            if ((int)($row['franchisee_id'] ?? 0) !== $fid) { self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return; }
        }
        $items = TruckMaintenanceModel::listForTruck($truckId);
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function addMaintenance(int $truckId): void {
        $user = auth_require();
        $row = TruckModel::find($truckId);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user)) {
            $fid = (int)($user['franchisee_id'] ?? 0);
            if ((int)($row['franchisee_id'] ?? 0) !== $fid) { self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return; }
        }
        $input = self::readInput();
        if (empty($input['title']) || empty($input['serviced_at'])) {
            self::json(['success'=>false,'data'=>null,'error'=>'title et serviced_at requis'],422); return;
        }
        $id = TruckMaintenanceModel::create($truckId, $input);
        $items = TruckMaintenanceModel::listForTruck($truckId);
        self::json(['success'=>true,'data'=>['created_id'=>$id,'items'=>$items],'error'=>null],201);
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


