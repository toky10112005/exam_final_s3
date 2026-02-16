
CREATE TABLE Region(
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE Ville (
    id SERIAL PRIMARY KEY,
    region_id INTEGER NOT NULL,
    nom VARCHAR(255) NOT NULL,
    FOREIGN KEY (region_id) REFERENCES Region(id)
);

-- 2. Référentiel des besoins (Le catalogue)
CREATE TABLE Type_besoin(
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,       -- Ex: 'Riz', 'Tôle', 'Argent'
    categorie VARCHAR(50) NOT NULL,  -- Ex: 'Nature', 'Materiau', 'Argent'
    prix_unitaire DECIMAL(10, 2) NOT NULL -- Le prix ne change jamais, il est ici
);

-- 3. Les Besoins exprimés par les villes
CREATE TABLE Besoin(
    id SERIAL PRIMARY KEY,
    ville_id INTEGER NOT NULL,
    type_id INTEGER NOT NULL,
    quantite_demandee INTEGER NOT NULL,
    quantite_satisfaite INTEGER DEFAULT 0, -- Pour savoir combien il reste
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Pour l'ordre de priorité
    statut VARCHAR(20) DEFAULT 'EN_ATTENTE', -- 'EN_ATTENTE', 'PARTIEL', 'SATISFAIT'
    
    FOREIGN KEY (ville_id) REFERENCES Ville(id),
    FOREIGN KEY (type_id) REFERENCES Type_besoin(id)
);

-- 4. Les Dons reçus (Pot commun)
CREATE TABLE Don(
    id SERIAL PRIMARY KEY,
    type_id INTEGER NOT NULL,        -- Ce qu'on donne (Riz, Argent...)
    quantite_donnee INTEGER NOT NULL,
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Pour l'ordre de distribution
    donateur VARCHAR(255),           -- Optionnel, mais utile
    
    FOREIGN KEY (type_id) REFERENCES Type_besoin(id)
);

-- 5. Table de Liaison (Le cœur du "Dispatch")
-- C'est ici qu'on enregistre la simulation d'attribution
CREATE TABLE Affectation(
    id SERIAL PRIMARY KEY,
    don_id INTEGER NOT NULL,
    besoin_id INTEGER NOT NULL,
    quantite_affectee INTEGER NOT NULL,
    date_affectation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (don_id) REFERENCES Don(id),
    FOREIGN KEY (besoin_id) REFERENCES Besoin(id)
);

