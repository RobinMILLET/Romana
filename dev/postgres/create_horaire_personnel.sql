/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    SEQUENCES
*/ --------------------------------

CREATE SEQUENCE sq_fermeture;
CREATE SEQUENCE sq_historique;
CREATE SEQUENCE sq_horaire;
CREATE SEQUENCE sq_reservation;
CREATE SEQUENCE sq_personnel;

/* --------------------------------
    HORAIRE
*/ --------------------------------

CREATE TABLE horaire (
    horaire_id INT PRIMARY KEY DEFAULT nextval('sq_horaire'),
    horaire_date_debut INT NOT NULL DEFAULT 1,
    horaire_date_fin INT NOT NULL DEFAULT 31,
    horaire_temps_debut TIME NOT NULL DEFAULT '00:00:00',
    horaire_temps_fin TIME NOT NULL DEFAULT '24:00:00',
    horaire_couverts INT NOT NULL
    CONSTRAINT ck_horaire_date_debut
        CHECK (horaire_date_debut BETWEEN 1 AND 31),
    CONSTRAINT ck_horaire_date_fin
        CHECK (horaire_date_fin BETWEEN 1 AND 31),
    CONSTRAINT ck_horaire_date
        CHECK (horaire_date_debut <= horaire_date_fin),
    CONSTRAINT ck_horaire_temps
        CHECK (horaire_temps_debut < horaire_temps_fin),
    CONSTRAINT ck_horaire_couverts
        CHECK (horaire_couverts > 0)
);

CREATE TABLE jour (
    jour_id INT NOT NULL,
    horaire_id INT NOT NULL,
    CONSTRAINT pk_jour
        PRIMARY KEY (jour_id, horaire_id),
    CONSTRAINT fk_jour_horaire
        FOREIGN KEY (horaire_id)
        REFERENCES horaire(horaire_id)
        ON DELETE CASCADE,
    CONSTRAINT ck_jour
        CHECK (jour_id BETWEEN 1 AND 7)
);

CREATE TABLE mois (
    mois_id INT NOT NULL,
    horaire_id INT NOT NULL,
    CONSTRAINT pk_mois
        PRIMARY KEY (mois_id, horaire_id),
    CONSTRAINT fk_mois_horaire
        FOREIGN KEY (horaire_id)
        REFERENCES horaire(horaire_id)
        ON DELETE CASCADE,
    CONSTRAINT ck_mois
        CHECK (mois_id BETWEEN 1 AND 12)
);

CREATE TABLE fermeture (
    fermeture_id INT PRIMARY KEY DEFAULT nextval('sq_fermeture'),
    fermeture_debut TIMESTAMP NOT NULL,
    fermeture_fin TIMESTAMP NOT NULL,
    fermeture_couverts INT NOT NULL,
    CONSTRAINT ck_fermeture
        CHECK (fermeture_debut < fermeture_fin),
    CONSTRAINT ck_fermeture_couverts
        CHECK (fermeture_couverts >= 0)
);

/* --------------------------------
    PERSONNEL
*/ --------------------------------

CREATE TABLE personnel (
    personnel_id INT PRIMARY KEY DEFAULT nextval('sq_personnel'),
    personnel_nom VARCHAR(64) NOT NULL,
    personnel_creation TIMESTAMP NOT NULL DEFAULT now(),
    personnel_mdp CHAR(64) NOT NULL,
    personnel_mdp_estdefaut BOOLEAN NOT NULL DEFAULT TRUE,
    personnel_mdp_changement TIMESTAMP NOT NULL DEFAULT now()
);

CREATE TABLE historique (
    historique_id INT PRIMARY KEY DEFAULT nextval('sq_historique'),
    personnel_id INT NULL,
    any_id INT NULL,
    historique_creation TIMESTAMP NOT NULL DEFAULT now(),
    historique_message VARCHAR(1024) NOT NULL,
    CONSTRAINT fk_historique_personnel
        FOREIGN KEY (personnel_id)
        REFERENCES personnel(personnel_id)
        ON DELETE SET NULL
);

