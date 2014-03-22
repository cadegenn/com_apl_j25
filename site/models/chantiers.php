
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class aplModelChantiers extends JModelList
{
	protected $chantiers;
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// on récupère l'id de la catégorie à afficher
		// l'id est disponible dans le tableau $_GET : voici la méthode joomla pour y accéder
		$catid = JRequest::getVar('id', 0, 'get','int');
		
		//echo("<pre>"); var_dump($id); echo ("</pre>"); die();
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		//$query->select('#__apl_chantiers.*');
		$query->select('*');
		// From the hello table
		$query->from('#__apl_chantiers as a');
		$query->where('a.catid = '.$catid);
		$query->where('a.published = 1');

		// Filter by start and end dates.
		$nullDate       = $db->Quote($db->getNullDate());
		$nowDate        = $db->Quote(JFactory::getDate()->toSql());
		$query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		$query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');
		
		$query->order('pays');
		$query->order('nom');
		return $query;
	}
	
}
?>
