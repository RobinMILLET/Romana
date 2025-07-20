/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM traductible where traductible_id BETWEEN 50 AND 200;

/* --------------------------------
    FUNCTION
*/ --------------------------------

CREATE OR REPLACE FUNCTION add_menu_element(
    p_traductible_id INT,
    p_langue_id INT,
    p_libelle VARCHAR,
    p_description VARCHAR DEFAULT NULL,
    p_categorie_ordre INT DEFAULT NULL,
    p_categorie_idparent INT DEFAULT NULL
)
RETURNS VOID AS $$
BEGIN
    -- Insertion du traductible ( id )
    INSERT INTO traductible (traductible_id) VALUES (p_traductible_id);

    -- Insertion de la traduction ( langue, id, libelle, description? )
    INSERT INTO traduction (langue_id, traductible_id, traduction_libelle, traduction_description)
    VALUES (p_langue_id, p_traductible_id, p_libelle, p_description);

    IF p_categorie_ordre IS NOT NULL THEN -- Si ordre est fournis, c'est une catégorie
        -- Insertion de la catégorie ( id, ordre, parent? )
        INSERT INTO categorie (categorie_id, categorie_ordre, categorie_idparent)
        VALUES (p_traductible_id, p_categorie_ordre, p_categorie_idparent);
    ELSE -- Sinon c'est un produit
        -- Insertion du produit ( id )
        INSERT INTO produit (produit_id)
        VALUES (p_traductible_id);
    END IF;
END;
$$ LANGUAGE plpgsql;

/* --------------------------------
    CATEGORIES
*/ --------------------------------

SELECT add_menu_element(
    50, 0, 'Pizzas à emporter',
    'Victime de notre succès, nous vous suggérons de commander vos pizzas dès 18H le soir.<br>Merci de votre compréhension et surtout de votre fidélité !',
    1, NULL
);

SELECT add_menu_element(
    51, 0, 'La Carte',
    'La Romana est ravie de vous accueillir avec sa nouvelle équipe.<br>Nous vous proposons notre nouvelle carte qui a été réalisée à partir de produits frais, de saison.<br><br>En vous souhaitant autant de plaisir dans l''assiette que nous en avons eu à la réaliser.<br><b>Bon appétit !</b><br><br>Prix nets en euros – service compris',
    2, NULL
);

SELECT add_menu_element(52, 0, 'Suppléments', NULL, 1, 50);

SELECT add_menu_element(53, 0, 'Menu du jour', 'Servi uniquement le midi, du lundi au vendredi', 1, 51);
SELECT add_menu_element(54, 0, 'Antipasti', NULL, 2, 51);
SELECT add_menu_element(55, 0, 'Salades', NULL, 3, 51);
SELECT add_menu_element(56, 0, 'Pizzas', NULL, 4, 51);
SELECT add_menu_element(57, 0, 'Suppléments', NULL, 1, 56);
SELECT add_menu_element(58, 0, 'Pasta et Risotto', NULL, 5, 51);
SELECT add_menu_element(59, 0, 'Menu enfant', 'Glace surprise en dessert', 6, 51);
SELECT add_menu_element(60, 0, 'Viande et poisson', NULL, 7, 51);
SELECT add_menu_element(61, 0, 'Fromages et desserts', NULL, 8, 51);
SELECT add_menu_element(62, 0, 'Softs', NULL, 9, 51);
SELECT add_menu_element(63, 0, 'Cocktails sans alcool', NULL, 10, 51);
SELECT add_menu_element(64, 0, 'Cocktails', NULL, 11, 51);
SELECT add_menu_element(65, 0, 'Apéritifs', NULL, 12, 51);
SELECT add_menu_element(66, 0, 'Bières Pressions', NULL, 13, 51);
SELECT add_menu_element(67, 0, 'Bières Bouteilles', NULL, 14, 51);
SELECT add_menu_element(68, 0, 'Alcools et Digestifs', NULL, 15, 51);

/* --------------------------------
    A EMPORTER
*/ --------------------------------

