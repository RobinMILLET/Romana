/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    SEQUENCES
*/ --------------------------------

CREATE SEQUENCE sq_conteneur;
CREATE SEQUENCE sq_evenement;
CREATE SEQUENCE sq_photo;
CREATE SEQUENCE sq_traductible;

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

CREATE TABLE element (
    element_id INT PRIMARY KEY,
    CONSTRAINT fk_element_traductible
        FOREIGN KEY (element_id)
        REFERENCES traductible(traductible_id)
        ON DELETE CASCADE
);

CREATE TABLE page (
    page_id INT PRIMARY KEY,
    page_ordre INT NOT NULL,
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
    element_id INT NOT NULL,
    categorise_prix NUMERIC(6,2) NOT NULL,
    CONSTRAINT pk_categorise
        PRIMARY KEY (categorie_id, element_id),
    CONSTRAINT fk_categorise_categorie
        FOREIGN KEY (categorie_id)
        REFERENCES categorie(categorie_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_categorise_element
        FOREIGN KEY (element_id)
        REFERENCES element(element_id)
        ON DELETE CASCADE,
    CONSTRAINT ck_categorise_prix
        CHECK (categorise_prix > 0)
);

CREATE TABLE presence (
    element_id INT NOT NULL,
    allergene_id INT NOT NULL,
    typepresence_id INT NOT NULL,
    CONSTRAINT pk_presence
        PRIMARY KEY (element_id, allergene_id),
    CONSTRAINT fk_presence_element
        FOREIGN KEY (element_id)
        REFERENCES element(element_id)
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
    evenement_suppression TIMESTAMP NULL,
    evenement_visible BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE police (
    police_id INT PRIMARY KEY,
    police_libelle VARCHAR(64) NOT NULL,
    police_texte VARCHAR(64) NOT NULL
);

CREATE TABLE conteneur (
    conteneur_id INT PRIMARY KEY DEFAULT nextval('sq_conteneur'),
    conteneur_libelle VARCHAR(64) NOT NULL,
    evenement_id INT NULL,
    page_id INT NULL,
    police_id INT NULL,
    conteneur_texte VARCHAR(2048) NULL,
    conteneur_centre BOOLEAN NULL,
    conteneur_fond CHAR(6) NULL DEFAULT '000000',
    conteneur_hex CHAR(6) NULL DEFAULT 'ffffff',
    conteneur_ligne INT NULL,
    conteneur_colonne INT NULL,
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
    CONSTRAINT ck_conteneur_fond
        CHECK (conteneur_fond ~ '^[0-9a-f]{6}$'),
    CONSTRAINT ck_conteneur_hex
        CHECK (conteneur_hex ~ '^[0-9a-f]{6}$')
);

/* --------------------------------
    PHOTO
*/ --------------------------------

CREATE TABLE photo (
    photo_id INT PRIMARY KEY DEFAULT nextval('sq_photo'),
    photo_libelle VARCHAR(64) NOT NULL,
    photo_url VARCHAR(256) NOT NULL
);

CREATE TABLE galerie (
    photo_id INT NOT NULL,
    conteneur_id INT NOT NULL,
    CONSTRAINT fk_galerie_photo
        FOREIGN KEY (photo_id)
        REFERENCES photo(photo_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_galerie_conteneur
        FOREIGN KEY (conteneur_id)
        REFERENCES conteneur(conteneur_id)
        ON DELETE CASCADE
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
    traduction_libelle VARCHAR(64) NOT NULL,
    traduction_description VARCHAR(512) NOT NULL,
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

CREATE TABLE texte (
    langue_id INT NOT NULL,
    photo_id INT NOT NULL,
    texte_description VARCHAR(512) NULL,
    CONSTRAINT fk_texte_langue
        FOREIGN KEY (langue_id)
        REFERENCES langue(langue_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_texte_photo
        FOREIGN KEY (photo_id)
        REFERENCES photo(photo_id)
        ON DELETE CASCADE
);

/* --------------------------------
    INDEX
*/ --------------------------------

CREATE UNIQUE INDEX ix_allergene_id ON allergene (allergene_id);
CREATE UNIQUE INDEX ix_categorie_id ON categorie (categorie_id);
CREATE UNIQUE INDEX ix_categorie_ordre ON categorie (categorie_ordre);
CREATE INDEX ix_categorie_parent ON categorie (categorie_idparent);
CREATE UNIQUE INDEX ix_categorise_pk ON categorise (categorie_id, element_id);
CREATE UNIQUE INDEX ix_conteneur_id ON conteneur (conteneur_id);
CREATE INDEX ix_conteneur_evenement ON conteneur (evenement_id);
CREATE INDEX ix_conteneur_page ON conteneur (page_id);
CREATE UNIQUE INDEX ix_contenu_pk ON contenu (conteneur_id, langue_id);
CREATE UNIQUE INDEX ix_element_id ON element (element_id);
CREATE UNIQUE INDEX ix_evenement_id ON evenement (evenement_id);
CREATE UNIQUE INDEX ix_evenement_libelle ON evenement (evenement_libelle);
CREATE UNIQUE INDEX ix_evenement_ordre ON evenement (evenement_ordre);
CREATE UNIQUE INDEX ix_galerie_pk ON galerie (photo_id, conteneur_id);
CREATE UNIQUE INDEX ix_langue_id ON langue (langue_id);
CREATE UNIQUE INDEX ix_langue_code ON langue (langue_code);
CREATE UNIQUE INDEX ix_langue_libelle ON langue (langue_libelle);
CREATE UNIQUE INDEX ix_page_id ON page (page_id);
CREATE UNIQUE INDEX ix_page_ordre ON page (page_ordre);
CREATE UNIQUE INDEX ix_photo_id ON photo (photo_id);
CREATE UNIQUE INDEX ix_photo_libelle ON photo (photo_libelle);
CREATE UNIQUE INDEX ix_police_id ON police (police_id);
CREATE UNIQUE INDEX ix_police_libelle ON police (police_libelle);
CREATE UNIQUE INDEX ix_presence_pk ON presence (allergene_id, element_id);
CREATE UNIQUE INDEX ix_texte_pk ON texte (photo_id, langue_id);
CREATE UNIQUE INDEX ix_traductible_id ON traductible (traductible_id);
CREATE UNIQUE INDEX ix_traduction_pk ON traduction (traductible_id, langue_id);
CREATE UNIQUE INDEX ix_typepresence_id ON typepresence (typepresence_id);
