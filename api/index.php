<?php
// Point d’entrée minimal, route vers contrôleurs, réponses JSON, CORS

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__.'/controllers/FranchiseesController.php';
require_once __DIR__.'/controllers/WarehousesController.php';
require_once __DIR__.'/controllers/TrucksController.php';
require_once __DIR__.'/controllers/ClientsController.php';
require_once __DIR__.'/controllers/PlatsController.php';
require_once __DIR__.'/controllers/EvenementsController.php';
require_once __DIR__.'/controllers/CommandesController.php';
require_once __DIR__.'/controllers/SuppliesController.php';
require_once __DIR__.'/controllers/SalesController.php';
require_once __DIR__.'/controllers/UsersController.php';

function respond($data, int $code=200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function read_input(): array {
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
    if (!empty($_POST)) return $_POST;
    $parsed = [];
    parse_str($raw, $parsed);
    return is_array($parsed) ? $parsed : [];
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (str_starts_with($path, '/api/')) { $path = substr($path, 5); }

if ($path === 'health') return respond(['ok' => true]);

try {
    switch (true) {
        // Franchisees
        case $path === 'franchisees' && $method === 'GET':
            FranchiseesController::index();
            break;
        case $path === 'franchisees' && $method === 'POST':
            FranchiseesController::store(read_input());
            break;
        case preg_match('#^franchisees/(\d+)$#', $path, $m) && $method === 'PUT':
            FranchiseesController::update((int)$m[1]);
            break;
        case preg_match('#^franchisees/(\d+)$#', $path, $m) && $method === 'DELETE':
            FranchiseesController::destroy((int)$m[1]);
            break;

        // Warehouses
        case $path === 'warehouses' && $method === 'GET':
            WarehousesController::index();
            break;
        case preg_match('#^warehouses/(\d+)$#', $path, $m) && $method === 'GET':
            WarehousesController::show((int)$m[1]);
            break;
        case $path === 'warehouses' && $method === 'POST':
            WarehousesController::store(read_input());
            break;
        case preg_match('#^warehouses/(\d+)$#', $path, $m) && $method === 'PUT':
            WarehousesController::update((int)$m[1]);
            break;
        case preg_match('#^warehouses/(\d+)$#', $path, $m) && $method === 'DELETE':
            WarehousesController::destroy((int)$m[1]);
            break;

        // Trucks
        case $path === 'trucks' && $method === 'GET':
            TrucksController::index();
            break;
        case $path === 'trucks' && $method === 'POST':
            TrucksController::store(read_input());
            break;
        case preg_match('#^trucks/(\d+)$#', $path, $m) && $method === 'GET':
            TrucksController::show((int)$m[1]);
            break;
        case preg_match('#^trucks/(\d+)$#', $path, $m) && $method === 'PUT':
            TrucksController::update((int)$m[1]);
            break;
        case preg_match('#^trucks/(\d+)$#', $path, $m) && $method === 'DELETE':
            TrucksController::destroy((int)$m[1]);
            break;
        case preg_match('#^trucks/(\d+)/maintenance$#', $path, $m) && $method === 'GET':
            TrucksController::listMaintenance((int)$m[1]);
            break;
        case preg_match('#^trucks/(\d+)/maintenance$#', $path, $m) && $method === 'POST':
            TrucksController::addMaintenance((int)$m[1]);
            break;

        // Clients CRUD basique
        case $path === 'clients' && $method === 'GET':
            ClientsController::index();
            break;
        case $path === 'clients' && $method === 'POST':
            ClientsController::store();
            break;
        case preg_match('#^clients/(\d+)$#', $path, $m) && $method === 'PUT':
            ClientsController::update((int)$m[1]);
            break;
        case preg_match('#^clients/(\d+)$#', $path, $m) && $method === 'DELETE':
            ClientsController::destroy((int)$m[1]);
            break;

        // Plats
        case $path === 'plats' && $method === 'GET':
            PlatsController::index();
            break;
        case $path === 'plats' && $method === 'POST':
            PlatsController::store();
            break;
        case preg_match('#^plats/(\d+)$#', $path, $m) && $method === 'DELETE':
            PlatsController::destroy((int)$m[1]);
            break;

        // Evenements
        case $path === 'evenements' && $method === 'GET':
            EvenementsController::index();
            break;
        case $path === 'evenements' && $method === 'POST':
            EvenementsController::store();
            break;
        case preg_match('#^evenements/(\d+)$#', $path, $m) && $method === 'DELETE':
            EvenementsController::destroy((int)$m[1]);
            break;

        // Commandes
        case $path === 'commandes' && $method === 'GET':
            CommandesController::index();
            break;
        case $path === 'commandes' && $method === 'POST':
            CommandesController::store();
            break;
        case preg_match('#^commandes/(\d+)$#', $path, $m) && $method === 'DELETE':
            CommandesController::destroy((int)$m[1]);
            break;

        // Supplies
        case $path === 'supplies' && $method === 'GET':
            SuppliesController::index();
            break;
        case preg_match('#^supplies/(\d+)$#', $path, $m) && $method === 'GET':
            SuppliesController::show((int)$m[1]);
            break;
        case $path === 'supplies' && $method === 'POST':
            SuppliesController::store(read_input());
            break;
        case preg_match('#^supplies/(\d+)$#', $path, $m) && $method === 'PUT':
            SuppliesController::update((int)$m[1]);
            break;
        case preg_match('#^supplies/(\d+)$#', $path, $m) && $method === 'DELETE':
            SuppliesController::destroy((int)$m[1]);
            break;
        case preg_match('#^supplies/(\d+)/items$#', $path, $m) && $method === 'GET':
            SuppliesController::listItems((int)$m[1]);
            break;
        case preg_match('#^supplies/(\d+)/items$#', $path, $m) && $method === 'POST':
            SuppliesController::addItem((int)$m[1]);
            break;
        case preg_match('#^supply_items/(\d+)$#', $path, $m) && $method === 'DELETE':
            SuppliesController::deleteItem((int)$m[1]);
            break;

        // Auth file-like endpoints
        case $path === 'login.php':
            require __DIR__.'/login.php';
            return;
        case $path === 'logout.php':
            require __DIR__.'/logout.php';
            return;

        // Sales report PDF (file-like endpoint)
        case $path === 'sales/report.php':
            require __DIR__.'/sales/report.php';
            return;

        // Sales
        case $path === 'sales' && $method === 'GET':
            SalesController::index();
            break;
        case $path === 'sales' && $method === 'POST':
            SalesController::store(read_input());
            break;

        // Users (admin-only)
        case $path === 'users' && $method === 'POST':
            UsersController::store(read_input());
            break;

        default:
            respond(['message' => 'Not Found'], 404);
    }
} catch (Throwable $e) {
    respond(['message' => 'Erreur serveur', 'detail' => $e->getMessage()], 500);
}

