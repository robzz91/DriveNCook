<?php

require_once __DIR__.'/../models/Client.php';
require_once __DIR__.'/../auth.php';

class ClientsController {
    private static function json($data, int $code=200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private static function input(): array {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        if (is_array($data)) return $data;
        if (!empty($_POST)) return $_POST;
        $parsed = [];
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }

    public static function index(): void {
        auth_require(); // lecture accessible à un utilisateur authentifié
        self::json(ClientModel::all());
    }

    public static function store(): void {
        auth_require(); // écriture requiert auth
        $input = self::input();
        if (empty($input['nom']) || empty($input['email'])) {
            self::json(['message' => 'nom et email requis'], 422);
            return;
        }
        try {
            $created = ClientModel::create($input);
            self::json($created, 201);
        } catch (Throwable $e) {
            self::json(['message' => 'erreur base de données', 'detail' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): void {
        $input = self::input();
        $updated = ClientModel::update($id, $input) ?? ['message' => 'Not Found'];
        self::json($updated, isset($updated['message']) ? 404 : 200);
    }

    public static function destroy(int $id): void {
        ClientModel::delete($id);
        self::json(['deleted' => true]);
    }
}


