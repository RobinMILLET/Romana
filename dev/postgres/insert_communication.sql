/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    SEQUENCES
*/ --------------------------------

ALTER SEQUENCE sq_conteneur RESTART;
ALTER SEQUENCE sq_evenement RESTART;
ALTER SEQUENCE sq_photo RESTART;
ALTER SEQUENCE sq_traductible RESTART;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM page;
DELETE FROM element;
DELETE FROM traductible;
DELETE FROM evenement;
DELETE FROM police;
DELETE FROM photo;
DELETE FROM langue;

/* --------------------------------
    LANGUE
*/ --------------------------------

INSERT INTO langue (langue_id, langue_code, langue_libelle, langue_affichage) VALUES
(0, 'fr', 'français', 'Français'),
(1, 'en', 'anglais', 'English'),
(2, 'es', 'espagnol', 'Español'),
(3, 'de', 'allemand', 'Deutsch'),
(4, 'it', 'italien', 'Italiano'),
(5, 'pt', 'portugais', 'Português'),
(6, 'ru', 'russe', 'Русский'),
(7, 'zh', 'chinois', '中文'),
(8, 'ja', 'japonais', '日本語'),
(9, 'ko', 'coréen', '한국어'),
(10, 'ar', 'arabe', 'العربية');

/* --------------------------------
    POLICE
*/ --------------------------------

INSERT INTO police (police_id, police_libelle, police_texte) VALUES
(1, 'Arial', 'Arial, sans-serif'),
(2, 'Trebuchet', 'Trebuchet MS, sans-serif'),
(3, 'Times New Roman', 'Times, Times New Roman, serif'),
(4, 'Typewriter', 'American Typewriter, serif'),
(5, 'Andale Mono', 'Andale Mono, monospace'),
(6, 'Courier New', 'Courier New, monospace'),
(7, 'OCR', 'OCR A Std, monospace'),
(8, 'Comic Sans', 'Comic Sans MS, Comic Sans, cursive'),
(9, 'Brush script', 'Brush Script MT, Brush Script Std, cursive'),
(10, 'Impact', 'Impact, fantasy'),
(11, 'Luminari', 'Luminari, fantasy'),
(12, 'Chalkduster', 'Chalkduster, fantasy'),
(13, 'Jazz', 'Jazz LET, fantasy'),
(14, 'Blippo', 'Blippo, fantasy'),
(15, 'Stencil', 'Stencil Std, fantasy'),
(16, 'Marker', 'Marker Felt, fantasy'),
(17, 'Trattatello', 'Trattatello, fantasy'),
(18, 'Felipa', 'Felipa, serif');

/* --------------------------------
    PAGE
*/ --------------------------------

INSERT INTO traductible (traductible_id) VALUES
(default),(default),(default),(default),
(default),(default),(default),(default);

INSERT INTO page (page_id, page_ordre, page_route) VALUES
(1, 1, 'home'), (2, 2, 'about'), (3, 3, 'event'), (4, 4, 'hours'),
(5, 5, 'menu'), (6, 6, 'book'), (7, 7, 'rating'), (8, 8, 'contact');

INSERT INTO traduction (traductible_id, langue_id, traduction_libelle) VALUES
(1, 0, 'Accueil'), (2, 0, 'À propos'), (3, 0, 'Événements'), (4, 0, 'Horaires'),
(5, 0, 'Menus'), (6, 0, 'Réserver'), (7, 0, 'Avis'), (8, 0, 'Contact'),
(1, 1, 'Home'), (2, 1, 'About'), (3, 1, 'Events'), (4, 1, 'Hours'),
(5, 1, 'Menus'), (6, 1, 'Booking'), (7, 1, 'Ratings'), (8, 1, 'Contact');

/* --------------------------------
    PAGE 1 : Accueil
*/ --------------------------------

INSERT INTO photo (photo_libelle, photo_url) VALUES ('Annecy', 'photo1.png'), ('Robin', 'photo2.jpg');

INSERT INTO conteneur (conteneur_libelle, page_id, photo_id, police_id, conteneur_texte,
conteneur_ligne, conteneur_colonne, conteneur_aligne, conteneur_bordure, conteneur_couleur,
conteneur_fond, conteneur_largeur, conteneur_marges, conteneur_ombre, conteneur_rayon, conteneur_visible) VALUES
('1Accueil1', 1, NULL, 18, '# La Romana',
1, 1, 5, NULL, 'ffffffff', NULL, NULL, NULL, NULL, '0px', TRUE),
('2Accueil2', 1, 2, NULL, NULL,
2, 1, 5, '00000080', 'ffffffff', NULL, '25vw', NULL, '00000080', '15px', TRUE),
('3Accueil3', 1, NULL, NULL, '### Bienvenue\nDans un cadre soigné et authentiquement italien, venez déguster:\n- Pizzas\n- Pâtes\n- Desserts\nEt autres délicatesses onctueuses !',
2, 2, 4, 'ffffffff', 'ffffffaa', '00000055', '25vw', '20px 40px 30px 10px', NULL, '15px',TRUE),
('4Accueil4', 1, 1, 3, '#### Réservez dès maintenant <u>[ici](https://www.youtube.com/watch?v=dQw4w9WgXcQ)</u>\nOu appellez nous au **0123456789**',
3, 1, 2, NULL, 'aa5500ff', '000000aa', NULL, '25px', NULL, '0px', TRUE);

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 1, '<h1>La Romana</h1>'),
(0, 3, '<h3>Bienvenue</h3><p>Dans un cadre soigné et authentiquement italien, venez déguster:</p><ul><li>Pizzas</li><li>Pâtes</li><li>Desserts</li></ul><p>Et autres délicatesses onctueuses !</p>'),
(0, 4, '<h4>Réservez dès maintenant <u><a href=''https://www.youtube.com/watch?v=dQw4w9WgXcQ''>ici</a></u></h4><p>Ou appellez nous au <b>0123456789</b></p>'),
(1, 1, '<h1>La Romana</h1>'),
(1, 3, '<h3>Welcome</h3><p>In a polished and authentically italian setting, come get a taste of:</p><ul><li>Pizzas</li><li>Pastas</li><li>Desserts</li></ul><p>And other flavorful delicacies!</p>'),
(1, 4, '<h4>Book right now <u><a href=''https://www.youtube.com/watch?v=dQw4w9WgXcQ''>here</a></u></h4><p>Or call us at <b>+33123456789</b></p>');