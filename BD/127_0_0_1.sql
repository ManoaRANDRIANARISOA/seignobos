-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 27 jan. 2026 à 09:43
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
-- Base de données : `seignobos_db`
--
CREATE DATABASE IF NOT EXISTS `seignobos_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `seignobos_db`;

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_description` text,
  `form_data` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('En attente','En cours','Fini') DEFAULT 'En attente',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `project_name`, `project_description`, `form_data`, `created_at`, `statut`) VALUES
(42, 0, 'PROJECT 1', '', '[{\"name\":\"CHAPITRES :\",\"type\":\"checkbox\",\"options\":[\"CHAPITRE 1\",\"CHAPITRE 2\",\"CHAPITRE 3\",\"Autre\"],\"allowOther\":true,\"allowAI\":false},{\"name\":\"Années concernées :\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"COMENTAIRES\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"INTERETS\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"REFERENCES\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":false},{\"name\":\"PAGES\",\"type\":\"number\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"IMAGES\",\"type\":\"file\",\"options\":[],\"allowOther\":true,\"allowAI\":false},{\"name\":\"TAGS\",\"type\":\"text\",\"options\":[],\"allowOther\":true,\"allowAI\":true}]', '2026-01-20 06:02:45', 'Fini'),
(43, 0, 'PROJECT 2', '', '[{\"name\":\"THEMES\",\"type\":\"select\",\"options\":[\"THEMES 1\",\"THEMES 2\",\"THEMES 3\",\"Autre\"],\"allowOther\":true,\"allowAI\":false},{\"name\":\"RESUMES\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"COMMENTAIRE\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"PAGES\",\"type\":\"number\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"UPLOAD\",\"type\":\"file\",\"options\":[],\"allowOther\":true,\"allowAI\":false}]', '2026-01-20 06:04:14', 'En cours'),
(46, 0, 'PROJECT 3', 'JE VIEN de changer', '[{\"name\":\"UPLOAD\",\"type\":\"file\",\"allowOther\":true,\"options\":[],\"allowAI\":false},{\"name\":\"CHAPITRES\",\"type\":\"select\",\"allowAI\":true,\"allowOther\":true,\"options\":[\"CHAP 1\",\"CHAP 2\",\"CHAP 3\",\"Autre\"]}]', '2026-01-23 06:33:37', 'En cours');

-- --------------------------------------------------------

--
-- Structure de la table `project_42`
--

DROP TABLE IF EXISTS `project_42`;
CREATE TABLE IF NOT EXISTS `project_42` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `CHAPITRES__` text,
  `CHAPITRES___other` text,
  `Ann__es_concern__es__` text,
  `Ann__es_concern__es___other` text,
  `COMENTAIRES` text,
  `COMENTAIRES_other` text,
  `INTERETS` text,
  `INTERETS_other` text,
  `REFERENCES` text,
  `REFERENCES_other` text,
  `PAGES` text,
  `PAGES_other` text,
  `IMAGES` text,
  `IMAGES_other` text,
  `TAGS` text,
  `TAGS_other` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `project_42`
--

