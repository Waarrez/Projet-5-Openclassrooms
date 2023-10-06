-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 06 oct. 2023 à 10:12
-- Version du serveur : 10.6.5-MariaDB
-- Version de PHP : 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `blog_php`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `publishedAt` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id`, `title`, `content`, `publishedAt`, `author_id`) VALUES
(14, 'aaa', 'aaaa', '2023-09-29 00:00:00', 50),
(11, 'Modifié 2', 'test', '2023-09-29 00:00:00', 47),
(10, 'Test 2 écrit', 'Salut le monde 2\r\n', '2023-09-21 00:00:00', 47),
(13, 'DZDDZA', 'DZADZAADZ', '2023-09-29 00:00:00', 47);

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `postedAt` datetime NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `validate` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `content`, `postedAt`, `article_id`, `author_id`, `validate`) VALUES
(10, 'Salut', '2023-09-21 00:00:00', 12, 47, 1),
(11, 'S', '2023-09-21 00:00:00', 12, 47, 1),
(12, 'Salut Test amasya', '2023-09-21 00:00:00', 12, 48, 1),
(13, 'sasaaaa', '2023-09-29 00:00:00', 11, 50, 0),
(14, 'aaaaa', '2023-09-29 00:00:00', 14, 50, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmAccount` varchar(255) DEFAULT NULL,
  `roles` varchar(255) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `confirmAccount`, `roles`, `file`, `content`, `pdf`, `twitter`, `github`, `linkedin`) VALUES
(47, 'Warez', 'thimote.cabotte6259@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$aTZuNUVNU2JJV3pFZ3c4Nw$I2fSxM9IOYXSwx6JDVr/zDZOLqDBVESMlOU6pGXiQTA', NULL, 'ROLE_ADMIN', 'image.png', 'Warez, un putain de développeur', 'CVThimoté.pdf', 'https://twitter.com/?lang=fr', 'https://github.com/Waarrez', 'https://fr.linkedin.com/?original_referer=https%3A%2F%2Fwww.google.com%2F'),
(50, 'Warez', 'devyourwebsite@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$UEE1TjJ2Tkh0TWI2WExPTw$NI3bmLAX4E0hC3Y31DXRhW5X/kYwBnanPsZaQkKBAQE', NULL, 'ROLE_USER', 'Array', 'Salut le monde', 'CV Thimoté.pdf', 'https://twitter.com/?lang=fr', 'https://github.com/Waarrez', 'https://fr.linkedin.com/?original_referer=https%3A%2F%2Fwww.google.com%2F');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
