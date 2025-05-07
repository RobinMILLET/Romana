/* --------------------------------
    DATABASE FOR "La Romana"
    SGBD: PostgreSQL 16
*/ --------------------------------

SET search_path = romana;

/* --------------------------------
    SEQUENCES
*/ --------------------------------

ALTER SEQUENCE sq_fermeture RESTART;
ALTER SEQUENCE sq_historique RESTART;
ALTER SEQUENCE sq_horaire RESTART;
ALTER SEQUENCE sq_reservation RESTART;
ALTER SEQUENCE sq_personnel RESTART;

/* --------------------------------
    DELETE
*/ --------------------------------

DELETE FROM horaire;
DELETE FROM fermeture;
DELETE FROM reservation;
DELETE FROM statut;
DELETE FROM historique;
DELETE FROM personnel;

/* --------------------------------
    HORAIRE
*/ --------------------------------

INSERT INTO horaire (horaire_date_debut, horaire_date_fin,
horaire_temps_debut, horaire_temps_fin, horaire_couverts) VALUES
(1, 31, '12:00:00', '14:00:00', 75),
(1, 31, '19:00:00', '22:00:00', 75),
(1, 31, '19:00:00', '23:00:00', 100);

INSERT INTO jour (horaire_id, jour_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6);

INSERT INTO mois (horaire_id, mois_id) VALUES
(3, 7), (3, 8);

INSERT INTO fermeture (fermeture_debut, fermeture_fin, fermeture_couverts) VALUES
('2025-12-15', '2026-01-15', 0);

/* --------------------------------
    PERSONNEL
*/ --------------------------------

INSERT INTO typepermission (typepermission_id, typepermission_clef,
typepermission_libelle, typepermission_description) VALUES
(1, 'SUPER', 'Superutilisateur', 'Octroie tous les droits au porteur, peut uniquement être géré par un autre Superutilisateur. Attention, ce rôle est dangereux !'),
(2, 'ADMIN', 'Administrateur', 'Donne le droit de modifier les constantes, les horaires d''ouverture et les infos importantes.'),
(3, 'MANAG', 'Management', 'Permet de créer et supprimer des utilisateurs, de modifier les permissions et de réinitialiser les mots de passes.'),
(4, 'COMMU', 'Communication', 'Modifie la vitrine du site, les pages d''accueil et la présentation du restaurant.'),
(5, 'ACTUS', 'Actualités', 'Peut créer, modifier et supprimer les événements, ainsi que le plât du jour.'),
(6, 'PHOTO', 'Illustrateur', 'Ajoute, supprimme et assigne des photos/images pour allimenter le site.'),
(7, 'RESTO', 'Restauration', 'Gestion des menus, des produits et des prix des consommations.'),
(8, 'ALLER', 'Allergies', 'Chargé de signaler la présence d''allergènes dans les plâts.'),
(9, 'RESER', 'Reservation', 'Peut consulter et gérer les réservations, ainsi qu''en créer de nouvelles.'),
(10, 'SERVE', 'Service', 'Accès en lecure seule sur les réservations (pour le service en salle).'),
(11, 'HISTO', 'Historique', 'Peut consulter l''historique des actions.');

INSERT INTO personnel (personnel_nom, personnel_mdp, personnel_mdp_estdefaut, personnel_mdp_changement) VALUES
('Robin', '$2y$10$qonZ9CVleo46t.7gwLVMFOSCwDGtZ/PuZgXZ6oKQSuMxdAuli7ZA6', FALSE, now()), -- Robin
('Main', '$2y$10$sklPKLJsHHU7bq5PGZuWxO3YEYlNhJmidJclpaF0F1QB0YBHd7kKO', FALSE, now()), -- Main
('User', '$2y$10$PS/EohdIpQVyF8.lTCtKb.NJdwq62FHq9/YtlJVthKFZxsqyL2RzW', TRUE, now()), -- mdp
('Serv', '$2y$10$TgbvJ3sbkOtxYVUhaqTnMuzW5w0uP3fUW0vrnD7Uq297ZI1L7SeKK', FALSE, now()); -- Serv

INSERT INTO permission (personnel_id, typepermission_id) VALUES
(1, 1),
(2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 11),
(3, 10), (3, 11),
(4, 5), (4, 9), (4, 10), (4, 11);

/* --------------------------------
    RESERVATION
*/ --------------------------------

INSERT INTO statut (statut_id, statut_libelle, statut_hex) VALUES
(1, 'En attente', 'cccccc'),    -- Blanc
(2, 'En approche', '77cc77'),   -- Vert
(3, 'En cours', 'cccc77'),      -- Jaune
(4, 'En retard', 'cc7777'),     -- Rouge
(5, 'Absent', 'cc77cc'),        -- Violet
(6, 'Annulé', '777777'),        -- Noir
(7, 'En salle', '77cccc'),      -- Cyan
(8, 'Terminé', '7777cc');       -- Bleu

INSERT INTO reservation (statut_id, personnel_id, reservation_num,
reservation_nom, reservation_prenom, reservation_telephone, reservation_personnes,
reservation_creation, reservation_horaire, reservation_anonymiser) VALUES
(1, 1, '4V4LYNRP', NULL, 'Lys', '0123456789', 4, now(), now() + INTERVAL '90 minute', FALSE),
(2, 4, '3ZZ4M4RI', 'EZZAMARI', 'Sofiane', '1234567890', 2, now(), now() + INTERVAL '30 minute', FALSE),
(3, NULL, '53BUL0NP', 'ZEBULON', 'Moustache', '2345678901', 10, now(), now() - INTERVAL '5 minute', FALSE),
(4, 4, '3C0N0MY1', 'YOHANN', 'Gaillard', '3456789012', 1, now(), now() - INTERVAL '15 minute', FALSE),
(5, NULL, 'J3SUSG0D', 'JESUS', 'Chuy', '4567890123', 7, now(), now() - INTERVAL '45 minute', FALSE),
(6, NULL, 'L0LXPTDR', 'MICHEL', 'Jean', '5678901234', 20, now(), now() - INTERVAL '60 minute', TRUE),
(7, 1, 'R0B1NM74', 'MILLET', 'Robin', '6789012345', 1, now(), now() - INTERVAL '30 minute', FALSE),
(8, 4, 'L0L0FL3G', 'GRAGAS', NULL, '7890123456', 5, now(), now() - INTERVAL '120 minute', FALSE);
