/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

DROP TABLE IF EXISTS constante;

CREATE TABLE constante (
    constante_clef VARCHAR(64) PRIMARY KEY,
    constante_description VARCHAR(256) NULL,
    constante_aide VARCHAR(256) NULL,
    constante_type VARCHAR(16) NOT NULL,
    constante_check VARCHAR(256) NULL,
    constante_valeur VARCHAR(256) NULL,
    constante_defaut VARCHAR(256) NOT NULL,
    CONSTRAINT ck_constante_clef
        CHECK (constante_clef = LOWER(constante_clef)),
    CONSTRAINT ck_constante_type
        CHECK (constante_type IN (
            'boolean', 'integer', 'float',
            'string', 'binary', 'json', 'csv',
            'date', 'time', 'datetime', 'interval'
        )
        OR (
            constante_type LIKE 'csv|%'
        )
    )
);

CREATE UNIQUE INDEX ix_constante_clef ON constante (constante_clef);

INSERT INTO constante VALUES

('avance_multiplicative',
'Une réservation doit-elle être créée ou modifiée plus tôt selon le nombre de personnes attendues ?',
'Si activé, l''avance nécéssaire pour réserver sera multipliée par le nombre de personnes attendues.',
'boolean', NULL, NULL, 'false'),

('mdp_critères',
'Définis les critères pour qu''un mot de passe de personnel soit valide.',
'5 entiers représentant (dans l''ordre) le nombre minimum de : caractères, majuscules, minuscules, chiffres, caractères spéciaux.',
'csv|integer', '>=0,<=32,L5', NULL, '8,1,1,1,1'),

('mdp_expiration',
'Au bout de combien de temps le mot de passe d''un personnel doit être changé.',
NULL,
'interval', '>=PT0S,<=P1Y', NULL, 'P2M'),

('sms_réservation',
'Active ou désactive l''envoi d''un SMS lors d''une réservation.',
'Permet nottament d''éviter le spam et permet au client de se souvenir de son numéro de réservation. Peut engendrer des frais.',
'boolean', NULL, NULL, 'true'),

('fuseau_horaire',
'Fuseau horaire à utiliser pour convertir les temps.',
'Ce champ est technique et doit rester valide. La liste est disponible sur php.net/manual/fr/timezones.php',
'string', NULL, NULL, 'Europe/Paris'),

('réservation_personnes_max',
'Nombre maximal de personnes en une seule réservation.',
NULL,
'integer', '>0', NULL, '20'),

('réservation_temps_max',
'Avance maximale que peut prendre un client lors de sa réservation.',
'Un client pourra créer une réservation au plus tôt X avant son horaire. Une valeur trop haute peut impacter les performances.',
'interval', '>=P2W,<=P1Y', NULL, 'P6M'),

('réservation_temps_min',
'Temps avant l''horaire pendant lequel où la réservation ne peut plus être créée ou modifiée.',
'Un client pourra créer ou modifier une réservation au plus tard X avant son horaire.',
'interval', '<=P2W', NULL, 'PT0S'),

('duree_réservation',
'Durée moyenne d''une réservation, en minutes.',
'Pendant combien de temps une réservation doit-elle être considérée comme ''en cours''.',
'integer', '>0,<1000', NULL, '75'),

('interval_réservation',
'Précision avec laquelle les clients peuvent choisir l''horaire de leur réservation, en minutes.',
'Définis la taille / le nombre de créneaux par heure. Une valeur trop basse peut impacter les performances. Doit diviser entièrement une heure.',
'integer', '>0,<=60,%60', NULL, '10');