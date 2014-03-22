
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * aplModelImportV2AdherentsOrigines List Model
 */
class aplModelImportV2AdherentsOrigines extends JModelList
{
    /*
     * database from wich to import data
     */
    protected $database = "";//apasdelo2011";
    /*
     * hostname where lives the database
     */
    protected $db_host = "";//localhost";
    /*
     * username to access database
     */
    protected $db_username = "";//root";
    /*
     * password of the user
     */
    protected $db_passwd = "";//alpha1";
	/*
	 * database object
	 */
	public $_db=null;

	public function __construct($config = array()) {
		parent::__construct($config);
		
		$db = JFactory::getDbo();
		$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_apl"');
		$params = json_decode( $db->loadResult(), true );
		$this->database=$params['database'];
		$this->db_host=$params['db_host'];
		$this->db_username=$params['db_username'];
		$this->db_passwd=$params['db_passwd'];

		$options = array(   "driver"    => "mysql",
							"database"  => $this->database,
							"select"    => true,
							"host"      => $this->db_host,
							"user"      => $this->db_username,
							"password"  => $this->db_passwd
				);
		$this->_db = JDatabaseMySQL::getInstance($options);
		
		if ($this->_db->getErrorNum()>0) { JFactory::getApplication()->enqueueMessage(JText::_('COM_APL_IMPORT_DATABASE_CONNEXION_ERROR'), 'error'); }
	}

	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		if ($this->_db->getErrorNum() > 0) { return false; }
		
		$this->_db->setQuery($query, $limitstart, $limit);
		$result = $this->_db->loadObjectList();

		return $result;
	}

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return	string	An SQL query
     */
    protected function getListQuery()
    {
		//echo var_dump($this->_db);
		if ($this->_db->getErrorNum() > 0) { return "SELECT 0=1"; }
				
		$query = $this->_db->getQuery(true);
		// Select some fields
		$query->select('*');
		// From the hello table
		$query->from($this->database.'.origines');
		//$query->leftJoin($this->database.'.categories_chantier ON categories_chantier.id = chantiers.categorie');
		$query->order('id');
		//$query->order('pays');
		//$query->order('nom');
		return $query;
    }

	/*
	 * Import chantiers from V2 APL database
	 */
	public function import($cid) {
		// pour afficher le debug, supprimer la redirection dans --> controllers/importv2chantierscategories.php
		//echo("<pre>".var_dump($cid)."</pre>");
		$db = JFactory::getDbo();
		
		foreach ($cid as $key => $id) {
			// on initialise les objets
			$query_apl = $this->_db->getQuery(true);
			$query = $db->getQuery(true);
			
			// on interroge la base V2
			$query_apl->select('origines.*')->from($this->database.'.origines')->where('origines.id = '.$id);
			$this->_db->setQuery($query_apl);
			//echo("<pre>".var_dump($this->_db->getQuery())."</pre>");
			$this->_db->execute();
			$rows = $this->_db->loadObjectList();
			//echo("<pre>".var_dump($rows)."</pre>");
			foreach( $rows as $row ) {
				//echo("<pre>".var_dump($row)."</pre>");
				// on duplique l'objet source
				$origine = clone $row; //new stdClass();//$row;
				// on ajoute les nouveaux champs
				$origine->published = 0;//($row->obsolete ? 2 : $categorie->published);
				// on formatte les données correctement
				$origine->published = ($row->enable ? 1 : $origine->published);
				// on supprime les attributs de trop
				unset($origine->enable);
				//echo("<pre>".var_dump($categorie)."</pre>");
				// on supprime les id en conflit
				$query->delete('#__apl_adherents_origines')->where('id = '.$origine->id);
				$db->setQuery($query);
				$db->execute();
				// on insert le nouvel objet (qui en fait contient les anciennes data de la V2 du site)
				$db->insertObject('#__apl_adherents_origines', $origine );
			}
		}
		// si une erreur survient, de toute façon, PHP aura intérompu le traitement et joomla et/ou les log apache l'auront affiché
		return true;
	}
	
	/*public function getPagination() {
		$pagination = parent::getPagination();
		$pagination->set("_viewall", true);
	}*/
}
?>
