-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 04 fév. 2026 à 04:50
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
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `project_name`, `project_description`, `form_data`, `created_at`, `statut`) VALUES
(42, 0, 'PROJECT 1', '', '[{\"name\":\"CHAPITRES :\",\"type\":\"checkbox\",\"options\":[\"CHAPITRE 1\",\"CHAPITRE 2\",\"CHAPITRE 3\",\"Autre\"],\"allowOther\":true,\"allowAI\":false},{\"name\":\"Années concernées :\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"COMENTAIRES\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"INTERETS\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"REFERENCES\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":false},{\"name\":\"PAGES\",\"type\":\"number\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"IMAGES\",\"type\":\"file\",\"options\":[],\"allowOther\":true,\"allowAI\":false},{\"name\":\"TAGS\",\"type\":\"text\",\"options\":[],\"allowOther\":true,\"allowAI\":true}]', '2026-01-20 06:02:45', 'Fini'),
(43, 0, 'PROJECT 2', '', '[{\"name\":\"THEMES\",\"type\":\"select\",\"options\":[\"THEMES 1\",\"THEMES 2\",\"THEMES 3\",\"Autre\"],\"allowOther\":true,\"allowAI\":false},{\"name\":\"RESUMES\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"COMMENTAIRE\",\"type\":\"textarea\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"PAGES\",\"type\":\"number\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"UPLOAD\",\"type\":\"file\",\"options\":[],\"allowOther\":true,\"allowAI\":false}]', '2026-01-20 06:04:14', 'En cours'),
(49, 0, 'MAROC', 'Histoire de la dynastie Alaouite du Maroc', '[{\"name\":\"THEME GENERAL :\",\"type\":\"select\",\"options\":[\"Chapitre 0 : Le Maroc avant 1575\",\"Chapitre 00 : Le Maroc juste avant les Alaouites (1575/1636)\",\"Chapitre 1: Moulay Mohammed (1636/1664)\",\"Chapitre 2 : Moulay Rachid (1664/1672)\",\"Chapitre 3 : Moulay Ismail (1672/1727)\",\"Chapitre 4 : l\'anarchie ( 1727/1757)\",\"Chapitre 5 : Sidi Modammed ben Abdallah (1757/1790)\",\"Chapitre 6 : Moulay Yazid (1790/1792)\",\"Chapitre 7 : Moulay Slimane (1792/1822)\",\"Chapitre 8 : Moulay Abderrahmane ben Hicham (1822/1859)\",\"Autre\"],\"allowOther\":true,\"allowAI\":false},{\"name\":\"Sujet de l\'extrait :\",\"type\":\"text\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"Année concernée par l\'extrait (Non obligatoire):\",\"type\":\"text\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"Commentaire :\",\"type\":\"text\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"Resumé rapide :\",\"type\":\"text\",\"options\":[],\"allowOther\":true,\"allowAI\":true},{\"name\":\"Tags/mots clés associés :\",\"type\":\"checkbox\",\"options\":[\"Vie du Sultan\",\"Vie de la cour /Harem\",\"Vie politique\",\"Contexte economique\",\"Contexte politique\",\"paysage\",\"évènement\",\"personnage\",\"Autre\"],\"allowOther\":true,\"allowAI\":true},{\"name\":\"UPLOAD de l\'extrait de source\",\"type\":\"file\",\"options\":[],\"allowOther\":true,\"allowAI\":false},{\"name\":\"Référence\",\"type\":\"select\",\"options\":[\"Abitbol, Michel. 2009. Histoire du Maroc. Pour l’histoire. Perrin.\",\"Barbe, Adam, et Thomas Piketty. 2021. Dette publique et impérialisme au Maroc. Albouraq éditions.\",\"Benoist-Mechin Histoires des Alaouites -paris - 1991\",\"Laboudi, Fouad. 2021. « La médecine au Maroc du XVIIe au XIXe siècle: esquisses historiques ». L’Harmattan Maghred-Orient.\",\"Lugan, Bernard. 2023. Histoire du Maroc: Des origines à nos jours. Ellipses.\",\"Autre\"],\"allowOther\":true,\"allowAI\":false}]', '2026-02-04 04:35:32', 'En cours');

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
-- Structure de la table `project_49`
--

