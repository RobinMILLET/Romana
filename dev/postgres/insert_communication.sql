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

INSERT INTO photo (photo_libelle, photo_url) VALUES 
('Salle du restaurant', 'interior.jpg'), 
('Pâtes à la carbonara', 'carbonara.jpg');

INSERT INTO conteneur (conteneur_libelle, page_id, photo_id, police_id, conteneur_texte,
conteneur_ligne, conteneur_colonne, conteneur_aligne, conteneur_bordure, conteneur_couleur,
conteneur_fond, conteneur_largeur, conteneur_marges, conteneur_ombre, conteneur_rayon, conteneur_visible) VALUES
('1Accueil1', 1, NULL, 18, '# La Romana',
1, 1, 5, NULL, 'ffffffff', NULL, NULL, NULL, NULL, '0px', TRUE),
('2Accueil2', 1, 1, NULL, NULL,
2, 1, 5, '00000080', 'ffffffff', NULL, 'max(40vw,300px)', NULL, '00000080', '15px', TRUE),
('3Accueil3', 1, NULL, NULL, '### Bienvenue à La Romana\nPlongez dans une ambiance chaleureuse et dégustez une cuisine italienne authentique :\n- Pizzas au feu de bois\n- Pâtes fraîches faites maison\n- Desserts gourmands et raffinés\n\nNous vous accueillons avec le sourire, midi et soir.',
2, 2, 4, 'ffffffff', 'ffffffcc', '00000022', 'max(40vw,300px)', '20px 30px 20px 30px', NULL, '15px', TRUE),
('4Accueil4', 1, 2, 3, '#### Réservez une table dès maintenant !\n📞 04 50 00 00 00\n💬 Ou utilisez notre formulaire de réservation.',
3, 1, 2, NULL, 'ff6633ff', '000000aa', NULL, '25px', NULL, '8px', TRUE);


INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 1, '<h1>La Romana</h1>'),
(0, 3, '<h3>Bienvenue à La Romana</h3><p>Plongez dans une ambiance chaleureuse et dégustez une cuisine italienne authentique :</p><ul><li>Pizzas au feu de bois</li><li>Pâtes fraîches faites maison</li><li>Desserts gourmands et raffinés</li></ul><p>Nous vous accueillons avec le sourire, midi et soir.</p>'),
(0, 4, '<h4>Réservez une table dès maintenant !</h4><p>📞 Appelez-nous au <b>04 50 00 00 00</b></p><p>💬 Ou utilisez notre <a href="/book">formulaire de réservation</a></p>'),

(1, 1, '<h1>La Romana</h1>'),
(1, 3, '<h3>Welcome to La Romana</h3><p>Step into a warm ambiance and enjoy authentic Italian cuisine:</p><ul><li>Wood-fired pizzas</li><li>Fresh handmade pasta</li><li>Refined and delicious desserts</li></ul><p>We welcome you with a smile, for lunch and dinner.</p>'),
(1, 4, '<h4>Book a table now!</h4><p>📞 Call us at <b>+33 4 50 00 00 00</b></p><p>💬 Or use our <a href="/book">booking form</a></p>');

/* --------------------------------
    PAGE 2 : À Propos
*/ --------------------------------

INSERT INTO photo (photo_libelle, photo_url) VALUES
('Fourneau à pizzas', 'furnace.jpg'),
('Chef Luigi', 'chef.jpg');

INSERT INTO conteneur (conteneur_libelle, page_id, photo_id, police_id, conteneur_texte,
conteneur_ligne, conteneur_colonne, conteneur_aligne, conteneur_bordure, conteneur_couleur,
conteneur_fond, conteneur_largeur, conteneur_marges, conteneur_ombre, conteneur_rayon, conteneur_visible)
VALUES
('5About1', 2, NULL, 18, '# À propos de La Romana',
1, 1, 5, NULL, 'ffffffff', NULL, NULL, NULL, NULL, '0px', TRUE),
('6About2', 2, 3, NULL,
'### Une histoire de passion\nFondée en 2003 à Annecy, La Romana est née de l’envie de partager la gastronomie italienne dans un cadre familial.\n\nNos recettes sont inspirées des traditions romaines, avec des produits frais et des pâtes faites maison chaque jour.',
2, 1, 4, NULL, 'ffffffdd', '00000022', '40vw', '20px', NULL, '10px', TRUE),
('7About3', 2, 4, NULL,
'#### Notre chef Luigi\nDiplômé de la Scuola Alberghiera di Roma, Luigi cuisine avec le cœur. Sa spécialité ? La pizza napolitaine au feu de bois.',
2, 2, 4, NULL, 'ffffffdd', '00000022', '25vw', '20px', NULL, '10px', TRUE);

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 5, '<h1>À propos de La Romana</h1>'),
(0, 6, '<h3>Une histoire de passion</h3><p>Fondée en 2003 à Annecy, La Romana est née de l’envie de partager la gastronomie italienne dans un cadre familial.</p><p>Nos recettes sont inspirées des traditions romaines, avec des produits frais et des pâtes faites maison chaque jour.</p>'),
(0, 7, '<h4>Notre chef Luigi</h4><p>Diplômé de la Scuola Alberghiera di Roma, Luigi cuisine avec le cœur. Sa spécialité ? La pizza napolitaine au feu de bois.</p>'),

