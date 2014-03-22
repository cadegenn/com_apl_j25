ALTER TABLE `#__apl_chantiers`
ADD `created_by` INT( 10 ) UNSIGNED NOT NULL ,
ADD `creation_date` DATETIME NOT NULL ,
ADD `modified_by` INT( 10 ) UNSIGNED NULL ,
ADD `modification_date` DATETIME NULL
AFTER `catid`;