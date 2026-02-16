
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

-- 2. Référentiel des besoins (Le catalogue)
CREATE TABLE bnjrc_Type_besoin(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,       -- Ex: 'Riz', 'Tôle', 'Argent'
    categorie VARCHAR(50) NOT NULL,  -- Ex: 'Nature', 'Materiau', 'Argent'
    prix_unitaire DECIMAL(10,2) NOT NULL -- Le prix ne change jamais, il est ici
);

-- 3. Les Besoins exprimés par les villes
CREATE TABLE bnjrc_Besoin(
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type_id INT NOT NULL,
    quantite_demandee INT NOT NULL,
    quantite_satisfaite INT DEFAULT 0, -- Pour savoir combien il reste
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Pour l'ordre de priorité
    statut VARCHAR(20) DEFAULT 'EN_ATTENTE', -- 'EN_ATTENTE', 'PARTIEL', 'SATISFAIT'
    
    FOREIGN KEY (ville_id) REFERENCES bnjrc_Ville(id),
    FOREIGN KEY (type_id) REFERENCES bnjrc_Type_besoin(id)
);

-- 4. Les Dons reçus (Pot commun)
CREATE TABLE  bnjrc_Don(
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,        -- Ce qu'on donne (Riz, Argent...)
    quantite_donnee INT NOT NULL,
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Pour l'ordre de distribution
    donateur VARCHAR(255),           -- Optionnel, mais utile
    
    FOREIGN KEY (type_id) REFERENCES bnjrc_Type_besoin(id)
);

-- 5. Table de Liaison (Le cœur du "Dispatch")
-- C'est ici qu'on enregistre la simulation d'attribution
CREATE TABLE bnjrc_Affectation(
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite_affectee INT NOT NULL,
    date_affectation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (don_id) REFERENCES bnjrc_Don(id),
    FOREIGN KEY (besoin_id) REFERENCES bnjrc_Besoin(id)
);

-- 6. Table de configuration (frais d'achat)
CREATE TABLE bnjrc_Config(
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) NOT NULL UNIQUE,
    valeur VARCHAR(255) NOT NULL,
    description VARCHAR(255)
);

-- 7. Table des Achats (utilisation des dons en argent pour acheter des besoins en nature/matériaux)
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

INSERT INTO bnjrc_Region (nom) VALUES
('Analamanga');

INSERT INTO bnjrc_Ville (region_id, nom) VALUES
(1, 'Antananarivo'),
(1, 'Ambohidratrimo'),
(1, 'Andramasina');

INSERT INTO bnjrc_Type_besoin (nom, categorie, prix_unitaire) VALUES
('Riz', 'Nature', 1.500),
('Tôle', 'Materiaux', 10.000),
('Argent', 'Argent', 1.000);

-- Frais d'achat par défaut (10%)
INSERT INTO bnjrc_Config (cle, valeur, description) VALUES
('frais_achat_pourcent', '10', 'Pourcentage de frais sur les achats via dons en argent');
