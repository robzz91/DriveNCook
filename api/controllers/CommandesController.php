<?php

require_once __DIR__.'/../models/Commande.php';

class CommandesController {
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
        self::json(CommandeModel::all());
    }

    public static function store(): void {
        $input = self::input();
        if (empty($input['client_id'])) { self::json(['message' => 'client_id requis'], 422); return; }
        $created = CommandeModel::create($input);
        self::json($created, 201);
    }

    public static function destroy(int $id): void {
        CommandeModel::delete($id);
        self::json(['deleted' => true]);
    }
}


