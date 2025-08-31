<?php

header('Content-Type: application/pdf');
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../models/Sale.php';
require_once __DIR__.'/../lib/SimplePDF.php';
require_once __DIR__.'/../config/db.php';

$user = auth_require();

$fid = isset($_GET['franchisee_id']) ? (int)$_GET['franchisee_id'] : null;
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

// Si non admin, forcer franchisee_id à soi
if (!auth_is_admin($user)) {
    $fid = (int)($user['franchisee_id'] ?? 0);
}

$items = SaleModel::listFiltered($fid, $from, $to, auth_is_admin($user), (int)($user['franchisee_id'] ?? 0));

// Récupérer nom franchisé
$franchiseeName = '';
if (!empty($fid)) {
    $stmt = db()->prepare('SELECT name FROM franchisees WHERE id = ?');
    $stmt->execute([$fid]);
    $row = $stmt->fetch();
    $franchiseeName = $row ? (string)$row['name'] : '';
}

$total = 0.0;
foreach ($items as $it) { $total += (float)$it['amount']; }

$p = new SimplePDF();
$p->addTitle("Drive'N'Cook", 32);
$p->addBlankLine();
$p->addLine('Rapport des ventes', 16);
$p->addBlankLine();
$p->addLine('Franchisé: '.($franchiseeName !== '' ? $franchiseeName : 'Tous'));
$p->addLine('Période: '.(($from ?? '...').' -> '.($to ?? '...')));
$p->addLine('Total: '.number_format($total, 2, ',', ' ').' EUR');
$p->addLine('');
$p->addLine('Ventes:');
foreach ($items as $it) {
    $p->addLine(sprintf('- %s | %.2f EUR', (string)$it['sold_at'], (float)$it['amount']));
}

$pdf = $p->output();

// Proposer un nom de fichier au client (le navigateur/outil choisit l'emplacement)
$rawName = $franchiseeName !== '' ? $franchiseeName : 'Tous';
$safeName = preg_replace('/[^A-Za-z0-9_\-]/','_', str_replace(' ', '_', $rawName));
$today = date('Y-m-d');
$filename = 'Rapport_'.$safeName.'_'.$today.'.pdf';
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: no-store');
header('Pragma: no-cache');

echo $pdf;


