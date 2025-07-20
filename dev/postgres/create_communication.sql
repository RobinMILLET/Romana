/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    SEQUENCES
*/ --------------------------------

CREATE SEQUENCE sq_conteneur START 101;
CREATE SEQUENCE sq_evenement;
CREATE SEQUENCE sq_photo;
CREATE SEQUENCE sq_traductible START 201;

/* --------------------------------
    TRADUCTIBLE
*/ --------------------------------

CREATE TABLE traductible (
    traductible_id INT PRIMARY KEY DEFAULT nextval('sq_traductible')
);

CREATE TABLE allergene (
    allergene_id INT PRIMARY KEY,
    CONSTRAINT fk_allergene_traductible
        FOREIGN KEY (allergene_id)
        REFERENCES traductible(traductible_id)
        ON DELETE CASCADE
);

CREATE TABLE categorie (
    categorie_id INT PRIMARY KEY,
    categorie_idparent INT NULL,
    categorie_ordre INT NOT NULL,
    CONSTRAINT fk_categorie_traductible
        FOREIGN KEY (categorie_id)
        REFERENCES traductible(traductible_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_categorie_parent
        FOREIGN KEY (categorie_idparent)
        REFERENCES categorie(categorie_id)
        ON DELETE SET NULL
);

CREATE TABLE produit (
    produit_id INT PRIMARY KEY,
    CONSTRAINT fk_produit_traductible
        FOREIGN KEY (produit_id)
        REFERENCES traductible(traductible_id)
        ON DELETE CASCADE
);

CREATE TABLE page (
    page_id INT PRIMARY KEY,
    page_ordre INT NOT NULL,
    page_route VARCHAR(32) NOT NULL,
    CONSTRAINT fk_page_traductible
        FOREIGN KEY (page_id)
        REFERENCES traductible(traductible_id)
        ON DELETE RESTRICT
);

CREATE TABLE typepresence (
    typepresence_id INT PRIMARY KEY,
    typepresence_hex CHAR(6) NOT NULL,
    CONSTRAINT fk_typepresence_traductible
        FOREIGN KEY (typepresence_id)
        REFERENCES traductible(traductible_id)
        ON DELETE CASCADE,
    CONSTRAINT ck_typepresence_hex
        CHECK (typepresence_hex ~ '^[0-9a-f]{6}$')
);

/* --------------------------------
    ASSOCIATIONS
*/ --------------------------------

CREATE TABLE categorise (
    categorie_id INT NOT NULL,
    produit_id INT NOT NULL,
    categorise_prix NUMERIC(6,2) NOT NULL,
    categorise_ordre INT NOT NULL,
    CONSTRAINT pk_categorise
        PRIMARY KEY (categorie_id, produit_id),
    CONSTRAINT fk_categorise_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES categorie(categorie_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_categorise_produit
        FOREIGN KEY (produit_id)
        REFERENCES produit(produit_id)
        ON DELETE CASCADE,
    CONSTRAINT ck_categorise_prix
        CHECK (categorise_prix > 0)
);

CREATE TABLE presence (
    produit_id INT NOT NULL,
    allergene_id INT NOT NULL,
    typepresence_id INT NOT NULL,
    CONSTRAINT pk_presence
        PRIMARY KEY (produit_id, allergene_id),
    CONSTRAINT fk_presence_produit
        FOREIGN KEY (produit_id)
        REFERENCES produit(produit_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_presence_allergene
        FOREIGN KEY (allergene_id)
        REFERENCES allergene(allergene_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_presence_typepresence
        FOREIGN KEY (typepresence_id)
        REFERENCES typepresence(typepresence_id)
        ON DELETE RESTRICT
);

/* --------------------------------
    CONTENEUR
*/ --------------------------------

CREATE TABLE evenement (
    evenement_id INT PRIMARY KEY DEFAULT nextval('sq_evenement'),
    evenement_libelle VARCHAR(64) NOT NULL,
    evenement_ordre INT NOT NULL,
    evenement_debut TIMESTAMP NULL,
    evenement_fin TIMESTAMP NULL,
    evenement_visible BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT ck_evenement_dates
        CHECK (evenement_debut < evenement_fin)
);

CREATE TABLE police (
    police_id INT PRIMARY KEY,
    police_libelle VARCHAR(64) NOT NULL,
    police_texte VARCHAR(64) NOT NULL
);

CREATE TABLE photo (
    photo_id INT PRIMARY KEY DEFAULT nextval('sq_photo'),
    photo_libelle VARCHAR(64) NOT NULL,
    photo_url VARCHAR(256) NOT NULL
);

CREATE TABLE conteneur (
    conteneur_id INT PRIMARY KEY DEFAULT nextval('sq_conteneur'),
    conteneur_libelle VARCHAR(64) NOT NULL,
    evenement_id INT NULL,
    page_id INT NULL,
    photo_id INT NULL,
    police_id INT NULL,
    conteneur_texte VARCHAR(2048) NULL,
    conteneur_ligne INT NOT NULL,
    conteneur_colonne INT NOT NULL,
    conteneur_aligne NUMERIC(1,0) NOT NULL DEFAULT 5,
    conteneur_bordure CHAR(8) NULL,
    conteneur_couleur CHAR(8) NOT NULL DEFAULT 'ffffffff',
    conteneur_fond CHAR(8) NULL,
    conteneur_largeur VARCHAR(32) NULL,
    conteneur_marges VARCHAR(32) NULL,
    conteneur_ombre CHAR(8) NULL,
    conteneur_rayon VARCHAR(32) NOT NULL DEFAULT '0px',
    conteneur_visible BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_conteneur_evenement
        FOREIGN KEY (evenement_id)
        REFERENCES evenement(evenement_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_conteneur_page
        FOREIGN KEY (page_id)
        REFERENCES page(page_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_conteneur_police
        FOREIGN KEY (police_id)
        REFERENCES police(police_id)
        ON DELETE SET NULL,
    CONSTRAINT fk_conteneur_photo
        FOREIGN KEY (photo_id)
        REFERENCES photo(photo_id)
        ON DELETE SET NULL,
    CONSTRAINT ck_conteneur_aligne
        CHECK (conteneur_aligne BETWEEN 1 AND 9),
    CONSTRAINT ck_conteneur_bordure
        CHECK (conteneur_bordure ~ '^[0-9a-f]{8}$'),
    CONSTRAINT ck_conteneur_couleur
        CHECK (conteneur_couleur ~ '^[0-9a-f]{8}$'),
    CONSTRAINT ck_conteneur_fond
        CHECK (conteneur_fond ~ '^[0-9a-f]{8}$'),
    CONSTRAINT ck_conteneur_ombre
        CHECK (conteneur_ombre ~ '^[0-9a-f]{8}$')
);

/* --------------------------------
    LANGUE
*/ --------------------------------

CREATE TABLE langue (
    langue_id INT PRIMARY KEY,
    langue_code CHAR(2) NOT NULL,
    langue_libelle VARCHAR(64) NOT NULL,
    langue_affichage VARCHAR(64) NOT NULL
);

CREATE TABLE traduction (
    langue_id INT NOT NULL,
    traductible_id INT NOT NULL,
    traduction_libelle VARCHAR(512) NOT NULL,
    traduction_description VARCHAR(1024) NULL,
    CONSTRAINT fk_traduction_langue
        FOREIGN KEY (langue_id)
        REFERENCES langue(langue_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_traduction_traductible
        FOREIGN KEY (traductible_id)
        REFERENCES traductible(traductible_id)
        ON DELETE CASCADE
);

CREATE TABLE contenu (
    langue_id INT NOT NULL,
    conteneur_id INT NOT NULL,
    contenu_texte VARCHAR(4096) NOT NULL,
    CONSTRAINT fk_contenu_langue
        FOREIGN KEY (langue_id)
        REFERENCES langue(langue_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_contenu_conteneur
        FOREIGN KEY (conteneur_id)
        REFERENCES conteneur(conteneur_id)
        ON DELETE CASCADE
);

/* --------------------------------
    INDEX
*/ --------------------------------

CREATE UNIQUE INDEX ix_allergene_id ON allergene (allergene_id);
CREATE UNIQUE INDEX ix_categorie_id ON categorie (categorie_id);
CREATE UNIQUE INDEX ix_categorie_ordre ON categorie (categorie_idparent, categorie_ordre);
CREATE INDEX ix_categorie_parent ON categorie (categorie_idparent);
CREATE UNIQUE INDEX ix_categorise_pk ON categorise (categorie_id, produit_id);
CREATE UNIQUE INDEX ix_categorise_ordre ON categorise (categorie_id, categorise_ordre);
CREATE UNIQUE INDEX ix_conteneur_id ON conteneur (conteneur_id);
CREATE UNIQUE INDEX ix_conteneur_libelle ON conteneur (conteneur_libelle);
CREATE INDEX ix_conteneur_evenement ON conteneur (evenement_id);
CREATE INDEX ix_conteneur_page ON conteneur (page_id);
CREATE INDEX ix_conteneur_photo ON conteneur (photo_id);
CREATE INDEX ix_conteneur_police ON conteneur (police_id);
CREATE UNIQUE INDEX ix_conteneur_position ON conteneur
    (page_id, evenement_id, conteneur_ligne, conteneur_colonne);
CREATE INDEX ix_conteneur_visible ON conteneur (conteneur_visible);
CREATE UNIQUE INDEX ix_contenu_pk ON contenu (conteneur_id, langue_id);
CREATE UNIQUE INDEX ix_evenement_id ON evenement (evenement_id);
CREATE UNIQUE INDEX ix_evenement_libelle ON evenement (evenement_libelle);
CREATE UNIQUE INDEX ix_evenement_ordre ON evenement (evenement_ordre);
CREATE UNIQUE INDEX ix_evenement_visible ON evenement (evenement_visible);
CREATE UNIQUE INDEX ix_langue_id ON langue (langue_id);
CREATE UNIQUE INDEX ix_langue_code ON langue (langue_code);
CREATE UNIQUE INDEX ix_langue_libelle ON langue (langue_libelle);
CREATE UNIQUE INDEX ix_page_id ON page (page_id);
CREATE UNIQUE INDEX ix_page_ordre ON page (page_ordre);
CREATE UNIQUE INDEX ix_page_route ON page (page_route);
CREATE UNIQUE INDEX ix_photo_id ON photo (photo_id);
CREATE UNIQUE INDEX ix_photo_libelle ON photo (photo_libelle);
CREATE UNIQUE INDEX ix_police_id ON police (police_id);
CREATE UNIQUE INDEX ix_police_libelle ON police (police_libelle);
CREATE UNIQUE INDEX ix_presence_pk ON presence (allergene_id, produit_id);
CREATE UNIQUE INDEX ix_produit_id ON produit (produit_id);
CREATE UNIQUE INDEX ix_traductible_id ON traductible (traductible_id);
CREATE UNIQUE INDEX ix_traduction_pk ON traduction (traductible_id, langue_id);
CREATE UNIQUE INDEX ix_typepresence_id ON typepresence (typepresence_id);