SELECT add_menu_element(80, 0, 'Margherita', 'Base tomate, mozzarella fior di latte, basilic');
SELECT add_menu_element(81, 0, 'Napolitaine', 'Base tomate, mozzarella fior di latte, câpres, anchois');
SELECT add_menu_element(82, 0, 'Prosciutto', 'Base tomate, mozzarella fior di latte, basilic, jambon blanc');
SELECT add_menu_element(83, 0, 'Prosciutto e funghi', 'Base tomate, mozzarella fior di latte, basilic, jambon blanc, champignons de Paris');
SELECT add_menu_element(84, 0, 'Capretta', 'Base crème fraîche, mozzarella fior di latte, fromage de chèvre, miel, basilic');
SELECT add_menu_element(85, 0, 'Capricciosa', 'Base tomate, mozzarella fior di latte, jambon, champignons de Paris, artichaut, olives');
SELECT add_menu_element(86, 0, '4 Formaggi', 'Base tomate, mozzarella fior di latte, gorgonzola, fromage de chèvre, reblochon');
SELECT add_menu_element(87, 0, 'Parma', 'Base tomate, mozzarella fior di latte, jambon de Parme, grada panado, basilic');
SELECT add_menu_element(88, 0, 'Diavola', 'Base tomate, mozzarella fior di latte, spianata, oignons rouge, olives noires, basilic');
SELECT add_menu_element(89, 0, 'Bufalina', 'Base tomate, mozzarella fior di latte, bufala, grada panado, basilic');
SELECT add_menu_element(90, 0, 'Parmigiana', 'Base tomate, mozzarella fior di latte, bufala, grada panado, aubergine, basilic');
SELECT add_menu_element(91, 0, 'Calzone', 'Base tomate, mozzarella fior di latte, jambon blanc, champignons de Paris, œuf');
SELECT add_menu_element(92, 0, 'Bella ciao', 'Base tomate, roquette, jambon de parme, grana padano, burrata, basilic');
SELECT add_menu_element(93, 0, 'Tartufata', 'Base crème de truffe, mozzarella fior di latte, jambon blanc, grada panado, tomates cerises');
SELECT add_menu_element(94, 0, 'Salmone', 'Base crème fraîche, mozzarella fior di latte, saumon, roquette, tomates cerises');
SELECT add_menu_element(95, 0, 'La Romana', 'Base tomate, mozzarella fior di latte, spianata, oignons rouge, aubergines, fromage de chèvre');

SELECT add_menu_element(96, 0, '1 œuf, roquette, champignons de Paris, grana padano, anchois');
SELECT add_menu_element(97, 0, 'Bufala, spianata, jambon blanc, jambon de Parme, saumon, gorgonzola, fromage de chèvre, reblochon');
SELECT add_menu_element(98, 0, 'Burrata entière');

/* --------------------------------
    SUR PLACE
*/ --------------------------------

SELECT add_menu_element(99, 0, 'Supplément sirop');

-- Menu du jour
SELECT add_menu_element(100, 0, 'Plat du jour');
SELECT add_menu_element(101, 0, 'Entrée et plat du jour');
SELECT add_menu_element(102, 0, 'Plat du jour et dessert');
SELECT add_menu_element(103, 0, 'Entrée, plat du jour et dessert');

-- Antipasti
SELECT add_menu_element(104, 0, 'Assiette de charcuterie et mozzarella burrata');
SELECT add_menu_element(105, 0, 'Straciatella di bufala, focaccia');
SELECT add_menu_element(106, 0, 'Bruschetta', 'Concassée de tomates, ail, huile d’olive, basilic, origan');

-- Salades
SELECT add_menu_element(107, 0, 'César', 'Salade de saison, émincé de volaille, parmesan, croûtons, tomates, oignons rouges, sauce César');
SELECT add_menu_element(108, 0, 'Italienne', 'Salade de saison, tomates, jambon de Parme, mozzarella di bufala, gressin');
SELECT add_menu_element(109, 0, 'Chèvre chaud', 'Salade de saison, toasts de chèvre chaud au miel, pommes, noix, tomates, oignons rouges');
SELECT add_menu_element(110, 0, 'Estival', 'Salade de saison, avocat, saumon fumé, carottes, courgettes, tomates séchées, oignons rouges');

-- Pasta et Risotto
SELECT add_menu_element(111, 0, 'Gnocchi aux 3 fromages', 'Gorgonzola, pecorino, grana padano');
SELECT add_menu_element(112, 0, 'Spaghetti à la bolognaise', 'Sauce bolognaise au veau et bœuf');
SELECT add_menu_element(113, 0, 'Linguini au pesto et burrata', 'Pesto maison : roquette, basilic, pignons de pin, grana padano');
SELECT add_menu_element(114, 0, 'Linguini alle vongole', 'Palourdes, tomates cerises, échalote, ail, herbes fraîches, vin blanc, fumet de poisson');
SELECT add_menu_element(115, 0, 'Risotto aux champignons du moment', 'Crème de truffe');
SELECT add_menu_element(116, 0, 'Risotto delizia au citron', 'Crème d’aneth et saumon fumé');