(1, 5, '<h1>About La Romana</h1>'),
(1, 6, '<h3>A story of passion</h3><p>Founded in 2003 in Annecy, La Romana was born out of a desire to share Italian gastronomy in a warm, family setting.</p><p>Our recipes are inspired by Roman traditions, with fresh ingredients and homemade pasta prepared daily.</p>'),
(1, 7, '<h4>Our Chef Luigi</h4><p>Graduated from the Scuola Alberghiera di Roma, Luigi cooks with heart. His specialty? Neapolitan pizza baked in a wood-fired oven.</p>');

/* --------------------------------
    PAGE 4 : Horaires
*/ --------------------------------

INSERT INTO conteneur (conteneur_libelle, page_id, photo_id, police_id, conteneur_texte,
conteneur_ligne, conteneur_colonne, conteneur_aligne, conteneur_bordure, conteneur_couleur,
conteneur_fond, conteneur_largeur, conteneur_marges, conteneur_ombre, conteneur_rayon, conteneur_visible) VALUES
('8Horaire1', 4, NULL, 8, '# Horaires\n||||\n|:-|-:|-:|\n|Lundi|12:00 - 14:00|19:00 - 22:00|\n|Mardi|12:00 - 14:00|19:00 - 22:00|\n|Mercredi|12:00 - 14:00|19:00 - 22:00|\n|Jeudi|12:00 - 14:00|19:00 - 22:00|\n|Vendredi|12:00 - 14:00|19:00 - 22:00|\n|Samedi|12:00 - 14:00|19:00 - 22:00|\n|Dimanche|-|-|\n\nLa terrasse extérieure  peut rester fermée selon la météo et les disponibilités.',
1, 1, 2, NULL, 'ffaaaaff', NULL, 'min(90vw, 750px)', '5vw', NULL, '0 px', TRUE);

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 8, '<h1>Horaires</h1><table><thead><tr><th align="left"></th><th align="right"></th><th align="right"></th></tr></thead><tbody><tr><td align="left">Lundi</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Mardi</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Mercredi</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Jeudi</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Vendredi</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Samedi</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Dimanche</td><td align="right">-</td><td align="right">-</td></tr></tbody></table><p>La terrasse extérieure  peut rester fermée selon la météo et les disponibilités.</p>'),
(1, 8, '<h1>Opening hours</h1><table><thead><tr><th align="left"></th><th align="right"></th><th align="right"></th></tr></thead><tbody><tr><td align="left">Monday</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Tuesday</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Wednesday</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Thursday</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Friday</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Saturday</td><td align="right">12:00 - 14:00</td><td align="right">19:00 - 22:00</td></tr><tr><td align="left">Sunday</td><td align="right">-</td><td align="right">-</td></tr></tbody></table><p>The exterior dining space may stay closed depending on weather and availability.</p>');

/* --------------------------------
    PAGE 6 : Réservation
*/ --------------------------------

INSERT INTO conteneur (conteneur_libelle, page_id, photo_id, police_id, conteneur_texte,
conteneur_ligne, conteneur_colonne, conteneur_aligne, conteneur_bordure, conteneur_couleur,
conteneur_fond, conteneur_largeur, conteneur_marges, conteneur_ombre, conteneur_rayon, conteneur_visible) VALUES
('9Reserv1', 6, NULL, 1, '<form#reservation>',
1, 1, 2, '33333333', 'ffffffff', NULL, '75vw', '5vw 10vw', NULL, '0 px', TRUE);

/* --------------------------------
    PAGE 8 : Contact
*/ --------------------------------

INSERT INTO conteneur (conteneur_libelle, page_id, photo_id, police_id, conteneur_texte,
conteneur_ligne, conteneur_colonne, conteneur_aligne, conteneur_bordure, conteneur_couleur,
conteneur_fond, conteneur_largeur, conteneur_marges, conteneur_ombre, conteneur_rayon, conteneur_visible)
VALUES
('10Contact1', 8, NULL, 18, '# Nous contacter',
1, 1, 5, NULL, 'ffffffff', NULL, NULL, NULL, NULL, '0px', TRUE),
('11Contact2', 8, NULL, NULL,
'### 📞 Téléphone\n**04 50 00 00 00**\n\n### ✉️ Email\n**contact@laromana.fr**\n\n### 📍 Adresse\n12 rue de la Pasta, 74000 Annecy',
2, 1, 4, NULL, 'ffffffdd', '00000022', '30vw', '20px', NULL, '10px', TRUE),
('12Contact3', 8, NULL, NULL, '<googlemap/>',
2, 2, 5, NULL, 'ffffffdd', '00000022', '30vw', '20px', NULL, '10px', TRUE);

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 10, '<h1>Nous contacter</h1>'),
(0, 11, '<h3>📞 Téléphone</h3><p><b>04 50 00 00 00</b></p><h3>✉️ Email</h3><p><b>contact@laromana.fr</b></p><h3>📍 Adresse</h3><p>12 rue de la Pasta, 74000 Annecy</p>'),
(0, 12, '<googlemap/>'),

(1, 10, '<h1>Contact us</h1>'),
(1, 11, '<h3>📞 Phone</h3><p><b>+33 4 50 00 00 00</b></p><h3>✉️ Email</h3><p><b>contact@laromana.fr</b></p><h3>📍 Address</h3><p>12 rue de la Pasta, 74000 Annecy</p>'),
(1, 12, '<googlemap/>');
