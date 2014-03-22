<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * controls component helper.
 * 
 * host all queries/access to database outside of the model 
 * in order to build GUI controls with its values
 * e.g. when a site needs to display all categories for example
 */
abstract class APLcontrols {
	/**
	 * connect to db
	 */

	/**
	 * extract categories of sites
	 */
	function selectChantiersCategories($select = 0) {
		$db = JFactory::getDbo();
		//$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__apl_chantiers_categories` WHERE published = 1 ORDER BY id";
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error().'<br />QUERY = '.nl2br($query));
		$db->setQuery($query);
		$db->execute();
		$rows = $db->loadAssocList();
		echo("<select id='jform_catid' name='jform[catid]'>");
		//while ($row = mysql_fetch_assoc($result)) {
		foreach ($rows as $row) {
			echo("<option value=".$row['id']." ".($row['id']==$select ? "selected" : "")." >".$row['nom']."</option>");
		}
		echo("</select>");
		//mysql_free_result($result);
	}

	
}
?>
