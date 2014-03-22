
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class aplModelMappemonde extends JModelList
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
		//$catid = JRequest::getVar('id', 0, 'get','int');
		
		//echo("<pre>"); var_dump($id); echo ("</pre>"); die();
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		//$query->select('#__apl_chantiers.*');
		$query->select('#__apl_chantiers.*, #__apl_chantiers_categories.id as catid,#__apl_chantiers_categories.published');
		// From the hello table
		$query->from('#__apl_chantiers');
		$query->leftJoin("#__apl_chantiers_categories ON (#__apl_chantiers.catid = #__apl_chantiers_categories.id)");
		//$query->where('catid = '.$catid);
		$query->where('#__apl_chantiers_categories.published = 1');
		$query->where('#__apl_chantiers.published = 1 #');
		return $query;
	}
	
}
?>