-- Menu enfant
SELECT add_menu_element(117, 0, 'Pâtes bolognaise');
SELECT add_menu_element(118, 0, 'Pâtes napolitaine');
SELECT add_menu_element(119, 0, 'Pizza bambino', 'Base tomate, mozzarella fior di latte, jambon blanc');

-- Viande et poisson
SELECT add_menu_element(120, 0, 'Entrecôte de bœuf', 'Charolaise 300 g min, frites, salade, sauce au choix : beurre maître d’hôtel, poivre vert, champignons du moment');
SELECT add_menu_element(121, 0, 'Ballotine de poulet jaune', 'Cuisson basse température, jambon de Parme, gorgonzola, linguini, crème de champignons du moment');
SELECT add_menu_element(122, 0, 'Côte de veau gremolata', 'Cuisson basse température, persillade aux agrumes, linguini napolitaine');
SELECT add_menu_element(123, 0, 'Escalope de veau milanaise', 'Servie avec linguini napolitaine');
SELECT add_menu_element(124, 0, 'Burger de la Romana', 'Steak haché bœuf Black Angus 150 g, tomates confites, oignons, gorgonzola, roquette');
SELECT add_menu_element(125, 0, 'Tartare de bœuf façon Romana', 'Pesto, grana padano, pignons, tomates confites, jaune d’œuf');
SELECT add_menu_element(126, 0, 'Tartare de saumon façon Romana', 'Crème de mascarpone aux agrumes et herbes fraîches');

-- Fromages et desserts
SELECT add_menu_element(127, 0, 'Assiette de fromages du moment');
SELECT add_menu_element(128, 0, 'Cœur coulant au chocolat');
SELECT add_menu_element(129, 0, 'Tarte citron façon Romana');
SELECT add_menu_element(130, 0, 'Tiramisu');
SELECT add_menu_element(131, 0, 'Panna cotta fruits rouges du moment');
SELECT add_menu_element(132, 0, 'Café ou thé gourmant');
SELECT add_menu_element(133, 0, 'Dessert du jour');
SELECT add_menu_element(134, 0, 'Angiolotti (à partager)', 'Beignets de pâte à pizza, sucre glace, nappés de nocciolata, éclats de pistache');
SELECT add_menu_element(135, 0, 'Coupe de glace 2 boules', 'Saveurs : vanille, café, chocolat, citron, fraise, framboise, rhum-raisin, menthe-chocolat');
SELECT add_menu_element(136, 0, 'Coupe de glace 3 boules', 'Saveurs : vanille, café, chocolat, citron, fraise, framboise, rhum-raisin, menthe-chocolat');

-- Softs
SELECT add_menu_element(137, 0, 'Coca‑Cola 33 cl');
SELECT add_menu_element(138, 0, 'Coca‑Cola Zéro 33 cl');
SELECT add_menu_element(139, 0, 'Orangina 25 cl');
SELECT add_menu_element(140, 0, 'Schweppes tonic 25 cl');
SELECT add_menu_element(141, 0, 'Schweppes agrumes 25 cl');
SELECT add_menu_element(142, 0, 'Limonade 25 cl');
SELECT add_menu_element(143, 0, 'Jus de fruits 25 cl', 'Saveurs : orange, ananas, abricot, pomme, tomate, pamplemousse');
SELECT add_menu_element(144, 0, 'Sirop à l’eau 25 cl', 'Saveurs : grenadine, fraise, framboise, cassis, citron, pêche, orgeat, menthe, violette, caramel, pamplemousse');
SELECT add_menu_element(145, 0, 'Evian 33 cl');
SELECT add_menu_element(146, 0, 'Evian 1 l');
SELECT add_menu_element(147, 0, 'Perrier 33 cl');
SELECT add_menu_element(148, 0, 'San Pellegrino 50 cl');
SELECT add_menu_element(149, 0, 'San Pellegrino 1 l');

-- Cocktails sans alcool
SELECT add_menu_element(150, 0, 'Virgin spritz', 'Sirop spritz, eau gazeuse, tranche d’orange');
SELECT add_menu_element(151, 0, 'Virgin mule', 'Sirop de canne, ginger beer, jus de citron vert');
SELECT add_menu_element(152, 0, 'Virgin mojito', 'Eau gazeuse, sucre de canne, menthe fraîche, citron vert');

