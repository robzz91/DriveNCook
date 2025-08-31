<?php

require_once __DIR__.'/../models/Evenement.php';
require_once __DIR__.'/../auth.php';

class EvenementsController {
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
        self::json(EvenementModel::all());
    }

    public static function store(): void {
        auth_require();
        $input = self::input();
        if (empty($input['titre']) || empty($input['date_evenement'])) { self::json(['message' => 'titre et date_evenement requis'], 422); return; }
        $created = EvenementModel::create($input);
        self::json($created, 201);
    }

    public static function destroy(int $id): void {
        EvenementModel::delete($id);
        self::json(['deleted' => true]);
    }
}


