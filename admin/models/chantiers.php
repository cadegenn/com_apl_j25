
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * aplModelChantiers List Model
 */
class aplModelChantiers extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'ch.id',
				'nom', 'ch.nom', 'LOWER(nom)', 'LOWER(ch.nom)',
				'lieu', 'ch.lieu', 'LOWER(lieu)',
				'pays', 'ch.pays', 'LOWER(pays)',
				'date_debut', 'ch.date_debut',
				'categorie', 'cc.nom', 'LOWER(categorie)',
				'published', 'ch.published',
				'created_by', 'ch.created_by',
				'creation_date', 'ch.creation_date'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		//$query->select('id, nom, lieu, pays, catid, status')->leftJoin('chantiers_categories ON chantiers_categories.id = chantiers.catid');;
		$query->select('ch.*, cc.nom as categorie')->from('#__apl_chantiers AS ch');
		$query->leftJoin('#__apl_chantiers_categories AS cc ON (cc.id = ch.catid)');;
		
		/*
		 * APPLY FILTERS
		 */
		// Filter by published
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('ch.published = '.(int) $published);
		}
		// Filter by categorie
		$categorieId = $this->getState('filter.categorie_id');
		if (is_numeric($categorieId)) {
			$query->where('ch.catid = '.(int) $categorieId);
		}
		// Filter by countrycode
		$countrycodeId = $this->getState('filter.countrycode_id');
		if (is_string($countrycodeId) && $countrycodeId != "") {
			$query->where('LOWER(ch.countrycode) = LOWER("'.$countrycodeId.'")');
		}
				
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'LOWER(ch.nom)');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		// quelque soit l'ordre, les ordres secondaires seront toujours :
		//$query->order('catid, pays, nom');

		return $query;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since		1.6
	 * @see			http://docs.joomla.org/How_to_add_custom_filters_to_component_admin
	*/
	protected function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		$categorieId = $app->getUserStateFromRequest($this->context.'.filter.categorie_id', 'filter_categorie_id');
		$this->setState('filter.categorie_id', $categorieId);

		$countrycodeId = $app->getUserStateFromRequest($this->context.'.filter.countrycode_id', 'filter_countrycode_id');
		$this->setState('filter.countrycode_id', $countrycodeId);

		$authorId = $app->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		// List state information.
		parent::populateState('ch.nom', 'asc');
	}
	
	/**
	 * Build a list of authors
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getAuthors() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__apl_chantiers AS ch ON ch.created_by = u.id');
		$query->group('u.id, u.name');
		$query->order('u.name');

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		return $db->loadObjectList();
	}

	public function getCategories() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('cat.id AS value, cat.nom AS text')->from('#__apl_chantiers_categories AS cat');
		$query->join('INNER','#__apl_chantiers AS ch ON (ch.catid = cat.id)');
		$query->group('cat.id, cat.nom');
		$query->order('cat.id ASC');
		
		$db->setQuery($query->__toString());
		return $db->loadObjectList();
	}

	public function getPays() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('ch.countrycode AS value, ch.pays AS text')->from('#__apl_chantiers AS ch');
		//$query->where('ch.pays IS NOT NULL')->where('ch.pays != ""');
		$query->group('LOWER(ch.countrycode)');
		$query->order('LOWER(ch.pays) ASC');
		
		$db->setQuery($query->__toString());
		return $db->loadObjectList();
	}

}
?>