-- Cocktails
SELECT add_menu_element(153, 0, 'Limoncello spritz', 'Limoncello, prosecco, eau gazeuse, tranche de citron');
SELECT add_menu_element(154, 0, 'Aperol spritz', 'Aperol, prosecco, eau gazeuse, tranche d’orange');
SELECT add_menu_element(155, 0, 'St‑Germain spritz', 'St‑Germain, prosecco, eau gazeuse, citron vert, menthe fraîche');
SELECT add_menu_element(156, 0, 'Mojito Bacardi Cuatro', 'Rhum ambré Bacardi, sucre de canne, eau gazeuse, menthe fraîche, citron vert');
SELECT add_menu_element(157, 0, 'French mule', 'Vodka Grey Goose, ginger beer, citron vert');
SELECT add_menu_element(158, 0, 'Caïpirinha', 'Cachaça, sucre de canne, citron vert');

-- Apéritifs
SELECT add_menu_element(159, 0, 'Bitter San Pellegrino 25 cl');
SELECT add_menu_element(160, 0, 'Ricard 2 cl');
SELECT add_menu_element(161, 0, 'Martini Rosso 4 cl');
SELECT add_menu_element(162, 0, 'Martini Bianco 4 cl');
SELECT add_menu_element(163, 0, 'Campari 4 cl');
SELECT add_menu_element(164, 0, 'Suze 4 cl');
SELECT add_menu_element(165, 0, 'Kir vin blanc 12 cl', 'Saveurs : cassis, pêche, mûre, framboise, châtaigne');
SELECT add_menu_element(166, 0, 'Kir vin royal 12 cl', 'Saveurs : cassis, pêche, mûre, framboise, châtaigne');

-- Bières Pressions
SELECT add_menu_element(167, 0, 'Birra Moretti 25 cl');
SELECT add_menu_element(168, 0, 'Birra Moretti 50 cl');
SELECT add_menu_element(169, 0, 'Picon 25 cl');
SELECT add_menu_element(170, 0, 'Picon 50 cl');
SELECT add_menu_element(171, 0, 'Monaco 25 cl');
SELECT add_menu_element(172, 0, 'Monaco 50 cl');
SELECT add_menu_element(173, 0, 'Panaché 25 cl');
SELECT add_menu_element(174, 0, 'Panaché 50 cl');

-- Bières Bouteilles
SELECT add_menu_element(175, 0, 'Mort Subite blanche 33 cl');
SELECT add_menu_element(176, 0, 'Galia IPA 33 cl');
SELECT add_menu_element(177, 0, 'Desperado 33 cl');
SELECT add_menu_element(178, 0, 'Heineken sans alcool 33 cl');

-- Alcools et Digestifs
SELECT add_menu_element(179, 0, 'Gin Gordon’s tonic');
SELECT add_menu_element(180, 0, 'Bombay Sapphire tonic');
SELECT add_menu_element(181, 0, 'Vodka Smirnoff');
SELECT add_menu_element(182, 0, 'Vodka Grey Goose');
SELECT add_menu_element(183, 0, 'Bailey’s');
SELECT add_menu_element(184, 0, 'Get 27 / 31');
SELECT add_menu_element(185, 0, 'J&B Blended');
SELECT add_menu_element(186, 0, 'Jameson Irlande');
SELECT add_menu_element(187, 0, 'Jack Daniel’s');
SELECT add_menu_element(188, 0, 'Nikka Japon');
SELECT add_menu_element(189, 0, 'Akachi Japon');
SELECT add_menu_element(190, 0, 'Limoncello');
SELECT add_menu_element(191, 0, 'Amaretto');
SELECT add_menu_element(192, 0, 'Génépi');
SELECT add_menu_element(193, 0, 'Chartreuse jaune ou verte'); -- <>
SELECT add_menu_element(194, 0, 'Grappa'); -- ?
SELECT add_menu_element(195, 0, 'Poire (eau‑de‑vie)'); -- ?
SELECT add_menu_element(196, 0, 'Cognac Hennessy');
SELECT add_menu_element(197, 0, 'Rhum Santa Teresa');
SELECT add_menu_element(198, 0, 'Rhum Diplomatico');
SELECT add_menu_element(199, 0, 'Rhum Don Papa');
SELECT add_menu_element(200, 0, 'Rhum Zacapa');

/* --------------------------------
    CATEGORISE
*/ --------------------------------

INSERT INTO categorise (categorie_id, produit_id, categorise_ordre, categorise_prix) VALUES

-- Pizzas (à emporter)
(50, 80, 1, 10),
(50, 81, 2, 12),
(50, 82, 3, 12),
(50, 83, 4, 13),
(50, 84, 5, 14),
(50, 85, 6, 15),
(50, 86, 7, 15),
(50, 87, 8, 15),
(50, 88, 9, 15),
(50, 89, 10, 15),
(50, 90, 11, 15),
(50, 91, 12, 15),
--(50, 92, 13, ),
(50, 93, 14, 18),
(50, 94, 15, 18),
(50, 95, 16, 18),

