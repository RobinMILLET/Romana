/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM conteneur where page_id = 1;

/* --------------------------------
    HOME
*/ --------------------------------

INSERT INTO conteneur (page_id, conteneur_id, conteneur_libelle, conteneur_ligne, conteneur_colonne,
photo_id, police_id, conteneur_aligne, conteneur_largeur, conteneur_marges, conteneur_rayon, conteneur_visible,
conteneur_couleur, conteneur_fond, conteneur_bordure, conteneur_ombre, 
conteneur_texte) VALUES

(1, 11, 'home1', 1, 1,
8, NULL, 5, '225px', '100px', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
'.'),

(1, 12, 'home2', 2, 1,
1, 18, 5, NULL, '1vw', '0px', TRUE,
'000000ff', NULL, NULL, NULL,
'.'),

(1, 13, 'home3', 3, 1,
NULL, 4, 5, 'max(50vw, 400px)', '1vw', '0px', TRUE,
'ffffffff', NULL, 'ffffffff', NULL,
'.'),

(1, 14, 'home4', 4, 1,
NULL, NULL, 1, 'max(40vw, 400px)', '2.5vw', '2.5vw', TRUE,
'ffffffff', '000000ff', NULL, NULL,
'.'),

(1, 15, 'home5', 4, 2,
3, NULL, 5, 'max(40vw, 500px)', '1vw', '0px', TRUE,
'ffffffff', NULL, '000000ff', NULL,
NULL),

(1, 16, 'home6', 5, 1,
5, NULL, 5, 'max(25vw, 300px)', '1vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
NULL),

(1, 17, 'home7', 5, 2,
6, NULL, 5, 'max(25vw, 300px)', '1vw', '0px', TRUE,
'ffffffff', NULL, NULL, NULL,
NULL),

(1, 18, 'home8', 6, 1,
4, NULL, 5, 'max(50vw, 500px)', '1vw', '0px', TRUE,
'ffffffff', NULL, '000000ff', NULL,
NULL),

(1, 19, 'home9', 6, 2,
NULL, NULL, 4, 'max(33vw, 400px)', '2.5vw', '2.5vw', TRUE,
'000000ff', 'ccccccff', NULL, NULL,
'.');

INSERT INTO contenu (langue_id, conteneur_id, contenu_texte) VALUES
(0, 11, '&nbsp;'),
(0, 12, '<h1>La Romana</h1>'),
(0, 13, '<h2>Ristorante Pizzeria Annecy</h2><p>Bienvenue à <b>La Romana</b>, votre adresse incontournable pour une expérience culinaire authentiquement italienne et cuisine française à <b>Annecy</b>. En tant que passionnés de la gastronomie transalpine, nous sommes fiers de vous accueillir dans notre restaurant chaleureux, où chaque plat est une véritable déclaration d''amour à la cuisine italienne. Afin de vous faciliter l''accès vous pourrez facilement vous garer sur notre parking privé.</p>'),
(0, 14, '<h2>La passion de la pizza</h2><h3>Et des spécialités italiennes</h3><p>Plongez dans l''univers envoûtant de nos spécialités italiennes et laissez-vous séduire par nos pizzas artisanales, cuites  sur place. Chaque recette est élaborée avec soin, alliant tradition et créativité pour vous offrir un véritable voyage culinaire au cœur de l''Italie. Des classiques intemporels aux créations originales, il y en a pour tous les goûts à <b>La Romana</b>.</p>'),
(0, 19, '<h2>Une cuisine de tradition</h2><h3>L''Italie dans votre assiette</h3><u><i><b><a href=''/book''>Réserver une table</a></b></i></u>');