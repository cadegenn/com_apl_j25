
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');

/**
 * aplModelImports List Model
 */
class aplModelImports extends JModelList
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

	public function __construct($config = array()) {
		parent::__construct($config);
		/*$db = JFactory::getDbo();
		$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_apl"');
		$params = json_decode( $db->loadResult(), true );
		$this->database=$params['database'];
		$this->db_host=$params['db_host'];
		$this->db_username=$params['db_username'];
		$this->db_passwd=$params['db_passwd'];*/

		$this->database=$this->getParam('database');
		$this->db_host=$this->getParam('db_host');
		$this->db_username=$this->getParam('db_username');
		$this->db_passwd=$this->getParam('db_passwd');
		
	}
	/**
	 * Méthode pour retrouver les paramètres du composant
	 * 
	 * @param string $key
	 * @return value
	 */
	function getParam( $key ) {
		// retreive one param
		$db = JFactory::getDbo();
		$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_apl"');
		$params = json_decode( $db->loadResult(), true );
		return $params[ $key ];
	}

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return	string	An SQL query
     */
    protected function getListQuery()
    {
		/*
		if (!$this->database || $this->database=="") { JFactory::getApplication()->enqueueMessage(JText::_('COM_APL_NO_IMPORT_DATABASE'), 'error'); return "SELECT 1=0"; }
		if (!$this->db_host || $this->db_host=="") { JFactory::getApplication()->enqueueMessage(JText::_('COM_APL_NO_IMPORT_DBHOST'), 'error'); return "SELECT 1=0"; }
		if (!$this->db_username || $this->db_username=="") { JFactory::getApplication()->enqueueMessage(JText::_('COM_APL_NO_IMPORT_DBUSERNAME'), 'error'); return "SELECT 1=0"; }
		if (!$this->db_passwd || $this->db_passwd=="") { JFactory::getApplication()->enqueueMessage(JText::_('COM_APL_NO_IMPORT_DBPASSWD'), 'error'); return "SELECT 1=0"; }

		$options = array(   "driver"    => "mysql",
							"database"  => $this->database,
							"select"    => true,
							"host"      => $this->db_host,
							"user"      => $this->db_username,
							"password"  => $this->db_passwd
				);
		$db_apl = JDatabaseMySQL::getInstance($options);
		//echo("<pre>".var_dump($db_apl)."</pre>");
		//$query = "show tables from ".$this->database."; #";     // trick à 2 balles, on ajoute un commentaire en fin de ligne car joomla s'entête à ajouter " LIMIT 0,20;" à la fin de la query, même si on a dit 0,0... trop chiant.
		//$db_apl->setQuery($query, 0, 0);
		//return $db_apl->getQuery(false);//$query_apl;
		
		$query = $db_apl->getQuery(true);
		// Select some fields
		$query->select('chantiers.id, nom, lieu, pays, categorie as catid, visible, categories_chantier.text as categorie');
		// From the hello table
		$query->from($this->database.'.chantiers');
		$query->leftJoin($this->database.'.categories_chantier ON categories_chantier.id = chantiers.categorie');
		$query->order('catid');
		$query->order('pays');
		$query->order('nom');
		return $query;
		 * 
		 */
    }

	/*
	 * Import chantiers from V2 APL database
	 */
	public function import($cid) {
		// pour afficher le debug, supprimer la redirection dans --> controllers/imports.php
		echo("<pre>".var_dump($cid)."</pre>");
		$options = array(   "driver"    => "mysql",
							"database"  => $this->database,
							"select"    => true,
							"host"      => $this->db_host,
							"user"      => $this->db_username,
							"password"  => $this->db_passwd
				);
		$db_apl = JDatabaseMySQL::getInstance($options);
		$db = JFactory::getDbo();
		
		foreach ($cid as $key => $id) {
			// on initialise les objets
			$query_apl = $db_apl->getQuery(true);
			$query = $db->getQuery(true);
			
			// on interroge la base V2
			$query_apl->select('chantiers.*, categories_chantier.text')->from($this->database.'.chantiers')->leftJoin('categories_chantier ON categories_chantier.id = chantiers.categorie')->where('chantiers.id = '.$id);
			$db_apl->setQuery($query_apl);
			//echo("<pre>".var_dump($db_apl->getQuery())."</pre>");
			$db_apl->execute();
			$rows = $db_apl->loadObjectList();
			//echo("<pre>".var_dump($rows)."</pre>");
			foreach( $rows as $row ) {
				//echo("<pre>".var_dump($row)."</pre>");
				// on duplique l'objet source
				$chantier = clone $row; //new stdClass();//$row;
				// on ajoute les nouveaux champs
				$chantier->glat = 0;
				$chantier->glng = 0;
				$chantier->published = 0;//($row->obsolete ? 2 : $chantier->published);
				$chantier->catid = 0;
				// on formatte les données correctement
				$chantier->glat = (float)$row->Glat;
				$chantier->glng = (float)$row->Glng;
				$chantier->nom = APLFunctions::escapeString($chantier->nom);
				$chantier->lieu = APLFunctions::escapeString($chantier->lieu);
				$chantier->pays = APLFunctions::escapeString($chantier->pays);
				$chantier->organisateurs = APLFunctions::escapeString($chantier->organisateurs);
				$chantier->contexte = APLFunctions::escapeString($chantier->contexte);
				$chantier->actions = APLFunctions::escapeString($chantier->actions);
				$chantier->benevole = APLFunctions::escapeString($chantier->benevole);
				$chantier->date_text = APLFunctions::escapeString($chantier->date_text);
				$chantier->profile = APLFunctions::escapeString($chantier->profile);
				$chantier->cout_text = APLFunctions::escapeString($chantier->cout_text);
				$chantier->published = ($row->obsolete ? 2 : $chantier->published);
				$chantier->published = ($row->visible ? 1 : $chantier->published);
				$chantier->catid = $row->categorie;
				// on supprime les attributs de trop
				unset($chantier->Glat);
				unset($chantier->Glng);
				unset($chantier->categorie);
				unset($chantier->obsolete);
				unset($chantier->visible);
				unset($chantier->text);
				//echo("<pre>".var_dump($chantier)."</pre>");
				$query->delete('#__apl_chantiers')->where('id = '.$chantier->id);
				$db->setQuery($query);
				$db->execute();
				// on insert le nouvel objet (qui en fait contient les anciennes data de la V2 du site)
				$db->insertObject('#__apl_chantiers', $chantier );
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