-- Suppléments
(52, 96, 1, 1.5),
(52, 97, 2, 2.5),
(52, 98, 3, 4.5),


-- Menu du jour
(53, 100, 1, 12),
(53, 101, 2, 15),
(53, 102, 3, 15),
(53, 103, 4, 18),

-- Antipasti
(54, 104, 1, 21),
(54, 105, 2, 14),
(54, 106, 3, 12),

-- Salades
(55, 107, 1, 18),
(55, 108, 2, 18),
(55, 109, 3, 18.9),
(55, 110, 4, 20),

-- Pizzas (sur place)
(56, 80, 1, 11),
(56, 81, 2, 13),
(56, 82, 3, 14),
(56, 83, 4, 14),
(56, 84, 5, 15),
--(56, 85, 6, ),
(56, 86, 7, 16),
(56, 87, 8, 16),
(56, 88, 9, 16),
(56, 89, 10, 16),
(56, 90, 11, 16),
(56, 91, 12, 16),
(56, 92, 13, 19),
(56, 93, 14, 19),
(56, 94, 15, 19),
(56, 95, 16, 19),

-- Suppléments
(57, 96, 1, 1.5),
(57, 97, 2, 2.5),
(57, 98, 3, 4.5),

-- Pasta et Risotto
(58, 111, 1, 18),
(58, 112, 2, 20),
(58, 113, 3, 19),
(58, 114, 4, 21),
(58, 115, 5, 21),
(58, 116, 6, 22),

-- Menu enfant
(59, 117, 1, 10),
(59, 118, 2, 10),
(59, 119, 3, 10),

-- Viande et poisson
(60, 120, 1, 30),
(60, 121, 2, 26),
(60, 122, 3, 28),
(60, 123, 4, 23),
(60, 124, 5, 21),
(60, 125, 6, 23),
(60, 126, 7, 23),

-- Fromages et desserts
(61, 127, 1, 12),
(61, 128, 2, 9),
(61, 129, 3, 9),
(61, 130, 4, 8.5),
(61, 131, 5, 7),
(61, 132, 6, 12),
(61, 133, 7, 6.5),
(61, 134, 8, 12),
(61, 135, 9, 6),
(61, 136, 10, 8.5),

-- Softs
(62, 137, 1, 4),
(62, 138, 2, 4),
(62, 139, 3, 4),
(62, 140, 4, 4),
(62, 141, 5, 4),
(62, 142, 6, 4),
(62, 143, 7, 4),
(62, 144, 8, 2.3),
(62, 145, 9, 4),
(62, 146, 10, 8),
(62, 147, 11, 4),
(62, 148, 12, 4.5),
(62, 149, 13, 8),

-- Cocktails sans alcool
(63, 150, 1, 7.5),
(63, 151, 2, 7.5),
(63, 152, 3, 7.5),

-- Cocktails
(64, 153, 1, 8.5),
(64, 154, 2, 8.5),
(64, 155, 3, 10),
(64, 156, 4, 9.5),
(64, 157, 5, 11),
(64, 158, 6, 8.5),

-- Apéritifs
(65, 159, 1, 4.2),
(65, 160, 2, 3.5),
(65, 161, 3, 4.5),
(65, 162, 4, 4.5),
(65, 163, 5, 6.5),
(65, 164, 6, 4.5),
(65, 165, 7, 5.8),
(65, 166, 8, 12),

-- Bières Pressions
(66, 167, 1, 3.5),
(66, 168, 2, 7),
(66, 169, 3, 4.5),
(66, 170, 4, 9),
(66, 171, 5, 3.8),
(66, 172, 6, 7.6),
(66, 173, 7, 3.5),
(66, 174, 8, 7),
(66, 99, 9, 0.3),

-- Bières Bouteilles
(67, 175, 1, 7),
(67, 176, 2, 7),
(67, 177, 3, 7),
(67, 178, 4, 7),

-- Alcools et Digestifs
(68, 179, 1, 8),
(68, 180, 2, 12),
(68, 181, 3, 8),
(68, 182, 4, 11),
(68, 183, 5, 8),
(68, 184, 6, 8),
(68, 185, 7, 8),
(68, 186, 8, 10),
(68, 187, 9, 10),
(68, 188, 10, 14),
(68, 189, 11, 14),
(68, 190, 12, 8),
(68, 191, 13, 9),
(68, 192, 14, 9),
(68, 193, 15, 14), -- <
(68, 194, 16, 9), -- ?
(68, 195, 17, 9), -- ?
(68, 196, 18, 10),
(68, 197, 19, 14),
(68, 198, 20, 14),
(68, 199, 21, 14),
(68, 200, 22, 16);