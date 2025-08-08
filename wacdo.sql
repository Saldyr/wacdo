-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 08 août 2025 à 12:48
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `wacdo`
--

-- --------------------------------------------------------

--
-- Structure de la table `boisson`
--

DROP TABLE IF EXISTS `boisson`;
CREATE TABLE IF NOT EXISTS `boisson` (
  `boisson_id` int NOT NULL AUTO_INCREMENT,
  `boisson_nom` varchar(100) NOT NULL,
  `boisson_description` varchar(255) DEFAULT NULL,
  `boisson_prix` decimal(8,2) NOT NULL DEFAULT '0.00',
  `boisson_image_url` varchar(255) DEFAULT NULL,
  `boisson_disponibilite` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`boisson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `boisson`
--

INSERT INTO `boisson` (`boisson_id`, `boisson_nom`, `boisson_description`, `boisson_prix`, `boisson_image_url`, `boisson_disponibilite`) VALUES
(1, 'L’Open Space (thé glacé)', 'Pour rester frais même en plein rush !', 2.50, '', 1),
(2, 'La Power Limonade', 'La boisson qui booste plus qu’une réunion motivante !', 3.50, '', 1),
(3, 'La Zénitude', 'La boisson qui te met en mode avion, même au bureau.', 2.50, '', 1),
(4, 'La Frapp’Attitude', 'Un coup de frais pour affronter tous les dossiers.', 2.50, '', 1),
(10, 'Le Codeur', NULL, 3.50, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_nom` varchar(50) NOT NULL,
  `category_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`category_id`, `category_nom`, `category_description`) VALUES
(1, 'Les Burgers', 'Chez nous, le burger n’est pas qu’un simple sandwich : c’est une expérience (presque) philosophique ! Recettes maison, ingrédients ultra frais, et pain moelleux à tomber… Bref, de quoi rendre jaloux même les hot-dogs ! Osez l’aventure.'),
(2, 'Hot-dogs', 'Préparez-vous à réconcilier l’Amérique et votre estomac ! Nos hot-dogs, c’est la promesse d’une saucisse savoureuse, d’un pain moelleux, et d’une avalanche de toppings à personnaliser selon vos envies. Classique, cheesy, ou carrément déjanté.'),
(3, 'Sandwichs', 'Le sandwich, c’est l’ami fidèle de toutes les faims, grandes ou petites. Chez nous, ils sont garnis généreusement, préparés à la minute et toujours prêts à vous sauver d’un coup de fringale ! Entre deux pains moelleux, retrouvez des recettes classiques.'),
(5, 'Snacks', 'À grignoter à tout moment : nos snacks malins régalent petits creux et grandes faims, toujours avec gourmandise.'),
(6, 'Drinks', 'Des boissons fraîches et pétillantes pour accompagner chaque pause avec peps et bonne humeur.'),
(7, 'Desserts', 'La touche sucrée qu’on attend tous : desserts gourmands et légers pour finir sur une note parfaite.');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `order_date_commande` date NOT NULL,
  `order_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_heure_livraison` time DEFAULT NULL,
  `order_statut_commande` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `order_numero_ticket` varchar(20) NOT NULL,
  `order_type` enum('sur_place','a_emporter','livraison') NOT NULL DEFAULT 'sur_place',
  `user_id` int NOT NULL,
  `livreur_id` int DEFAULT NULL,
  `boisson_id` int DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `fk_commande_user` (`user_id`),
  KEY `fk_commande_boisson` (`boisson_id`),
  KEY `fk_commande_livreur` (`livreur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`order_id`, `order_date_commande`, `order_created_at`, `order_heure_livraison`, `order_statut_commande`, `order_numero_ticket`, `order_type`, `user_id`, `livreur_id`, `boisson_id`) VALUES
