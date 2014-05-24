ALTER TABLE `#__apl_chantiers`
ADD `vpn` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'chantier porte la griffe VPN ?'
AFTER `urgence`;
