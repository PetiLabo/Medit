-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Client: 127.0.0.1
-- Généré le: Sam 27 Février 2016 à 14:05
-- Version du serveur: 5.6.14
-- Version de PHP: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `medit_base`
--

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_annotation`
--

CREATE TABLE IF NOT EXISTS `medit_test_annotation` (
  `id_annotation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_type_annotation` int(10) unsigned NOT NULL,
  `fk_paragraphe` int(10) unsigned NOT NULL,
  `fk_auteur` int(10) unsigned NOT NULL,
  `texte` text NOT NULL,
  PRIMARY KEY (`id_annotation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_article`
--

CREATE TABLE IF NOT EXISTS `medit_test_article` (
  `id_article` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(140) NOT NULL,
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_auteur`
--

CREATE TABLE IF NOT EXISTS `medit_test_auteur` (
  `id_auteur` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(50) NOT NULL,
  `pseudo` varchar(40) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `nom` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `mot_de_passe` varchar(48) NOT NULL,
  `derniere_connexion` datetime NOT NULL,
  `fk_profil` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_auteur`),
  UNIQUE KEY `identifiant_2` (`identifiant`),
  KEY `pseudo` (`pseudo`),
  KEY `identifiant` (`identifiant`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_commentaire`
--

CREATE TABLE IF NOT EXISTS `medit_test_commentaire` (
  `id_commentaire` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `niveau` tinyint(3) unsigned NOT NULL,
  `horodatage` datetime NOT NULL,
  `texte` text NOT NULL,
  `fk_commentaire` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_auteur` int(10) unsigned NOT NULL,
  `fk_fil_discussion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_commentaire`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_composition`
--

CREATE TABLE IF NOT EXISTS `medit_test_composition` (
  `id_composition` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_version` int(10) unsigned NOT NULL,
  `fk_paragraphe` int(10) unsigned NOT NULL,
  `no_ordre` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_composition`),
  KEY `fk_version` (`fk_version`,`fk_paragraphe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=619 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_discussion`
--

CREATE TABLE IF NOT EXISTS `medit_test_discussion` (
  `id_discussion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_paragraphe` int(10) unsigned NOT NULL,
  `fk_fil_discussion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_discussion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_fil_discussion`
--

CREATE TABLE IF NOT EXISTS `medit_test_fil_discussion` (
  `id_fil_discussion` int(11) NOT NULL AUTO_INCREMENT,
  `horodatage` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fk_auteur` int(10) unsigned NOT NULL,
  `fk_article` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_fil_discussion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_journal`
--

CREATE TABLE IF NOT EXISTS `medit_test_journal` (
  `id_journal` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_evenement` tinyint(3) unsigned NOT NULL,
  `horodatage` datetime NOT NULL,
  `fk_auteur` int(10) unsigned NOT NULL,
  `fk_article` int(10) unsigned NOT NULL,
  `fk_contextuel` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_journal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_paragraphe`
--

CREATE TABLE IF NOT EXISTS `medit_test_paragraphe` (
  `id_paragraphe` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `niveau` int(10) NOT NULL,
  `horodatage` datetime NOT NULL,
  `texte` text NOT NULL,
  `verrou` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fk_verrou` int(10) unsigned NOT NULL,
  `fk_auteur` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_paragraphe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_profil`
--

CREATE TABLE IF NOT EXISTS `medit_test_profil` (
  `id_profil` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom_profil` varchar(50) NOT NULL,
  `droit_mode_edition` tinyint(4) NOT NULL DEFAULT '1',
  `droit_edition` tinyint(4) NOT NULL DEFAULT '1',
  `droit_commentaire` tinyint(4) NOT NULL DEFAULT '1',
  `droit_annotation` tinyint(4) NOT NULL DEFAULT '1',
  `droit_creation_version` tinyint(4) NOT NULL DEFAULT '0',
  `droit_creation_fil_discussion` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_profil`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_type_annotation`
--

CREATE TABLE IF NOT EXISTS `medit_test_type_annotation` (
  `id_type_annotation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label_annotation` varchar(80) NOT NULL,
  `code_couleur` varchar(20) NOT NULL,
  PRIMARY KEY (`id_type_annotation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_verrou`
--

CREATE TABLE IF NOT EXISTS `medit_test_verrou` (
  `id_verrou` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_verrou` tinyint(3) unsigned NOT NULL,
  `horodatage` datetime NOT NULL,
  `suspension` datetime DEFAULT NULL,
  `fk_auteur` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_verrou`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medit_test_version`
--

CREATE TABLE IF NOT EXISTS `medit_test_version` (
  `id_version` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `no_version` int(10) unsigned NOT NULL,
  `no_sous_version` int(10) unsigned NOT NULL DEFAULT '0',
  `horodatage` datetime NOT NULL,
  `fk_article` int(10) unsigned NOT NULL,
  `fk_auteur` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_version`),
  KEY `no_sous_version` (`no_sous_version`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
