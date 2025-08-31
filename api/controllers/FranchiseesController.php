<?php

require_once __DIR__.'/../models/Franchisee.php';
require_once __DIR__.'/../auth.php';

class FranchiseesController {
    public static function index(): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;
        $data = FranchiseeModel::paginate($perPage, $offset);
        self::json([
            'success' => true,
            'data' => [
                'items' => $data['items'],
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $data['total'],
                    'total_pages' => (int)ceil($data['total'] / max(1,$perPage)),
                ],
            ],
            'error' => null,
        ]);
    }

    public static function store(array $input): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        if ($name === '' || $email === '') {
            self::json(['success' => false, 'data' => null, 'error' => 'name et email requis'], 422);
            return;
        }
        try {
            $created = FranchiseeModel::create([
                'name' => $name,
                'email' => $email,
                'phone' => $input['phone'] ?? null,
                'address' => $input['address'] ?? null,
                'joined_at' => $input['joined_at'] ?? null,
                'status' => $input['status'] ?? 'active',
            ]);
            self::json(['success' => true, 'data' => $created, 'error' => null], 201);
        } catch (Throwable $e) {
            self::json(['success' => false, 'data' => null, 'error' => 'erreur base de données'], 500);
        }
    }

    public static function update(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        $input = self::readInput();
        $updated = FranchiseeModel::update($id, $input);
        if (!$updated) { self::json(['success'=>false,'data'=>null,'error'=>'Not Found'],404); return; }
        self::json(['success'=>true,'data'=>$updated,'error'=>null]);
    }

    public static function destroy(int $id): void {
        $user = auth_require();
        auth_require_role($user, ['admin']);
        FranchiseeModel::delete($id);
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


