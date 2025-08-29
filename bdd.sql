-- ========================================
-- ARCHITECTURE BASE DE DONNEES DRIVNCOOK
-- ========================================

DROP DATABASE IF EXISTS drivncook_m2;
CREATE DATABASE drivncook_m2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE drivncook_m2;

SET FOREIGN_KEY_CHECKS=0;

-- ======================
-- MISSION 2 : Services Clients
-- ======================

CREATE TABLE clients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE plats (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL,
  description TEXT,
  prix DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE commandes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_id INT UNSIGNED NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'brouillon',
  paye TINYINT(1) NOT NULL DEFAULT 0,
  total_ht DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_ttc DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE commande_lignes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  commande_id INT UNSIGNED NOT NULL,
  plat_id INT UNSIGNED NOT NULL,
  quantite INT UNSIGNED NOT NULL DEFAULT 1,
  prix_unitaire DECIMAL(10,2) NOT NULL,
  total_ligne DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
  FOREIGN KEY (plat_id) REFERENCES plats(id) ON DELETE RESTRICT
);

CREATE TABLE paiements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  commande_id INT UNSIGNED NOT NULL,
  montant DECIMAL(10,2) NOT NULL,
  statut VARCHAR(50) NOT NULL,
  paid_at DATETIME DEFAULT NULL,
  meta JSON DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
);

CREATE TABLE evenements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  description TEXT,
  date_evenement DATE NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

-- ======================
-- MISSION 1 : Services Franchisés
-- ======================

-- Franchisés
CREATE TABLE franchisees (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(50),
  joined_at DATE,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

-- Entrepôts
CREATE TABLE warehouses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address VARCHAR(255),
  capacity INT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

-- Camions
CREATE TABLE trucks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plate VARCHAR(30) NOT NULL UNIQUE,
  franchisee_id BIGINT UNSIGNED NULL,
  status ENUM('active','maintenance','broken') NOT NULL DEFAULT 'active',
  last_service_at DATE NULL,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (franchisee_id) REFERENCES franchisees(id) ON DELETE SET NULL
);

-- Maintenances camions
CREATE TABLE truck_maintenances (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  truck_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  cost DECIMAL(10,2) NOT NULL DEFAULT 0,
  serviced_at DATE NOT NULL,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (truck_id) REFERENCES trucks(id) ON DELETE CASCADE
);

-- Approvisionnements
CREATE TABLE supplies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  franchisee_id BIGINT UNSIGNED NOT NULL,
  warehouse_id BIGINT UNSIGNED NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (franchisee_id) REFERENCES franchisees(id) ON DELETE CASCADE,
  FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT
);

-- Lignes d’approvisionnement
CREATE TABLE supply_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  supply_id BIGINT UNSIGNED NOT NULL,
  sku VARCHAR(100) NOT NULL,
  label VARCHAR(255) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  source ENUM('warehouse','free') NOT NULL DEFAULT 'warehouse',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (supply_id) REFERENCES supplies(id) ON DELETE CASCADE
);

-- Ventes
CREATE TABLE sales (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  franchisee_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  sold_at DATETIME NOT NULL,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (franchisee_id) REFERENCES franchisees(id) ON DELETE CASCADE
);

-- ======================
-- TABLES TECHNIQUES LARAVEL
-- ======================

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','franchisee') NOT NULL DEFAULT 'franchisee',
  franchisee_id BIGINT UNSIGNED NULL,
  remember_token VARCHAR(100) DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (franchisee_id) REFERENCES franchisees(id) ON DELETE SET NULL
);

CREATE TABLE migrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(255) NOT NULL,
  batch INT NOT NULL
);

CREATE TABLE personal_access_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tokenable_type VARCHAR(255) NOT NULL,
  tokenable_id BIGINT UNSIGNED NOT NULL,
  name TEXT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  abilities TEXT,
  last_used_at TIMESTAMP NULL DEFAULT NULL,
  expires_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE password_reset_tokens (
  email VARCHAR(255) NOT NULL PRIMARY KEY,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL
);

SET FOREIGN_KEY_CHECKS=1;

-- ======================
-- Vues
-- ======================

CREATE OR REPLACE VIEW v_sales_summary AS
SELECT
  franchisee_id,
  DATE(sold_at) AS d,
  COUNT(*) AS sales_count,
  SUM(amount) AS sales_total,
  AVG(amount) AS sales_avg
FROM sales
GROUP BY franchisee_id, DATE(sold_at);

CREATE OR REPLACE VIEW v_supplies_summary AS
SELECT
    s.id                AS supply_id,
    s.franchisee_id,
    s.warehouse_id,
    DATE(s.created_at)  AS supply_date,
    s.total_amount,
    SUM(CASE WHEN si.source = 'warehouse' THEN (si.quantity * si.unit_price) ELSE 0 END) AS warehouse_amount,
    SUM(CASE WHEN si.source = 'free' THEN (si.quantity * si.unit_price) ELSE 0 END)      AS free_amount,
    ROUND(
        (SUM(CASE WHEN si.source = 'warehouse' THEN (si.quantity * si.unit_price) ELSE 0 END) / NULLIF(s.total_amount,0)) * 100,
        2
    ) AS warehouse_pct,
    ROUND(
        (SUM(CASE WHEN si.source = 'free' THEN (si.quantity * si.unit_price) ELSE 0 END) / NULLIF(s.total_amount,0)) * 100,
        2
    ) AS free_pct
FROM supplies s
LEFT JOIN supply_items si ON si.supply_id = s.id
GROUP BY s.id, s.franchisee_id, s.warehouse_id, s.created_at, s.total_amount;

