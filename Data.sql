
CREATE DATABASE IF NOT EXISTS bnjrc;
USE bnjrc;


CREATE TABLE bnjrc_Region(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE bnjrc_Ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    FOREIGN KEY (region_id) REFERENCES bnjrc_Region(id)
);


CREATE TABLE bnjrc_Type_besoin(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,       
    categorie VARCHAR(50) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL 
);

CREATE TABLE bnjrc_Besoin(
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type_id INT NOT NULL,
    quantite_demandee INT NOT NULL,
    quantite_satisfaite INT DEFAULT 0, 
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'EN_ATTENTE', 
    
    FOREIGN KEY (ville_id) REFERENCES bnjrc_Ville(id),
    FOREIGN KEY (type_id) REFERENCES bnjrc_Type_besoin(id)
);


CREATE TABLE  bnjrc_Don(
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,       
    quantite_donnee INT NOT NULL,
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    donateur VARCHAR(255),          
    
    FOREIGN KEY (type_id) REFERENCES bnjrc_Type_besoin(id)
);


CREATE TABLE bnjrc_Affectation(
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite_affectee INT NOT NULL,
    date_affectation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (don_id) REFERENCES bnjrc_Don(id),
    FOREIGN KEY (besoin_id) REFERENCES bnjrc_Besoin(id)
);


CREATE TABLE bnjrc_Config(
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) NOT NULL UNIQUE,
    valeur VARCHAR(255) NOT NULL,
    description VARCHAR(255)
);


CREATE TABLE bnjrc_Achat(
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    quantite_achetee INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    frais_pourcent DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL,  -- quantite * prix * (1 + frais%)
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (besoin_id) REFERENCES bnjrc_Besoin(id)
);


-- ========================================
-- INSERTION DES DONNÉES
-- ========================================

-- Régions
INSERT INTO bnjrc_Region (id, nom) VALUES
(1, 'Atsinanana'),
(2, 'Vatovavy-Fitovinany'),
(3, 'Atsimo-Atsinanana'),
(4, 'Diana'),
(5, 'Menabe');

-- Villes
INSERT INTO bnjrc_Ville (id, region_id, nom) VALUES
(1, 1, 'Toamasina'),
(2, 2, 'Mananjary'),
(3, 3, 'Farafangana'),
(4, 4, 'Nosy Be'),
(5, 5, 'Morondava');

-- Types de besoin
INSERT INTO bnjrc_Type_besoin (id, nom, categorie, prix_unitaire) VALUES
(1, 'Riz (kg)', 'nature', 3000.00),
(2, 'Eau (L)', 'nature', 1000.00),
(3, 'Tôle', 'materiel', 25000.00),
(4, 'Bâche', 'materiel', 15000.00),
(5, 'Argent', 'argent', 1.00),
(6, 'Huile (L)', 'nature', 6000.00),
(7, 'Clous (kg)', 'materiel', 8000.00),
(8, 'Haricots', 'nature', 4000.00),
(9, 'Bois', 'materiel', 10000.00),
(10, 'Groupe', 'materiel', 6750000.00);

-- Besoins
INSERT INTO bnjrc_Besoin (ville_id, type_id, quantite_demandee, quantite_satisfaite, date_demande, statut) VALUES
-- Toamasina
(1, 1, 800, 0, '2026-02-16', 'EN_ATTENTE'),
(1, 2, 1500, 0, '2026-02-15', 'EN_ATTENTE'),
(1, 3, 120, 0, '2026-02-16', 'EN_ATTENTE'),
(1, 4, 200, 0, '2026-02-15', 'EN_ATTENTE'),
(1, 5, 12000000, 0, '2026-02-16', 'EN_ATTENTE'),
-- Mananjary
(2, 1, 500, 0, '2026-02-15', 'EN_ATTENTE'),
(2, 6, 120, 0, '2026-02-16', 'EN_ATTENTE'),
(2, 3, 80, 0, '2026-02-15', 'EN_ATTENTE'),
(2, 7, 60, 0, '2026-02-16', 'EN_ATTENTE'),
(2, 5, 6000000, 0, '2026-02-15', 'EN_ATTENTE'),
-- Farafangana
(3, 1, 600, 0, '2026-02-16', 'EN_ATTENTE'),
(3, 2, 1000, 0, '2026-02-15', 'EN_ATTENTE'),
(3, 4, 150, 0, '2026-02-16', 'EN_ATTENTE'),
(3, 9, 100, 0, '2026-02-15', 'EN_ATTENTE'),
(3, 5, 8000000, 0, '2026-02-16', 'EN_ATTENTE'),
-- Nosy Be
(4, 1, 300, 0, '2026-02-15', 'EN_ATTENTE'),
(4, 8, 200, 0, '2026-02-16', 'EN_ATTENTE'),
(4, 3, 40, 0, '2026-02-15', 'EN_ATTENTE'),
(4, 7, 30, 0, '2026-02-16', 'EN_ATTENTE'),
(4, 5, 4000000, 0, '2026-02-15', 'EN_ATTENTE'),
-- Morondava
(5, 1, 700, 0, '2026-02-16', 'EN_ATTENTE'),
(5, 2, 1200, 0, '2026-02-15', 'EN_ATTENTE'),
(5, 4, 180, 0, '2026-02-16', 'EN_ATTENTE'),
(5, 9, 150, 0, '2026-02-15', 'EN_ATTENTE'),
(5, 5, 10000000, 0, '2026-02-16', 'EN_ATTENTE'),
-- Toamasina (Groupe)
(1, 10, 3, 0, '2026-02-15', 'EN_ATTENTE');
