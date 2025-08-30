<?php
// API PHP minimale, SQLite file-based, endpoints essentiels pour le front

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

function input_array(): array {
  $raw = file_get_contents('php://input') ?: '';
  $data = json_decode($raw, true);
  if (is_array($data)) return $data;
  if (!empty($_POST)) return $_POST;
  // Supporter aussi application/x-www-form-urlencoded envoyé en brut
  $parsed = [];
  parse_str($raw, $parsed);
  return is_array($parsed) ? $parsed : [];
}

// Connexion DB différée après la route /health

function json($data, $code=200) {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function dberror(Throwable $e, int $code = 500) {
  json(['message' => 'erreur base de données', 'detail' => $e->getMessage()], $code);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normaliser: enlever préfixe /api
if (str_starts_with($path, '/api/')) { $path = substr($path, 5); }

// Health
if ($path === 'health') json(['ok' => true]);

// Connexion MySQL (valeurs par défaut simples; sur Windows Docker, host.docker.internal pointe vers l'hôte)
$dbHost = getenv('DB_HOST') ?: 'host.docker.internal';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_DATABASE') ?: 'drivncook_m2';
$dbUser = getenv('DB_USERNAME') ?: 'drivnuser';
$dbPass = getenv('DB_PASSWORD') ?: 'drivnpass';

try {
  $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  json(['message' => 'Connexion MySQL échouée', 'detail' => $e->getMessage()], 500);
}

// Init tables (désactivable)
if ((getenv('DB_INIT') ?: '1') !== '0') {
  $pdo->exec('CREATE TABLE IF NOT EXISTS clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
  $pdo->exec('CREATE TABLE IF NOT EXISTS plats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT NULL,
    prix DECIMAL(10,2) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
  $pdo->exec('CREATE TABLE IF NOT EXISTS commandes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT "brouillon",
    paye TINYINT(1) NOT NULL DEFAULT 0,
    total_ht DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_ttc DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
  $pdo->exec('CREATE TABLE IF NOT EXISTS commande_lignes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commande_id INT UNSIGNED NOT NULL,
    plat_id INT UNSIGNED NOT NULL,
    quantite INT UNSIGNED NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    total_ligne DECIMAL(10,2) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
  $pdo->exec('CREATE TABLE IF NOT EXISTS evenements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NULL,
    date_evenement DATE NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
}

// Clients
if ($path === 'clients' && $method === 'GET') {
  try {
    $rows = $pdo->query('SELECT id, nom, email FROM clients ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    json($rows);
  } catch (Throwable $e) { dberror($e); }
}
if ($path === 'clients' && $method === 'POST') {
  $input = input_array();
  $nom = trim($input['nom'] ?? '');
  $email = trim($input['email'] ?? '');
  if ($nom === '' || $email === '') json(['message' => 'nom et email requis'], 422);
  $now = date('Y-m-d H:i:s');
  $stmt = $pdo->prepare('INSERT INTO clients(nom,email,created_at,updated_at) VALUES(?,?,?,?)');
  try {
    $stmt->execute([$nom, $email, $now, $now]);
  } catch (PDOException $e) {
    if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'UNIQUE')) {
      json(['message' => 'email existe déjà'], 409);
    }
    dberror($e);
  }
  $id = (int)$pdo->lastInsertId();
  json(['id'=>$id,'nom'=>$nom,'email'=>$email], 201);
}
if (preg_match('#^clients/(\d+)$#', $path, $m)) {
  $id = (int)$m[1];
  if ($method === 'DELETE') {
    $pdo->prepare('DELETE FROM clients WHERE id=?')->execute([$id]);
    json(['deleted'=>true]);
  }
}

// Plats
if ($path === 'plats' && $method === 'GET') {
  $rows = $pdo->query('SELECT id, nom, description, prix FROM plats ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
  json($rows);
}
if ($path === 'plats' && $method === 'POST') {
  $input = input_array();
  $nom = trim($input['nom'] ?? '');
  $prix = (float)($input['prix'] ?? 0);
  $description = $input['description'] ?? null;
  if ($nom === '') json(['message' => 'nom requis'], 422);
  $stmt = $pdo->prepare('INSERT INTO plats(nom,description,prix,created_at,updated_at) VALUES(?,?,?,?,?)');
  $now = date('Y-m-d H:i:s');
  $stmt->execute([$nom, $description, $prix, $now, $now]);
  $id = (int)$pdo->lastInsertId();
  json(['id'=>$id,'nom'=>$nom,'description'=>$description,'prix'=>$prix], 201);
}

// Commandes (très simple: liste)
if ($path === 'commandes' && $method === 'GET') {
  $rows = $pdo->query('SELECT id, client_id, status, paye, total_ht, total_ttc FROM commandes ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
  json($rows);
}
if ($path === 'commandes' && $method === 'POST') {
  $input = input_array();
  $clientId = (int)($input['client_id'] ?? 0);
  if ($clientId <= 0) json(['message'=>'client_id requis'], 422);
  $now = date('Y-m-d H:i:s');
  $pdo->prepare('INSERT INTO commandes(client_id,status,paye,total_ht,total_ttc,created_at,updated_at) VALUES(?,?,?,?,?,?,?)')
      ->execute([$clientId,'brouillon',0,0,0,$now,$now]);
  $id = (int)$pdo->lastInsertId();
  json(['id'=>$id,'client_id'=>$clientId,'status'=>'brouillon','paye'=>0,'total_ht'=>0,'total_ttc'=>0], 201);
}

// Evenements
if ($path === 'evenements' && $method === 'GET') {
  $rows = $pdo->query('SELECT id, titre, description, date_evenement FROM evenements ORDER BY date_evenement DESC')->fetchAll(PDO::FETCH_ASSOC);
  json($rows);
}
if ($path === 'evenements' && $method === 'POST') {
  $input = input_array();
  $titre = trim($input['titre'] ?? '');
  $date = trim($input['date_evenement'] ?? '');
  if ($titre === '' || $date === '') json(['message'=>'titre et date_evenement requis'], 422);
  $now = date('Y-m-d H:i:s');
  $stmt = $pdo->prepare('INSERT INTO evenements(titre,description,date_evenement,created_at,updated_at) VALUES(?,?,?,?,?)');
  $stmt->execute([$titre, $input['description'] ?? null, $date, $now, $now]);
  $id = (int)$pdo->lastInsertId();
  json(['id'=>$id,'titre'=>$titre,'description'=>$input['description']??null,'date_evenement'=>$date], 201);
}

// Par défaut
json(['message' => 'Not Found'], 404);