INSERT INTO `project_42` (`id`, `submitted_at`, `CHAPITRES__`, `CHAPITRES___other`, `Ann__es_concern__es__`, `Ann__es_concern__es___other`, `COMENTAIRES`, `COMENTAIRES_other`, `INTERETS`, `INTERETS_other`, `REFERENCES`, `REFERENCES_other`, `PAGES`, `PAGES_other`, `IMAGES`, `IMAGES_other`, `TAGS`, `TAGS_other`) VALUES
(1, '2026-01-20 06:04:50', '[\"CHAPITRE 2\"]', NULL, 'gkjvh ùgoiheùvihE% POH', NULL, 'VOIHzomeiht,oZIEHZ', NULL, 'COEJ  \r\nPOA', NULL, 'VETETETVZTZBTEBE', NULL, '242', NULL, 'uploads/file_696f1b024b702.webp', NULL, 'msfl,mqldnb', NULL),
(2, '2026-01-20 06:05:17', '[\"CHAPITRE 1\",\"CHAPITRE 3\"]', NULL, 'fvalkrb ùpzojepoZ', NULL, 'oievh %POAHZV\r\nPROaojOJAZ', NULL, 'ZOCRJ PAozjvroajz', NULL, 'êlkfnamkznsgmvlqnsf', NULL, '24', NULL, NULL, NULL, 'gzdhaqeg', NULL),
(3, '2026-01-20 06:05:59', '[\"CHAPITRE 3\"]', NULL, 'k faozojJ €O ', NULL, 'ELKGN OE', NULL, 'LSV?MLS?VL', NULL, 'LSKVNMK<n', NULL, '45', NULL, 'uploads/file_696f1b478751f.jpeg', NULL, 'q,vnmkq', NULL),
(4, '2026-01-20 06:19:40', '[\"CHAPITRE 3\"]', NULL, 'dvZBzddvzv', NULL, 'dzZZGZEGZ', NULL, 'ZRGERERG', NULL, 'EOIFHV Z%EO', NULL, '45', NULL, NULL, NULL, 'JSCLK', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `project_43`
--

DROP TABLE IF EXISTS `project_43`;
CREATE TABLE IF NOT EXISTS `project_43` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `THEMES` text,
  `THEMES_other` text,
  `RESUMES` text,
  `RESUMES_other` text,
  `COMMENTAIRE` text,
  `COMMENTAIRE_other` text,
  `PAGES` text,
  `PAGES_other` text,
  `UPLOAD` text,
  `UPLOAD_other` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `project_43`
--

INSERT INTO `project_43` (`id`, `submitted_at`, `THEMES`, `THEMES_other`, `RESUMES`, `RESUMES_other`, `COMMENTAIRE`, `COMMENTAIRE_other`, `PAGES`, `PAGES_other`, `UPLOAD`, `UPLOAD_other`) VALUES
(1, '2026-01-20 06:06:27', 'Autre', 'THEMES 4', 'dlvkn ponfO', NULL, 'qldkN pdfa', NULL, '45', NULL, 'uploads/file_696f1b63f0420.webp', NULL),
(2, '2026-01-20 06:06:45', 'THEMES 1', '', 'svdkh ùzeovjpo azj', NULL, 'lkvj \r\npoajzvop', NULL, '445', NULL, NULL, NULL),
(3, '2026-01-20 06:07:01', 'THEMES 2', '', ':ffvmkqnd', NULL, 'qdl,ùlS?G%ms\r\nm,\r\nmz,f', NULL, '214', NULL, 'uploads/file_696f1b85f09a3.jpeg', NULL),
(4, '2026-01-20 06:09:43', 'THEMES 2', '', 'rrvffazrvraz', NULL, 'sf<ehz<h455', NULL, '54', NULL, NULL, NULL),
(5, '2026-01-20 06:27:37', 'THEMES 2', '', 'djvoljvblvjb', NULL, 'sjkv ùzkjdv ùlkdv', NULL, '445', NULL, NULL, NULL),
(6, '2026-01-20 06:48:25', 'THEMES 2', '', 's,v msk vm k fnùen', NULL, 'jvlldvkqmkvqmvlms', NULL, '23', NULL, NULL, NULL),
(7, '2026-01-20 07:01:50', 'THEMES 2', '', 'jkbbmijkj', NULL, 'khbmkjb', NULL, '23', NULL, NULL, NULL),
(8, '2026-01-20 07:05:45', 'THEMES 1', '', 'klnjnkjkhjghdg', NULL, 'hjkkmll:lkjv', NULL, '24', NULL, NULL, NULL),
(9, '2026-01-20 07:08:07', 'THEMES 3', '', 'd,ld;dkdkqknds', NULL, 'pmlkjhgfd', NULL, '47', NULL, NULL, NULL),
(10, '2026-01-20 07:11:49', 'THEMES 2', '', 'asdfghtr', NULL, 'lkdnqlkdb', NULL, '25', NULL, NULL, NULL),
(11, '2026-01-20 07:18:55', 'THEMES 2', '', 'elkgfAVPoa?¨\r\nR', NULL, '/QDKNVMQEKGNMPAO', NULL, '25', NULL, 'uploads/file_696f2c5f3469d.png', NULL),
(12, '2026-01-23 04:24:00', '', '', '', NULL, '', NULL, '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `project_46`
--

DROP TABLE IF EXISTS `project_46`;
CREATE TABLE IF NOT EXISTS `project_46` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UPLOAD` text,
  `UPLOAD_other` text,
  `CHAPITRES` text,
  `CHAPITRES_other` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `project_46`
--

INSERT INTO `project_46` (`id`, `submitted_at`, `UPLOAD`, `UPLOAD_other`, `CHAPITRES`, `CHAPITRES_other`) VALUES
(1, '2026-01-23 06:34:04', NULL, NULL, 'CHAP 1', '');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `role` enum('admin','chercheur','assistant','user') DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `role`, `password`, `created_at`) VALUES
(3, 'RAN', 'Mia', 'mialisoa3@gmail.com', 'admin', '$2y$10$NjYiKR83xPJiE4m/uq51kenscOlO46wJbXBwr/.dWCqKXK.rIElNO', '2026-01-14 07:07:44'),
(6, 'COO', 'Bee', 'alienor.grandemange@hotmail.fr', 'assistant', '$2y$10$oWQcNRH6GBUhE1cpyJLt9O2QtyKyxteucElfRHI68254n6o9kfJuq', '2026-01-24 04:58:13');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
