/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM conteneur where page_id = 6;

/* --------------------------------
    BOOK
*/ --------------------------------

INSERT INTO conteneur (page_id, conteneur_id, conteneur_libelle, conteneur_ligne, conteneur_colonne,
photo_id, police_id, conteneur_aligne, conteneur_largeur, conteneur_marges, conteneur_rayon, conteneur_visible,
conteneur_couleur, conteneur_fond, conteneur_bordure, conteneur_ombre, 
conteneur_texte) VALUES

(6, 61, 'book1', 1, 1,
NULL, 4, 5, NULL, '1vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'.'),

(6, 62, 'book2', 2, 1,
5, 5, 2, 'max(20vw, 400px)', '2.5vw', '2vw', TRUE,
'ffffffff', '00000070', NULL, '000000ff',
'.'),

(6, 63, 'book3', 2, 2,
NULL, 1, 5, 'fit-content', '2.5vw', '2vw', TRUE,
'ffffffff', NULL, '505050ff', NULL,
'<form#reserver_>'),

(6, 64, 'book4', 2, 3,
NULL, 1, 2, 'fit-content', '2.5vw', '2vw', TRUE,
'ffffffff', NULL, '505050ff', NULL,
'<form#trouver_>');

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 61, '<h2>Vous souhaitez réserver ? Vous êtes au bon endroit !</h2>'),
(1, 61, '<h2>Do you wish to make a reservation? You''re in the right place!</h2>'),
(0, 62, '<h4>Garantissez-vous une place en réservant un créneau à l''avance, et restez tranquilles en attendant :<br>On s''occupe de tout !</h4>'),
(1, 62, '<h4>Lock-in a time slot to ensure you spot is safekept, and rest easy in the meantime:<br>We''ve got you covered!</h4>');