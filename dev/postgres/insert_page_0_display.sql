/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM conteneur where page_id = 0;

/* --------------------------------
    DISPLAY
*/ --------------------------------

INSERT INTO conteneur (page_id, conteneur_id, conteneur_libelle, conteneur_ligne, conteneur_colonne,
photo_id, police_id, conteneur_aligne, conteneur_largeur, conteneur_marges, conteneur_rayon, conteneur_visible,
conteneur_couleur, conteneur_fond, conteneur_bordure, conteneur_ombre, 
conteneur_texte) VALUES

(0, 1, 'display1', 1, 1,
NULL, 1, 5, NULL, '1vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'<form#afficher1_>'),

(0, 2, 'display2', 2, 1,
NULL, 1, 2, 'fit-content', '2.5vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'<form#afficher2_>'),

(0, 3, 'display3', 2, 2,
NULL, 1, 2, 'fit-content', '2.5vw', '0px', TRUE,
'ffffffff', NULL, '505050ff', NULL,
'<form#afficher3_>'),

(0, 4, 'display4', 2, 3,
NULL, 1, 2, 'max(20vw, 400px)', '2.5vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'<form#afficher4_>');