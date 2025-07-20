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
DELETE FROM produit;
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
(0), (1), (2), (3), (4), (5), (6), (7), (8);

INSERT INTO page (page_id, page_ordre, page_route) VALUES
(0, 0, 'display'),
(1, 1, 'home'), (2, 2, 'about'), (3, 3, 'event'), (4, 4, 'hours'),
(5, 5, 'menu'), (6, 6, 'book'), (7, 7, 'rating'), (8, 8, 'contact');

INSERT INTO traduction (traductible_id, langue_id, traduction_libelle) VALUES
(1, 0, 'Accueil'), (2, 0, 'À propos'), (3, 0, 'Événements'), (4, 0, 'Horaires'),
(5, 0, 'Menus'), (6, 0, 'Réservation'), (7, 0, 'Avis'), (8, 0, 'Contact'),
(1, 1, 'Home'), (2, 1, 'About'), (3, 1, 'Events'), (4, 1, 'Hours'),
(5, 1, 'Menus'), (6, 1, 'Booking'), (7, 1, 'Ratings'), (8, 1, 'Contact');

/* --------------------------------
    PHOTOS
*/ --------------------------------

INSERT INTO photo (photo_libelle, photo_url) VALUES 
('Bandeau italien', 'bandeau.png'), 
('Bar vide', 'bar.jpg'),
('Restaurant depuis l''exterieur', 'resto.jpg'),
('Rue le long du restaurant', 'rue.jpg'),
('Gros plan sur table et couvert', 'table.jpg'),
('Plât, pain et vin', 'vin.jpg'),
('Logo Romana en noir', 'romana_b.png'),
('Logo Romana en blanc', 'romana_w.png');