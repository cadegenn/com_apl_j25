<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * db component helper.
 * 
 * host all queries/access to database outside of the model
 * when we need only the data
 * If wee need to build a control, please see APLcontrols
 */
abstract class APLdb {
	/**
	 * connect to db
	 */

	/**
	 * extract default latlng from #__apl_chantiers_categories
	 */
	function getDefaultChantiersCategorie($catid = 10) {
		$db = JFactory::getDbo();
		//$query = $db->getQuery(true);
		//$query = "SELECT * FROM `#__apl_chantiers_categories` WHERE published = 1 ORDER BY id ASC LIMIT 1";
		$query = "SELECT * FROM `#__apl_chantiers_categories` WHERE id=".$catid;
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error().'<br />QUERY = '.nl2br($query));
		$db->setQuery($query);
		$db->execute();
		$row = $db->loadObject();
		return $row;
	}

	/**
	 * @brief	get the default mapType of the categorie choosen
	 * 
	 * @return type
	 */
	function getDefaultMapType($catid = 0) {
		$db = JFactory::getDbo();
		//$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__apl_chantiers_categories` WHERE published = 1 ORDER BY id ASC LIMIT 1";
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error().'<br />QUERY = '.nl2br($query));
		$db->setQuery($query);
		$db->execute();
		$row = $db->loadObject();
		return $row;
	}

	
}
?>
