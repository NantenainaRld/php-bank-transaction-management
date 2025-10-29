-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 05 juil. 2025 à 17:09
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
-- Base de données : `transaction_bancaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `banque`
--

CREATE TABLE `banque` (
  `codeBanque` int(1) NOT NULL,
  `nomBanque` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `emailBanque` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `banque`
--

INSERT INTO `banque` (`codeBanque`, `nomBanque`, `password`, `emailBanque`) VALUES
(3, 'Accès Banque', '$2y$10$PIYLYJMSN1xVxHd1Mzg75OorbY58nziMiqAlvKrr.wW5ISRDyXMdC', 'bni@gmail.com'),
(4, 'Nantenaina Noelly Edouardo RALANDISON', '$2y$10$H4GDsitvVkQB2rpiZshLj.4rAY4sZTp7o0esIbkF1wk7ikvd1DrUS', 'edouardorld@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `numCompte` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenoms` varchar(125) NOT NULL,
  `Tel` varchar(20) NOT NULL,
  `emailClient` varchar(125) NOT NULL,
  `code_banque` int(11) NOT NULL,
  `solde` decimal(20,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`numCompte`, `Nom`, `Prenoms`, `Tel`, `emailClient`, `code_banque`, `solde`) VALUES
(206108, 'MAXIM', 'Rakoto', '0328329440', 'dollar.marielle@gmail.com', 3, 1200000.00),
(438452, 'Nantenaina', '', '032558899', 'nantenainanoelly@gmail.com', 3, 19550.00),
(983424, 'ANDRIARILALAINA', 'Lynda', '123456789', 'lyndaandriarilalaina@gmail.com', 3, 510350.00);

-- --------------------------------------------------------

--
-- Structure de la table `depot`
--

CREATE TABLE `depot` (
  `codeDepot` varchar(27) NOT NULL,
  `montantDepot` decimal(20,2) NOT NULL DEFAULT 0.00,
  `dateDepot` datetime NOT NULL,
  `num_compte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `depot`
--

INSERT INTO `depot` (`codeDepot`, `montantDepot`, `dateDepot`, `num_compte`) VALUES
('DP-202507031227-ODU380', 150.00, '2025-07-03 12:27:43', 438452),
('DP-202507031239-WFX31', 50.00, '2025-07-03 12:39:51', 438452),
('DP-202507051643-QTX176', 1000000.00, '2025-07-05 16:43:46', 206108);

-- --------------------------------------------------------

--
-- Structure de la table `pret`
--

CREATE TABLE `pret` (
  `codePret` varchar(27) NOT NULL,
  `num_compte` int(11) NOT NULL,
  `montantPret` decimal(20,2) NOT NULL,
  `datePret` datetime NOT NULL,
  `duree` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pret`
--

INSERT INTO `pret` (`codePret`, `num_compte`, `montantPret`, `datePret`, `duree`) VALUES
('PR-202507050934-NZE9', 438452, 5000.00, '2025-07-05 09:34:55', 12),
('PR-202507050936-CUY907', 983424, 5008.00, '2025-07-05 09:36:22', 7),
('PR-202507050937-NEW623', 438452, 8000.00, '2025-07-05 09:37:36', 7),
('PR-202507051648-PLU895', 206108, 1000000.00, '2025-07-05 16:48:41', 7);

-- --------------------------------------------------------

--
-- Structure de la table `rendu`
--

CREATE TABLE `rendu` (
  `codeRendu` varchar(27) NOT NULL,
  `code_pret` varchar(27) NOT NULL,
  `montantRendu` decimal(20,2) NOT NULL,
  `situation` enum('tout payé','payé une part') NOT NULL,
  `restePaye` decimal(20,2) NOT NULL,
  `dateRendu` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rendu`
--

INSERT INTO `rendu` (`codeRendu`, `code_pret`, `montantRendu`, `situation`, `restePaye`, `dateRendu`) VALUES
('RD-202507050940-DYB424', 'PR-202507050936-CUY907', 5008.00, 'tout payé', 0.00, '2025-07-05 09:40:57'),
('RD-202507050942-HGT120', 'PR-202507050937-NEW623', 5000.00, 'payé une part', 3000.00, '2025-07-05 09:42:44'),
('RD-202507051649-BQP579', 'PR-202507051648-PLU895', 300000.00, 'payé une part', 700000.00, '2025-07-05 16:49:27');

-- --------------------------------------------------------

--
-- Structure de la table `virement`
--

CREATE TABLE `virement` (
  `codeVirement` varchar(27) NOT NULL,
  `num_compteE` int(11) NOT NULL,
  `num_compteB` int(11) NOT NULL,
  `montantVirement` decimal(20,2) NOT NULL,
  `dateVirement` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `virement`
--

INSERT INTO `virement` (`codeVirement`, `num_compteE`, `num_compteB`, `montantVirement`, `dateVirement`) VALUES
('VR-202507051644-BXP869', 206108, 983424, 500000.00, '2025-07-05 16:44:54');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `banque`
--
ALTER TABLE `banque`
  ADD PRIMARY KEY (`codeBanque`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`numCompte`),
  ADD KEY `code_banque` (`code_banque`);

--
-- Index pour la table `depot`
--
ALTER TABLE `depot`
  ADD PRIMARY KEY (`codeDepot`),
  ADD KEY `num_compte` (`num_compte`);

--
-- Index pour la table `pret`
--
ALTER TABLE `pret`
  ADD PRIMARY KEY (`codePret`),
  ADD KEY `num_compteP` (`num_compte`);

--
-- Index pour la table `rendu`
--
ALTER TABLE `rendu`
  ADD PRIMARY KEY (`codeRendu`),
  ADD KEY `code_pret` (`code_pret`);

--
-- Index pour la table `virement`
--
ALTER TABLE `virement`
  ADD PRIMARY KEY (`codeVirement`),
  ADD KEY `num_compteE` (`num_compteE`),
  ADD KEY `num_compteB` (`num_compteB`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `banque`
--
ALTER TABLE `banque`
  MODIFY `codeBanque` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `code_banque` FOREIGN KEY (`code_banque`) REFERENCES `banque` (`codeBanque`) ON DELETE CASCADE;

--
-- Contraintes pour la table `depot`
--
ALTER TABLE `depot`
  ADD CONSTRAINT `num_compte` FOREIGN KEY (`num_compte`) REFERENCES `client` (`numCompte`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pret`
--
ALTER TABLE `pret`
  ADD CONSTRAINT `num_compteP` FOREIGN KEY (`num_compte`) REFERENCES `client` (`numCompte`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rendu`
--
ALTER TABLE `rendu`
  ADD CONSTRAINT `code_pret` FOREIGN KEY (`code_pret`) REFERENCES `pret` (`codePret`) ON DELETE CASCADE;

--
-- Contraintes pour la table `virement`
--
ALTER TABLE `virement`
  ADD CONSTRAINT `num_compteB` FOREIGN KEY (`num_compteB`) REFERENCES `client` (`numCompte`) ON DELETE CASCADE,
  ADD CONSTRAINT `num_compteE` FOREIGN KEY (`num_compteE`) REFERENCES `client` (`numCompte`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
