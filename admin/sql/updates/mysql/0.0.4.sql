--
-- Structure de la table `#__apl_chantiers_categories`
--

CREATE TABLE IF NOT EXISTS `#__apl_chantiers_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) COLLATE utf8_bin NOT NULL,
  `link` varchar(255) COLLATE utf8_bin NOT NULL,
  `glat` float NOT NULL DEFAULT '0',
  `glng` float NOT NULL DEFAULT '0',
  `zoomLevel` smallint(5) unsigned NOT NULL DEFAULT '1',
  `map_type` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'TERRAIN' COMMENT 'https://developers.google.com/maps/documentation/javascript/maptypes',
  `enable` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `#__apl_chantiers_categories`
--

INSERT INTO `#__apl_chantiers_categories` (`id`, `text`, `link`, `glat`, `glng`, `zoomLevel`, `map_type`, `enable`) VALUES
(10, 'Rhone-Alpes', '', 45.1943, 5.73163, 7, 'TERRAIN', 1),
(20, 'France', '', 46.3166, 2.37305, 5, 'TERRAIN', 1),
(100, 'Europe', '', 49.6676, 4.83398, 4, 'TERRAIN', 0),
(999, 'Monde', '', 20.9614, -12.6563, 2, 'TERRAIN', 1);