DROP TABLE IF EXISTS `project_49`;
CREATE TABLE IF NOT EXISTS `project_49` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `THEME_GENERAL__` text,
  `THEME_GENERAL___other` text,
  `Sujet_de_l_extrait__` text,
  `Sujet_de_l_extrait___other` text,
  `Ann__e_concern__e_par_l_extrait__Non_obligatoire__` text,
  `Ann__e_concern__e_par_l_extrait__Non_obligatoire___other` text,
  `Commentaire__` text,
  `Commentaire___other` text,
  `Resum___rapide__` text,
  `Resum___rapide___other` text,
  `Tags_mots_cl__s_associ__s__` text,
  `Tags_mots_cl__s_associ__s___other` text,
  `UPLOAD_de_l_extrait_de_source` text,
  `UPLOAD_de_l_extrait_de_source_other` text,
  `R__f__rence` text,
  `R__f__rence_other` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `project_49`
--

INSERT INTO `project_49` (`id`, `submitted_at`, `THEME_GENERAL__`, `THEME_GENERAL___other`, `Sujet_de_l_extrait__`, `Sujet_de_l_extrait___other`, `Ann__e_concern__e_par_l_extrait__Non_obligatoire__`, `Ann__e_concern__e_par_l_extrait__Non_obligatoire___other`, `Commentaire__`, `Commentaire___other`, `Resum___rapide__`, `Resum___rapide___other`, `Tags_mots_cl__s_associ__s__`, `Tags_mots_cl__s_associ__s___other`, `UPLOAD_de_l_extrait_de_source`, `UPLOAD_de_l_extrait_de_source_other`, `R__f__rence`, `R__f__rence_other`) VALUES
(1, '2026-02-04 04:38:14', 'Chapitre 0 : Le Maroc avant 1575', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '1235', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '[\"Contexte economique\"]', NULL, 'uploads/file_6982cd36106e1.png', NULL, 'Lugan, Bernard. 2023. Histoire du Maroc: Des origines à nos jours. Ellipses.', ''),
(2, '2026-02-04 04:38:44', 'Chapitre 1: Moulay Mohammed (1636/1664)', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '352', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '[\"Vie du Sultan\"]', NULL, 'uploads/file_6982cd5444e6c.jpeg', NULL, 'Barbe, Adam, et Thomas Piketty. 2021. Dette publique et impérialisme au Maroc. Albouraq éditions.', ''),
(3, '2026-02-04 04:39:13', 'Chapitre 2 : Moulay Rachid (1664/1672)', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '6543', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '[\"Contexte politique\"]', NULL, 'uploads/file_6982cd70e4f00.png', NULL, 'Laboudi, Fouad. 2021. « La médecine au Maroc du XVIIe au XIXe siècle: esquisses historiques ». L’Harmattan Maghred-Orient.', ''),
(4, '2026-02-04 04:39:37', 'Chapitre 4 : l\'anarchie ( 1727/1757)', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '1235', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '[\"personnage\"]', NULL, 'uploads/file_6982cd89df059.webp', NULL, 'Lugan, Bernard. 2023. Histoire du Maroc: Des origines à nos jours. Ellipses.', ''),
(5, '2026-02-04 04:39:46', 'Chapitre 1: Moulay Mohammed (1636/1664)', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '68', NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, '', ''),
(6, '2026-02-04 04:40:50', 'Chapitre 4 : l\'anarchie ( 1727/1757)', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '2003', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '[\"Contexte economique\"]', NULL, 'uploads/file_6982cdd24d4d9.png', NULL, 'Benoist-Mechin Histoires des Alaouites -paris - 1991', '');

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
  `reset_code` varchar(10) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `role`, `password`, `reset_code`, `reset_expires_at`, `created_at`) VALUES
(3, 'RAN', 'Mia', 'mialisoa3@gmail.com', 'admin', '$2y$10$BvDxjZWa3JTHd6Uk6jwOauKCj2reFLQrMsZXdo2vk9zwgPIMwCFyO', '907534', '2026-02-04 05:20:58', '2026-01-14 07:07:44'),
(6, 'COO', 'Bee', 'alienor.grandemange@hotmail.fr', 'user', '$2y$10$KM9hpxcMluXEEv88sUUFTu3MrPSuPxhEAa73y4ttBKRFeUmcvIQ4u', NULL, NULL, '2026-01-24 04:58:13');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
