-- Insertion des clients
INSERT INTO clients (nom, email) VALUES
('Alice Martin', 'alice@example.com'),
('Bob Dupont', 'bob@example.com'),
('Chloé Bernard', 'chloe@example.com');

-- Insertion des livreurs
INSERT INTO livreurs (nom, email, telephone) VALUES
('Jean Livreur', 'jean@delivery.com', '0610203040'),
('Sophie Express', 'sophie@delivery.com', '0620304050');

-- Insertion des plats
INSERT INTO plats (nom, description, prix, actif, image) VALUES
('Pizza Margherita', 'Pizza classique avec tomate et mozzarella', 9.90, 1, 'pizza.jpg'),
('Burger Maison', 'Burger avec steak, salade, tomate, cheddar', 12.50, 1, 'burger.jpg'),
('Sushi Mix', 'Assortiment de sushis variés', 14.00, 1, 'sushi.jpg');

-- Insertion des commandes
INSERT INTO commandes (client_id, status, paye, total_ht, total_ttc) VALUES
(1, 'confirmee', 1, 22.40, 24.64),
(2, 'brouillon', 0, 0.00, 0.00);

-- Insertion des lignes de commande
INSERT INTO commande_lignes (commande_id, plat_id, quantite, prix_unitaire, total_ligne) VALUES
(1, 1, 2, 9.90, 19.80),
(1, 2, 1, 12.50, 12.50);

-- Insertion des paiements
INSERT INTO paiements (commande_id, montant, statut, paid_at, meta) VALUES
(1, 24.64, 'paye', NOW(), JSON_OBJECT('methode', 'stripe', 'transaction_id', 'TX12345'));

-- Insertion des cartes de fidélité
INSERT INTO cartes_fidelite (client_id, points, statut) VALUES
(1, 20, 'active'),
(2, 0, 'active');

-- Insertion de l'historique des points
INSERT INTO historique_points (client_id, variation, raison) VALUES
(1, +20, 'Première commande validée'),
(1, -10, 'Utilisation des points');

-- Insertion des événements
INSERT INTO evenements (titre, description, date_evenement) VALUES
('Dégustation Printemps', 'Venez goûter nos nouveaux plats de saison', '2025-09-20'),
('Jeux Concours', 'Tentez de gagner des points fidélité', '2025-10-05');

-- Insertion des avis clients
INSERT INTO avis_clients (client_id, plat_id, note, commentaire) VALUES
(1, 1, 5, 'Délicieuse pizza !'),
(2, 2, 4, 'Bon burger, un peu trop salé.');
