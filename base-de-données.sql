-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 31 déc. 2025 à 23:40
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pc_tech_boutique`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `full_name`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@boutiquetech.com', 'Administrateur Principal', '2025-12-31 20:32:14', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `nom_client` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `produits` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `statut` varchar(20) DEFAULT 'en attente',
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `nom_client`, `email`, `telephone`, `adresse`, `produits`, `total`, `statut`, `date_commande`) VALUES
(1, 'Test Client', NULL, '0551234567', 'Alger, Algeria', '[{\"nom\":\"HP Pavilion14\",\"prix\":120000,\"quantite\":1}]', 120000.00, 'en attente', '2025-12-30 19:12:14'),
(2, 'Test Client', NULL, '0551234567', 'Alger, Algeria', '[{\"nom\":\"HP Pavilion14\",\"prix\":120000,\"quantite\":1}]', 120000.00, 'en attente', '2025-12-30 19:12:31'),
(3, 'Test Client', NULL, '0551234567', 'Alger, Algeria', '[{\"nom\":\"HP Pavilion14\",\"prix\":120000,\"quantite\":1}]', 120000.00, 'en attente', '2025-12-30 20:43:32'),
(4, 'Test Client', NULL, '0551234567', 'Alger, Algeria', '[{\"nom\":\"HP Pavilion14\",\"prix\":120000,\"quantite\":1}]', 120000.00, 'en attente', '2025-12-30 20:44:41'),
(5, 'Test Client', NULL, '0551234567', 'Alger, Algeria', '[{\"nom\":\"HP Pavilion14\",\"prix\":120000,\"quantite\":1}]', 120000.00, 'en attente', '2025-12-30 20:48:27'),
(8, 'WissaL ', NULL, '0782436921', '', '', 120000.00, 'en attente', '2025-12-30 23:15:12'),
(9, 'Houssem', NULL, '0736546373', '', '', 120000.00, 'en attente', '2025-12-30 23:16:15'),
(10, 'Hadji Hadda', NULL, '0783436721', '', '', 120000.00, 'en attente', '2025-12-31 10:03:16'),
(11, 'wissal', NULL, '0936241562', '', '', 120000.00, 'en attente', '2025-12-31 10:21:38');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sujet` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `date_envoi` timestamp NOT NULL DEFAULT current_timestamp(),
  `lu` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `nom`, `email`, `sujet`, `message`, `date_envoi`, `lu`) VALUES
(1, 'Test', 'test@email.com', 'Test Sujet', 'Ceci est un message test', '2025-12-31 11:04:28', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `categorie` varchar(50) DEFAULT 'portable',
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `prix`, `image`, `description`, `categorie`, `date_ajout`) VALUES
(1, 'HP Pavilion14', 120000.00, 'hp pavilion.jpg', 'Intel Core i5, 8 Go, 512 Go SSD, 15.6 FHD', 'portable', '2025-12-30 11:10:59'),
(2, 'Dell Inspiron15', 180000.00, 'dell inspiron.jpg', 'Intel Core i7, 16 Go RAM, 512 Go SSD, 15.6 FHD', 'portable', '2025-12-30 11:10:59'),
(3, 'Lenovo IdePad 3', 95000.00, 'lenovo Idepad.jpg', 'AMD Ryzen 5, 8 Go RAM, 256 Go SSD, 15.6 FHD', 'portable', '2025-12-30 11:10:59'),
(4, 'Acer Aspire 5', 110000.00, 'acer aspire.jpg', 'Intel Core i5, 8 Go RAM, 512 Go SSD, 15.6 FHD', 'portable', '2025-12-30 11:10:59'),
(5, 'Assus VivoBook 14', 150000.00, 'assus.jpg', 'AMD Ryzen 7, 16 Go, 512 Go SSD, 14 FHD', 'portable', '2025-12-30 11:10:59'),
(6, 'HP EliteBook 840 G7', 220000.00, 'elitbook.jpg', 'Intel Core i7, 16 Go RAM, 512 Go SSD, 14 FHD', 'portable', '2025-12-30 11:10:59'),
(7, 'HP Envy x360', 210000.00, 'envy.jpg', 'AMD Ryzen 7, 16 Go RAM, 512 Go SSD, 15.6 Tactile', 'portable', '2025-12-30 11:10:59'),
(8, 'HP Spectre x360', 320000.00, 'spectre.jpg', 'Intel Core i7, 16 Go RAM, 1 To SSD, 13.3 OLED', 'portable', '2025-12-30 11:10:59'),
(9, 'HP Omen 15', 270000.00, 'omen.jpg', 'Intel Core i7, 16 Go RAM, 1 To SSD, RTX, 144 HZ', 'portable', '2025-12-30 11:10:59'),
(10, 'HP 250 G8', 95000.00, 'hp 250.jpg', 'Intel Core i3, 8 Go RAM, 256 Go SSD, 15.6', 'portable', '2025-12-30 11:10:59'),
(11, 'MacBook Air M1', 210000.00, 'Apple MacBook .jpg', 'Apple M1, 8 Go RAM, 256 Go SSD, 13.3 Retina', 'portable', '2025-12-30 11:10:59'),
(12, 'Dell Vostro 3500', 175000.00, 'DDEl.jpg', 'Intel Core i7, 16 Go, 512 Go SSD, 15.6 FHD', 'portable', '2025-12-30 11:10:59');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