CREATE TABLE typepermission (
    typepermission_id INT PRIMARY KEY,
    typepermission_clef CHAR(5) NOT NULL,
    typepermission_libelle VARCHAR(64) NOT NULL,
    typepermission_description VARCHAR(256) NOT NULL
);

CREATE TABLE permission (
    personnel_id INT NOT NULL,
    typepermission_id INT NOT NULL,
    CONSTRAINT pk_permission
        PRIMARY KEY (personnel_id, typepermission_id),
    CONSTRAINT fk_permission_personnel
        FOREIGN KEY (personnel_id)
        REFERENCES personnel(personnel_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_permission_typepermission
        FOREIGN KEY (typepermission_id)
        REFERENCES typepermission(typepermission_id)
        ON DELETE RESTRICT
);

/* --------------------------------
    RESERVATION
*/ --------------------------------

CREATE TABLE statut (
    statut_id INT PRIMARY KEY,
    statut_libelle VARCHAR(64) NOT NULL,
    statut_hex CHAR(6) NOT NULL,
    CONSTRAINT ck_statut_hex
        CHECK (statut_hex ~ '^[0-9a-f]{6}$')
);

CREATE TABLE reservation (
    reservation_id INT PRIMARY KEY DEFAULT nextval('sq_reservation'),
    statut_id INT NOT NULL,
    personnel_id INT NULL,
    reservation_num CHAR(8) NOT NULL,
    reservation_nom VARCHAR(256) NULL,
    reservation_prenom VARCHAR(256) NULL,
    reservation_telephone CHAR(10) NULL,
    reservation_personnes INT NOT NULL,
    reservation_creation TIMESTAMP NOT NULL DEFAULT now(),
    reservation_horaire TIMESTAMP NOT NULL,
    reservation_anonymiser BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_reservation_statut
        FOREIGN KEY (statut_id)
        REFERENCES statut(statut_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_reservation_personnel
        FOREIGN KEY (personnel_id)
        REFERENCES personnel(personnel_id)
        ON DELETE SET NULL,
    CONSTRAINT ck_reservation_telephone
        CHECK (reservation_telephone ~ '^[0-9]{10}$'),
    CONSTRAINT ck_reservation_personnes
        CHECK (reservation_personnes > 0),
    CONSTRAINT ck_reservation_num
        CHECK (reservation_num ~ '^[0-9A-Z]{8}$')
);

/* --------------------------------
    INDEX
*/ --------------------------------

CREATE UNIQUE INDEX ix_fermeture_id ON fermeture (fermeture_id);
CREATE INDEX ix_fermeture_debut ON fermeture (fermeture_debut);
CREATE UNIQUE INDEX ix_historique_id ON historique (historique_id);
CREATE INDEX ix_historique_personnel ON historique (personnel_id);
CREATE INDEX ix_historique_creation ON historique (historique_creation);
CREATE UNIQUE INDEX ix_horaire_id ON horaire (horaire_id);
CREATE UNIQUE INDEX ix_jour_pk ON jour (jour_id, horaire_id);
CREATE UNIQUE INDEX ix_mois_pk ON mois (mois_id, horaire_id);
CREATE UNIQUE INDEX ix_permission_pk ON permission (personnel_id, typepermission_id);
CREATE UNIQUE INDEX ix_personnel_id ON personnel (personnel_id);
CREATE UNIQUE INDEX ix_personnel_nom ON personnel (personnel_nom);
CREATE UNIQUE INDEX ix_reservation_id ON reservation (reservation_id);
CREATE INDEX ix_reservation_statut ON reservation (statut_id);
CREATE UNIQUE INDEX ix_reservation_num ON reservation (reservation_num);
CREATE INDEX ix_reservation_nom ON reservation (reservation_nom);
CREATE INDEX ix_reservation_prenom ON reservation (reservation_prenom);
CREATE INDEX ix_reservation_telephone ON reservation (reservation_telephone);
CREATE INDEX ix_reservation_horaire ON reservation (reservation_horaire);
CREATE UNIQUE INDEX ix_statut_id ON statut (statut_id);
CREATE UNIQUE INDEX ix_typepermission_id ON typepermission (typepermission_id);
CREATE UNIQUE INDEX ix_typepermission_clef ON typepermission (typepermission_clef);