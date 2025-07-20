/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM conteneur where page_id = 2;

/* --------------------------------
    ABOUT
*/ --------------------------------

INSERT INTO conteneur (page_id, conteneur_id, conteneur_libelle, conteneur_ligne, conteneur_colonne,
photo_id, police_id, conteneur_aligne, conteneur_largeur, conteneur_marges, conteneur_rayon, conteneur_visible,
conteneur_couleur, conteneur_fond, conteneur_bordure, conteneur_ombre, 
conteneur_texte) VALUES

(2, 21, 'about1', 1, 1,
1, 18, 5, NULL, '1vw', '0px', TRUE,
'000000ff', NULL, NULL, NULL,
'.'),

(2, 22, 'about2', 2, 1,
NULL, NULL, 4, NULL, '0 5vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'.'),

(2, 23, 'about3', 3, 1,
2, NULL, 5, 'max(33vw, 400px)', '5vw', '5vw', TRUE,
'ffffffff', NULL, '000000ff', NULL,
NULL),

(2, 24, 'about4', 3, 2,
NULL, NULL, 1, 'max(50vw, 500px)', '2.5vw', '2.5vw', TRUE,
'ffffffff', '00000080', NULL, NULL,
'.'),

(2, 25, 'about5', 4, 1,
4, NULL, 5, NULL, '1vw', '0px', TRUE,
'ffe000ff', '00000050', NULL, NULL,
'.'),

(2, 26, 'about7', 5, 1,
NULL, NULL, 4, 'max(50vw, 500px)', '2.5vw', '2.5vw', TRUE,
'000000ff', 'ffffffc0', NULL, NULL,
'.'),

(2, 27, 'about6', 5, 2,
6, NULL, 5, 'max(33vw, 400px)', '5vw', '5vw', TRUE,
'ffffffff', NULL, '000000ff', NULL,
NULL);

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 21, '<h2>La Romana : Le restaurant</h2>'),
(0, 22, '<h4>Des saveurs authentiques</h4><h3>Des produits locaux</h3><p>Chez <b>La Romana</b>, nous croyons fermement en la qualité des ingrédients. C''est pourquoi nous nous approvisionnons exclusivement en produits frais et locaux, sélectionnés avec soin pour vous offrir une expérience gustative inoubliable. De nos légumes croquants à nos fromages fondants, chaque bouchée reflète l''essence même de la cuisine italienne : simple, savoureuse et généreuse.</p>'),
(0, 24, '<h3>Élégance et convivialité</h3><p>Plongez dans une atmosphère accueillante et contemporaine dès votre entrée dans notre établissement. Notre salle lumineuse, baignée de lumière naturelle, vous invite à un voyage sensoriel où chaque détail est soigneusement pensé pour créer une expérience inoubliable. La décoration moderne, aux lignes épurées et aux accents italiens, évoque l''élégance intemporelle de la péninsule méditerranéenne.</p><br><br><p>Installez-vous confortablement sur nos sièges et banquettes, symboles de convivialité et de chaleur humaine, et laissez-vous imprégner par l''ambiance chaleureuse et festive de La Romana. Que ce soit pour un dîner en amoureux ou un repas entre amis, notre cadre raffiné saura vous séduire et vous transporter dans une véritable oasis de plaisir culinaire.</p>'),
(0, 25, '<br>&nbsp;<br>&nbsp;<br>&nbsp;<br><h1><i><u><a href=''/book''>Réserver une table</a></u></i></h1><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>'),
(0, 26, '<h2>Accompagnez votre repas</h2><h4>Avec notre sélection de vins</h4><p>Pour sublimer votre expérience gastronomique, rien de tel qu''un verre de vin finement sélectionné. Découvrez notre carte des vins, mettant à l''honneur les trésors viticoles de l''Italie et de la France. Laissez-vous guider par nos suggestions avisées et savourez chaque gorgée en harmonie parfaite avec vos plats préférés.</p><br><br><p>À <b>La Romana</b>, nous sommes bien plus qu''une simple pizzeria : nous sommes une invitation au voyage, une ode à la convivialité et à la bonne cuisine. Rejoignez-nous et laissez-vous transporter par la magie de l''Italie, le temps d''un repas mémorable à <b>Annecy</b>. Buon appetito !</p>');