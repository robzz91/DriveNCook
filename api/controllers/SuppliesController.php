<?php

require_once __DIR__.'/../models/Supply.php';
require_once __DIR__.'/../models/SupplyItem.php';
require_once __DIR__.'/../auth.php';

class SuppliesController {
    public static function index(): void {
        $user = auth_require();
        if (auth_is_admin($user)) {
            $items = SupplyModel::allAdmin();
        } else {
            $fid = (int)($user['franchisee_id'] ?? 0);
            $items = SupplyModel::allScoped($fid);
        }
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function show(int $id): void {
        $user = auth_require();
        $row = SupplyModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user) && (int)$row['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
            self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
        }
        self::json(['success'=>true,'data'=>$row,'error'=>null]);
    }

    public static function store(array $input): void {
        $user = auth_require();
        if (!auth_is_admin($user)) {
            // force sur son franchisee_id
            $input['franchisee_id'] = (int)($user['franchisee_id'] ?? 0);
        }
        if (empty($input['franchisee_id']) || empty($input['warehouse_id'])) {
            self::json(['success'=>false,'data'=>null,'error'=>'franchisee_id et warehouse_id requis'],422); return;
        }
        $id = SupplyModel::create($input);
        $created = SupplyModel::find($id);
        self::json(['success'=>true,'data'=>$created,'error'=>null],201);
    }

    public static function update(int $id): void {
        $user = auth_require();
        $row = SupplyModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user) && (int)$row['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
            self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
        }
        $input = self::readInput();
        $updated = SupplyModel::update($id, $input);
        self::json(['success'=>true,'data'=>$updated,'error'=>null]);
    }

    public static function destroy(int $id): void {
        $user = auth_require();
        $row = SupplyModel::find($id);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user) && (int)$row['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
            self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
        }
        SupplyModel::delete($id);
        self::json(['success'=>true,'data'=>['deleted'=>true],'error'=>null]);
    }

    public static function listItems(int $supplyId): void {
        $user = auth_require();
        $row = SupplyModel::find($supplyId);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user) && (int)$row['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
            self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
        }
        $items = SupplyItemModel::listForSupply($supplyId);
        self::json(['success'=>true,'data'=>$items,'error'=>null]);
    }

    public static function addItem(int $supplyId): void {
        $user = auth_require();
        $row = SupplyModel::find($supplyId);
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user) && (int)$row['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
            self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
        }
        $input = self::readInput();
        if (empty($input['sku']) || empty($input['label']) || !isset($input['quantity']) || !isset($input['unit_price'])) {
            self::json(['success'=>false,'data'=>null,'error'=>'sku, label, quantity, unit_price requis'],422); return;
        }
        $id = SupplyItemModel::create($supplyId, $input);
        // Vérification règle 80/20
        $pdo = db();
        $stmt = $pdo->prepare('SELECT 
              COALESCE(SUM(CASE WHEN source = "warehouse" THEN quantity*unit_price ELSE 0 END),0) AS w_amount,
              COALESCE(SUM(CASE WHEN source = "free" THEN quantity*unit_price ELSE 0 END),0) AS f_amount
            FROM supply_items WHERE supply_id = ?');
        $stmt->execute([$supplyId]);
        $a = $stmt->fetch();
        $w = (float)($a['w_amount'] ?? 0);
        $f = (float)($a['f_amount'] ?? 0);
        $total = $w + $f;
        $warehouseRatioOk = ($total <= 0) ? true : (($w / $total) >= 0.8);
        if (!$warehouseRatioOk) {
            // rollback simple: supprimer l'item ajouté et recalculer
            SupplyItemModel::delete($id);
            http_response_code(422);
            echo json_encode(['error' => 'La règle 80/20 est violée'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $items = SupplyItemModel::listForSupply($supplyId);
        $supply = SupplyModel::find($supplyId);
        self::json(['success'=>true,'data'=>['created_id'=>$id,'items'=>$items,'supply'=>$supply],'error'=>null],201);
    }

    public static function deleteItem(int $itemId): void {
        $user = auth_require();
        // vérifier portée via l'item -> supply
        $pdo = db();
        $stmt = $pdo->prepare('SELECT si.id, si.supply_id, s.franchisee_id FROM supply_items si JOIN supplies s ON s.id = si.supply_id WHERE si.id = ?');
        $stmt->execute([$itemId]);
        $row = $stmt->fetch();
        if (!$row) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        if (!auth_is_admin($user) && (int)$row['franchisee_id'] !== (int)($user['franchisee_id'] ?? 0)) {
            self::json(['success'=>false,'data'=>null,'error'=>'Accès interdit'],403); return;
        }
        SupplyItemModel::delete($itemId);
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