(119, '2025-07-30', '2025-08-02 08:42:26', NULL, 'servie', '002', 'a_emporter', 17, NULL, NULL),
(120, '2025-07-30', '2025-08-02 08:42:26', NULL, 'livree', '003', 'livraison', 17, 18, NULL),
(121, '2025-07-30', '2025-08-02 08:42:26', NULL, 'servie', '004', 'sur_place', 17, NULL, NULL),
(122, '2025-07-30', '2025-08-02 08:42:26', NULL, 'livree', '005', 'livraison', 17, 18, NULL),
(123, '2025-07-30', '2025-08-02 08:42:26', NULL, 'servie', '006', 'sur_place', 17, NULL, NULL),
(124, '2025-08-02', '2025-08-02 08:42:26', NULL, 'servie', '001', 'sur_place', 17, NULL, NULL),
(125, '2025-08-02', '2025-08-02 08:50:09', NULL, 'livree', '002', 'livraison', 17, 18, NULL),
(133, '2025-08-06', '2025-08-06 11:44:17', NULL, 'servie', '001', 'sur_place', 17, NULL, NULL),
(136, '2025-08-06', '2025-08-06 14:06:53', NULL, 'delivered', 'TEST123', 'sur_place', 10, NULL, 3),
(137, '2025-08-08', '2025-08-08 14:40:40', NULL, 'servie', '001', 'a_emporter', 34, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commande_boisson`
--

DROP TABLE IF EXISTS `commande_boisson`;
CREATE TABLE IF NOT EXISTS `commande_boisson` (
  `commande_boisson_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `boisson_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`commande_boisson_id`),
  KEY `order_id` (`order_id`),
  KEY `boisson_id` (`boisson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_boisson`
--

INSERT INTO `commande_boisson` (`commande_boisson_id`, `order_id`, `boisson_id`, `quantity`) VALUES
(88, 123, 2, 1),
(89, 123, 1, 1),
(90, 123, 3, 1),
(91, 123, 10, 1),
(93, 125, 10, 1),
(97, 133, 1, 1),
(102, 136, 3, 2),
(103, 136, 4, 1),
(104, 137, 2, 1),
(105, 137, 10, 1);

-- --------------------------------------------------------

--
-- Structure de la table `commande_menu`
--

DROP TABLE IF EXISTS `commande_menu`;
CREATE TABLE IF NOT EXISTS `commande_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `order_menu_quantite` int NOT NULL DEFAULT '1',
  `menu_boisson_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cm_order` (`order_id`),
  KEY `fk_cm_menu` (`menu_id`),
  KEY `fk_menu_boisson` (`menu_boisson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=148219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_menu`
--

INSERT INTO `commande_menu` (`id`, `order_id`, `menu_id`, `order_menu_quantite`, `menu_boisson_id`) VALUES
(148170, 119, 2, 1, 2),
(148171, 119, 2, 1, NULL),
(148172, 119, 2, 1, 2),
(148173, 119, 2, 1, NULL),
(148174, 120, 3, 1, 10),
(148175, 120, 3, 1, NULL),
(148176, 120, 3, 1, 10),
(148177, 120, 3, 1, NULL),
(148203, 122, 2, 1, 1),
(148204, 122, 2, 1, 3),
(148205, 123, 2, 1, 3),
(148206, 124, 2, 1, 2),
(148208, 125, 3, 1, 3),
(148214, 133, 2, 1, 1),
(148215, 133, 3, 1, 3),
(148217, 136, 2, 1, NULL),
(148218, 137, 2, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `commande_produit`
--

DROP TABLE IF EXISTS `commande_produit`;
CREATE TABLE IF NOT EXISTS `commande_produit` (
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `order_product_quantite` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`order_id`,`product_id`),
  KEY `fk_cp_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_produit`
--

INSERT INTO `commande_produit` (`order_id`, `product_id`, `order_product_quantite`) VALUES
(121, 1, 1),
(121, 4, 1),
(121, 5, 1),
(123, 1, 1),
(123, 7, 1),
(123, 10, 1),
(123, 16, 1),
(123, 20, 1),
(125, 8, 1),
(125, 11, 1),
(125, 20, 1),
(133, 1, 1),
(133, 4, 1),
(133, 7, 1),
(133, 10, 1),
(133, 16, 1),
(133, 19, 1),
(137, 5, 1),
(137, 9, 1),
(137, 16, 1);

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `menu_nom` varchar(100) NOT NULL,
  `menu_description` varchar(255) DEFAULT NULL,
  `menu_prix` decimal(8,2) NOT NULL,
  `menu_image_url` varchar(255) DEFAULT NULL,
  `menu_disponibilite` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `menu`
--

INSERT INTO `menu` (`menu_id`, `menu_nom`, `menu_description`, `menu_prix`, `menu_image_url`, `menu_disponibilite`) VALUES
(2, 'L\'intello Gourmand', '', 12.70, '', 1),
(3, 'L\'audacieux Comfort', '', 12.50, '', 1),
(4, 'L’Executive Express', '', 12.60, '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `menu_produit`
--

DROP TABLE IF EXISTS `menu_produit`;
CREATE TABLE IF NOT EXISTS `menu_produit` (
  `menu_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`product_id`),
  KEY `fk_mp_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `menu_produit`
--

INSERT INTO `menu_produit` (`menu_id`, `product_id`) VALUES
(2, 1),
(3, 4),
(4, 9),
(2, 16),
(4, 16),
(3, 17),
(2, 18),
(3, 19),
(4, 20);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `product_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `product_prix` decimal(8,2) NOT NULL,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_disponibilite` tinyint(1) NOT NULL DEFAULT '1',
  `category_id` int NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `fk_category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`product_id`, `product_nom`, `product_description`, `product_prix`, `product_image_url`, `product_disponibilite`, `category_id`) VALUES
(1, 'Le Big Brain Burger', 'Un burger qui met tout le monde d’accord ! Savourez un steak juteux de bœuf français, sublimé par une généreuse tranche de cheddar fondant, des oignons confits maison, de la roquette croquante et une sauce crémeuse légèrement épicée. Le tout niché dans un', 8.50, '', 1, 1),
(4, 'Le RH (Raclette & Hamburger)', 'Parce que les amateurs de raclette savent que c’est une passion qui ne s’arrête pas à l’hiver, découvrez Le RH : un steak juteux, une généreuse couche de raclette fondante, des pommes de terre dorées et des oignons caramélisés, le tout dans un pain brioch', 8.50, '', 1, 1),
(5, 'Le Manager Miam', 'Le boss des burgers, celui qui mène la team gourmande à la baguette ! Imaginez : un steak de bœuf généreux, du bacon croustillant, du cheddar coulant, une touche de sauce secrète et quelques crudités bien croquantes pour la fraîcheur. Dans son pain moelle', 9.80, '', 1, 1),
(6, 'Le Classic Patron', 'Le burger qui ne fait jamais de grève et qui tient toujours ses promesses ! Un steak savoureux, du fromage fondant, de la salade croquante, des tomates fraîches et une pointe de sauce maison : tout ce qu’il faut pour plaire aux amateurs de valeurs sûres. ', 7.50, '', 1, 1),
(7, 'Le Hot Collègue', 'Le collègue qu’on aimerait avoir à la pause déjeuner ! Une saucisse grillée à la perfection, des oignons croustillants, une pointe de moutarde qui réveille, et une avalanche de cheddar fondant, le tout niché dans un pain tout chaud. Avec Le Hot Collègue, ', 5.80, '', 1, 2),
(8, 'Le Dog’ument Officiel', 'Ici, c’est le hot-dog qui fait foi ! Saucisse premium, oignons dorés, pickles croquants et sauce signature soigneusement validée… Tout est réuni pour une expérience certifiée gourmande. Ce Dog’ument Officiel a le tampon de la pause parfaite : impossible d', 6.50, '', 1, 2),
(9, 'Le CEO Sauci', 'Le patron des hot-dogs, celui qui prend les décisions les plus gourmandes ! Saucisse premium, avalanche de sauces maison, cheddar coulant et oignons croustillants, le tout dans un pain ultra moelleux. Avec Le CEO Sauci, c’est le leadership par le goût : c', 8.00, '', 1, 2),
(10, 'Le Briefing Baguette', 'La réunion la plus croustillante de la journée ! Retrouvez tout le savoir-faire français dans ce sandwich baguette généreusement garni : jambon de qualité, fromage affiné, salade fraîche et petite touche de beurre demi-sel. Un vrai classique revisité pour', 4.50, '', 1, 3),
(11, 'Le Président Panini', 'Le panini qui prend le pouvoir sur votre faim ! Pain doré à souhait, jambon italien, mozzarella fondante, tomates confites et une pointe de pesto maison : chaque bouchée vous fait voter pour la gourmandise. Avec Le Président Panini, c’est la pause qui dev', 4.50, '', 1, 3),
(16, 'Les Frites du Conseil', 'Des frites croustillantes, dorées à souhait et toujours de bon conseil pour accompagner tous vos plats. À picorer sans hésiter en réunion… ou juste par gourmandise !', 3.50, '', 1, 5),
(17, 'Les Nuggets de la Réu', 'Petites bouchées dorées, tendres à l’intérieur, parfaites pour dynamiser toutes vos réunions. Les Nuggets de la Réu : à partager… ou à garder rien que pour soi !', 3.50, '', 1, 5),
(18, 'Le Nuage Choco-Vanille', 'Un duo aérien de mousse au chocolat et crème vanille, aussi léger qu’un nuage, pour une pause tout en douceur.', 3.00, '', 1, 7),
(19, 'Le Délice Croquant', 'Un dessert qui allie le fondant et le croustillant, avec un cœur chocolat et des éclats de biscuit, irrésistible à chaque bouchée.', 3.00, '', 1, 7),
(20, 'La Douceur Fruitée', 'Un mélange frais de fruits de saison et de crème légère, parfait pour finir le repas sur une note vitaminée et gourmande.', 3.40, '', 1, 7);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_nom` varchar(30) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`role_id`, `role_nom`, `role_description`) VALUES
(1, 'Administrateur', 'Accès complet au back-office'),
(2, 'Préparateur', 'Peut voir et valider les commandes'),
(3, 'Accueil', 'Peut saisir et remettre les commandes'),
(4, 'Livreur', 'Livre les commandes aux clients et changer le statut'),
(5, 'Client', 'Peut passer des commandes et suivre leur statut');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_nom` varchar(50) NOT NULL,
  `user_prenom` varchar(50) NOT NULL,
  `user_mail` varchar(100) NOT NULL,
  `user_password` varchar(72) NOT NULL,
  `user_date_creation` date NOT NULL,
  `role_id` int NOT NULL,
  `consentement` tinyint(1) NOT NULL DEFAULT '0',
  `date_consentement` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  KEY `fk_utilisateur_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`user_id`, `user_nom`, `user_prenom`, `user_mail`, `user_password`, `user_date_creation`, `role_id`, `consentement`, `date_consentement`, `is_active`) VALUES
(9, 'admin', 'Test', 'admin@exemple.com', '$2y$10$j.KsEGZmsmnXrq0QZwP3t.aA1RbnmzH11CEEM.aosAgV1eVgMniT6', '2025-07-20', 1, 0, NULL, 1),
(10, 'Prep', 'Test', 'prep@exemple.com', '$2y$12$LyLFCPeBsYaFeBlBXWAkVO1Fpmc4Xo1l2Sjbu1F4o/DXlLK/sz29m', '2025-07-20', 2, 0, NULL, 1),
(11, 'Accueil', 'Test', 'accueil@exemple.com', '$2y$12$DzPUNMyOwLCcze7.WPJLu.XL2dLjpcyNCxP8lv8QBNr2myZryeZMa', '2025-07-20', 3, 0, NULL, 1),
(17, 'Anonyme', 'Anonyme', 'user+17@anonymise.local', '$2y$10$thKXGzPIi4TfxwUQ0BRERe9bNULrtH4PzivMLPzgKqa3aGhIeHdFC', '2025-07-27', 5, 0, NULL, 0),
(18, 'test', 'Ilies', 'livreur@test.fr', '$2y$10$xhYJRm4Rad5CUMwJFXdQOuc1IpdhVHnXYw5.N4BW.HBU2vphyjiTW', '2025-07-28', 4, 0, NULL, 1),
(26, 'Anonyme', 'Anonyme', 'user+26@anonymise.local', '$2y$10$wnzaLYDvAlEFRA7rUuQdg.mWFsgilzW3Cw/pZrXQjHVvvxOayvtja', '2025-08-06', 5, 0, NULL, 0),
(27, 'Anonyme', 'Anonyme', 'user+27@anonymise.local', '$2y$10$UobCaFC5fK1/FGJozCppKuP2rR2grsngLPz8V5pudLg7XUXH5Kr3q', '2025-08-06', 5, 0, NULL, 0),
(28, 'Anonyme', 'Anonyme', 'user+28@anonymise.local', '$2y$10$eH5RXGV8jv74xhKxA0Zq2.qGEqZ/b9MXPH0aFqh.sl2STXoETHndq', '2025-08-06', 5, 0, NULL, 0),
(29, 'Anonyme', 'Anonyme', 'user+29@anonymise.local', '$2y$10$HGkf9LK6n78f0BpRs.H2aOzXWjpiY2P/D1dxmCEPh/uzrGfWjmLsy', '2025-08-06', 5, 0, NULL, 0),
(30, 'Anonyme', 'Anonyme', 'user+30@anonymise.local', '$2y$10$DXOvvS1ifkc57v5aU0u3pOcBC6mhs2FtqAEvWDK1t16C/PF0f8XG2', '2025-08-06', 5, 0, NULL, 0),
(31, 'Dupont', 'Jean', 'j.dupont@example.com', '$2y$12$g.8hk6DmGlyAg3Lha21bdOshe2beV53ehGR9TrqtzwBkCcx1PZQZ2', '2025-08-07', 5, 1, '2025-08-07 10:39:04', 1),
(32, 'B', 'A', 'a.b@example.com', '$2y$12$Iz8JqmKHPJ8jfkEkZj3ImejZemYlZgU2TQEgQAoEFn48X00T.pmdO', '2025-08-07', 5, 0, NULL, 1),
(33, 'Anonyme', 'Anonyme', 'user+33@anonymise.local', '$2y$10$dcJd2kNmOBDKmVh.CiUXKe95jcbF0DhalCNgCCntHcpZ7ZyD8ATHm', '2025-08-07', 5, 0, NULL, 0),
(34, 'Test', 'Client', 'test@client.fr', '$2y$10$sQAfRqh4SqHMF.0bYxJAQO4Ip5W/DlP7lbc0GoBnwCLGpMnA8gY9W', '2025-08-08', 5, 1, '2025-08-08 12:40:01', 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `fk_commande_boisson` FOREIGN KEY (`boisson_id`) REFERENCES `boisson` (`boisson_id`),
  ADD CONSTRAINT `fk_commande_livreur` FOREIGN KEY (`livreur_id`) REFERENCES `utilisateur` (`user_id`),
  ADD CONSTRAINT `fk_commande_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`user_id`);

--
-- Contraintes pour la table `commande_boisson`
--
ALTER TABLE `commande_boisson`
  ADD CONSTRAINT `commande_boisson_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `commande` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_boisson_ibfk_2` FOREIGN KEY (`boisson_id`) REFERENCES `boisson` (`boisson_id`);

--
-- Contraintes pour la table `commande_menu`
--
ALTER TABLE `commande_menu`
  ADD CONSTRAINT `fk_cm_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`),
  ADD CONSTRAINT `fk_cm_order` FOREIGN KEY (`order_id`) REFERENCES `commande` (`order_id`),
  ADD CONSTRAINT `fk_menu_boisson` FOREIGN KEY (`menu_boisson_id`) REFERENCES `boisson` (`boisson_id`);

--
-- Contraintes pour la table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD CONSTRAINT `fk_cp_order` FOREIGN KEY (`order_id`) REFERENCES `commande` (`order_id`),
  ADD CONSTRAINT `fk_cp_product` FOREIGN KEY (`product_id`) REFERENCES `produit` (`product_id`);

--
-- Contraintes pour la table `menu_produit`
--
ALTER TABLE `menu_produit`
  ADD CONSTRAINT `fk_mp_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`),
  ADD CONSTRAINT `fk_mp_product` FOREIGN KEY (`product_id`) REFERENCES `produit` (`product_id`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`category_id`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
