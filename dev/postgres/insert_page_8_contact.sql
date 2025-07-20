/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM conteneur where page_id = 8;

/* --------------------------------
    CONTACT
*/ --------------------------------

INSERT INTO conteneur (page_id, conteneur_id, conteneur_libelle, conteneur_ligne, conteneur_colonne,
photo_id, police_id, conteneur_aligne, conteneur_largeur, conteneur_marges, conteneur_rayon, conteneur_visible,
conteneur_couleur, conteneur_fond, conteneur_bordure, conteneur_ombre, 
conteneur_texte) VALUES

(8, 81, 'contact1', 1, 1,
NULL, 4, 5, NULL, '1vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'.'),

(8, 82, 'contact2', 2, 1,
NULL, NULL, 5, 'max(50vw,400px)', NULL, '1vw', TRUE,
'ffffffff', NULL, NULL, '000000ff',
'<form#googlemap>'),

(8, 83, 'contact3', 3, 1,
1, 3, 5, NULL, '5px', '0px', TRUE,
'000000c0', NULL, NULL, NULL,
'.'),

(8, 84, 'contact4', 4, 1,
NULL, 14, 5, 'fit-content', '2vw 2.5vw 1.5vw 2.5vw', '5vw', TRUE,
'ffffffff', '50505050', NULL, NULL,
'.');

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 81, '<h2>Rejoignez nous au plus vite !</h2>'),
(1, 81, '<h2>Come join us as soon as possible!</h2>'),
(0, 83, '<h3>La Romana | Ristorante • Pizzeria</h3>'),
(1, 83, '<h3>La Romana | Ristorante • Pizzeria</h3>'),
(0, 84, '<p>581 Route du Pont de Brogny<br>74370 Pringy<br><b>04 50 27 35 46</b></p>'),
(1, 84, '<p>581 Route du Pont de Brogny<br>74370 Pringy<br><b>+33 4 50 27 35 46</b></p>');