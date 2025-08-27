-- Reset propre (optionnel si tu lances plusieurs fois ce script)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE commande_lignes;
TRUNCATE TABLE commandes;
TRUNCATE TABLE avis_clients;
TRUNCATE TABLE historique_points;
TRUNCATE TABLE cartes_fidelite;
TRUNCATE TABLE paiements;
TRUNCATE TABLE plats;
TRUNCATE TABLE clients;
SET FOREIGN_KEY_CHECKS = 1;

-- Clients
INSERT INTO clients (nom, email) VALUES
('Alice', 'alice@example.com'),
('Bob', 'bob@example.com'),
('Chloe', 'chloe@example.com');

-- Plats
INSERT INTO plats (nom, description, prix, actif, image) VALUES
('Pizza', 'Pizza 4 fromages', 12.50, 1, NULL),
('Burger', 'Burger classique', 9.90, 1, NULL),
('Sushi', 'Assortiment 12 pièces', 15.00, 1, NULL);

-- Commandes (totaux simples pour l’exemple)
INSERT INTO commandes (client_id, status, paye, total_ht, total_ttc, created_at, updated_at) VALUES
(1, 'validee', 1, 25.00, 25.00, NOW(), NOW()),  -- correspondra à 2x Pizza
(2, 'validee', 1, 9.90, 9.90, NOW(), NOW()),    -- 1x Burger
(3, 'validee', 0, 75.00, 75.00, NOW(), NOW());  -- 5x Sushi

-- Lignes des commandes
INSERT INTO commande_lignes (commande_id, plat_id, quantite, prix_unitaire, total_ligne, created_at, updated_at) VALUES
(1, 1, 2, 12.50, 25.00, NOW(), NOW()),  -- 2x Pizza
(2, 2, 1, 9.90,  9.90, NOW(), NOW()),   -- 1x Burger
(3, 3, 5, 15.00, 75.00, NOW(), NOW());  -- 5x Sushi
