--
-- Structure de la table `chantiers`
--

CREATE TABLE IF NOT EXISTS `#__apl_chantiers` (
  `id` double NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `sous_titre` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `lieu` varchar(255) COLLATE utf8_bin NOT NULL,
  `countrycode` varchar(8) COLLATE utf8_bin NOT NULL,
  `pays` varchar(255) COLLATE utf8_bin NOT NULL,
  `glat` float NOT NULL DEFAULT '0',
  `glng` float NOT NULL DEFAULT '0',
  `organisateurs` mediumtext COLLATE utf8_bin,
  `contexte` mediumtext COLLATE utf8_bin,
  `actions` mediumtext COLLATE utf8_bin,
  `benevole` mediumtext COLLATE utf8_bin,
  `date_debut` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `date_fin` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `date_exacte` tinyint(1) NOT NULL COMMENT 'Si date_exacte n''est pas cochée (0), alors le site affiche des dates "vagues" genre "de mi-aout à  fin septembre" au lieu de "du 14 aout au 21 septembre"',
  `date_text` mediumtext COLLATE utf8_bin,
  `publish_up` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `profile` mediumtext COLLATE utf8_bin,
  `cout` int(11) DEFAULT '0',
  `cout_text` mediumtext COLLATE utf8_bin,
  `fiche_info` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `fiche_inscription` varchar(255) COLLATE utf8_bin NOT NULL,
  `fiche_custom` varchar(255) COLLATE utf8_bin NOT NULL,
  `test` tinyint(1) NOT NULL DEFAULT '0',
  `complet` tinyint(4) NOT NULL DEFAULT '0',
  `urgence` tinyint(4) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0' COMMENT 'category id pour integration joomla!',
  `created_by` int(10) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `published` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'published pour integration joomla!',
  `type` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;

--
-- Structure de la table `types_chantier`
--

CREATE TABLE IF NOT EXISTS `#__apl_chantiers_types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `types_chantier`
--

INSERT IGNORE INTO `#__apl_chantiers_types` (`id`, `nom`, `label`) VALUES
(1, 'Faune', 'Sauvegarde de la faune'),
(2, 'Flore', 'Sauvegarde de la flore');

--
-- Structure de la table `#__apl_chantiers_categories`
--

CREATE TABLE IF NOT EXISTS `#__apl_chantiers_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `link` varchar(255) COLLATE utf8_bin NOT NULL,
  `mapGlat` float NOT NULL DEFAULT '0',
  `mapGlng` float NOT NULL DEFAULT '0',
  `zoomLevel` smallint(5) unsigned NOT NULL DEFAULT '1',
  `mapType` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'TERRAIN' COMMENT 'https://developers.google.com/maps/documentation/javascript/maptypes',
  `published` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
