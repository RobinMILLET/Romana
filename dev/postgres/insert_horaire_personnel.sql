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

--DELETE FROM planning;
DELETE FROM horaire;
DELETE FROM fermeture;
DELETE FROM reservation;
DELETE FROM statut;
DELETE FROM historique;
DELETE FROM personnel;
DELETE FROM typepermission;

/* --------------------------------
    HORAIRE
*/ --------------------------------

INSERT INTO horaire (horaire_date_debut, horaire_date_fin,
horaire_temps_debut, horaire_temps_fin, horaire_couverts) VALUES
(1, 31, '12:00:00', '14:00:00', 50),
(1, 31, '19:00:00', '22:00:00', 50),
(1, 31, '18:00:00', '22:00:00', 50);

INSERT INTO jour (horaire_id, jour_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6),
(3, 7);

/* --------------------------------
    PERSONNEL
*/ --------------------------------

INSERT INTO typepermission (typepermission_id, typepermission_clef,
typepermission_libelle, typepermission_description) VALUES
(0, 'LOGIN', 'Connexion', 'A le droit de se connecter à l''interface du personnel.'),
(1, 'SUPER', 'Superutilisateur', 'Octroie tous les droits au porteur, peut uniquement être géré par un autre Superutilisateur. Attention, ce rôle est dangereux !'),
(2, 'ADMIN', 'Administrateur', 'Donne le droit de modifier les constantes, les horaires d''ouverture et les infos importantes.'),
(3, 'MANAG', 'Management', 'Permet de créer et supprimer des utilisateurs, de modifier les permissions et de réinitialiser les mots de passes.'),
(4, 'COMMU', 'Communication', 'Modifie la vitrine du site, les pages d''accueil et la présentation du restaurant.'),
(5, 'ACTUS', 'Actualités', 'Peut créer, modifier et supprimer les événements.'),
(6, 'PHOTO', 'Illustrateur', 'Ajoute, supprimme et assigne des photos/images pour allimenter le site.'),
(7, 'RESTO', 'Restauration', 'Gestion des menus, des produits et des prix des consommations.'),
(8, 'ALLER', 'Allergies', 'Chargé de signaler la présence d''allergènes dans les plâts.'),
(9, 'RESER', 'Reservation', 'Peut consulter et gérer les réservations, ainsi qu''en créer de nouvelles.'),
(10, 'SERVE', 'Service', 'Accès en lecure seule sur les réservations (pour le service en salle).'),
(11, 'HISTO', 'Historique', 'Peut consulter l''historique des actions.'),
(12, 'PERSO', 'Individuel', 'Peut modifier ou supprimer son compte, et changer le mot de passe même s''il est encore valide');

INSERT INTO personnel (personnel_nom, personnel_mdp) VALUES
('Robin', '$2y$10$qonZ9CVleo46t.7gwLVMFOSCwDGtZ/PuZgXZ6oKQSuMxdAuli7ZA6'), -- Robin
('Main', '$2y$10$sklPKLJsHHU7bq5PGZuWxO3YEYlNhJmidJclpaF0F1QB0YBHd7kKO'), -- Main
('User', '$2y$10$PS/EohdIpQVyF8.lTCtKb.NJdwq62FHq9/YtlJVthKFZxsqyL2RzW'), -- mdp
('Serv', '$2y$10$TgbvJ3sbkOtxYVUhaqTnMuzW5w0uP3fUW0vrnD7Uq297ZI1L7SeKK'); -- Serv

INSERT INTO permission (personnel_id, typepermission_id) VALUES
(1, 1),
(2, 0), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 11), (2, 12),
(3, 0), (3, 10), (3, 11), (3, 12),
(4, 0), (4, 5), (4, 9), (4, 10), (4, 11);

/* --------------------------------
    RESERVATION
*/ --------------------------------

INSERT INTO statut (statut_id, statut_libelle, statut_hex) VALUES
(1, 'En attente', '77cc77'),    -- Vert
(2, 'En cours', '77cccc'),      -- Cyan
(3, 'Terminée', '7777cc'),      -- Bleu
(4, 'Archivée', 'cccc77'),      -- Jaune
(5, 'Anonymisée', 'cc77cc'),    -- Violet
(6, 'Annulée', 'cc7777'),       -- Rouge