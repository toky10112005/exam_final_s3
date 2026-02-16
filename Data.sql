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

CREATE TABLE Nature_besoin(
    id SERIAL PRIMARY KEY,
    etat VARCHAR(25) NOT NULL
);


CREATE TABLE Prix_unitaire(
    id SERIAL PRIMARY KEY,
    nature_id INTEGER NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,

    FOREIGN KEY (nature_id) REFERENCES nature_besoin(id)
);

CREATE TABLE Besoin(
    id SERIAL PRIMARY KEY,
    ville_id INTEGER NOT NULL,
    nature_id INTEGER NOT NULL,
    etat VARCHAR(25) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    quantite INTEGER NOT NULL,

    FOREIGN KEY (ville_id) REFERENCES ville(id),
    FOREIGN KEY (nature_id) REFERENCES nature_besoin(id)
);

CREATE TABLE Don(
    id SERIAL PRIMARY KEY,
    ville_id INTEGER NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,

    FOREIGN KEY (ville_id) REFERENCES ville(id)
    
);