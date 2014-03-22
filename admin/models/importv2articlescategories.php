
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');

/**
 * aplModelImportV2ArticlesCategories List Model
 */
class aplModelImportV2ArticlesCategories extends JModelList
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
		$query->select('actus.*, actus_rubriques.label as rubrique_label');
		// From the hello table
		$query->from($this->database.'.actus');
		$query->leftJoin($this->database.'.actus_rubriques ON actus_rubriques.id = actus.rubrique');
		$query->order('date');
		return $query;
    }

	/*
	 * Import articlesCategories from V2 APL database
	 */
	public function import($cid) {
		// pour afficher le debug, supprimer la redirection dans --> controllers/importv2articlesCategories.php
		//echo("<pre>".var_dump($cid)."</pre>");
		$db = JFactory::getDbo();
		
		foreach ($cid as $key => $id) {
			// on initialise les objets
			$query_apl = $this->_db->getQuery(true);
			$query = $db->getQuery(true);
			
			// on interroge la base V2
			$query_apl->select('actus.*, actus_rubriques.label as rubrique_label')->from($this->database.'.actus')->leftJoin('actus_rubriques ON actus_rubriques.id = actus.rubrique')->where('actus.id = '.$id);
			$this->_db->setQuery($query_apl);
			//echo("<pre>".var_dump($this->_db->getQuery())."</pre>");
			$this->_db->execute();
			$rows = $this->_db->loadObjectList();
			//echo("<pre>".var_dump($rows)."</pre>");
			foreach( $rows as $row ) {
				//echo("<pre>".var_dump($row)."</pre>");
				//
				// on harmonise un peu
				$row->type = ($row->type == '---' ? "actu" : $row->type);
				
				// on cherche la bonne catégorie de l'article
				//$db->setQuery('SELECT * FROM #__categories WHERE alias = "'.$row->type."' AND  LIMIT 1");
				// on créé la rubrique si elle n'existe pas
				$db->setQuery('SELECT * FROM #__categories WHERE path = "blog/'.$row->type.'/'.preg_replace("/[^A-Za-z0-9 ]/", '-', $row->rubrique_label).'" LIMIT 1');
				//$db2->select('*')->from('#__categories')->where('path = "blog/'.$row->type.'/'.preg_replace("/[^A-Za-z0-9 ]/", '-', $row->rubrique_label).'" LIMIT 1');
				$result = $db->loadObject();
				//print_r($result); die();
				if (!isset($result->id)) { // si la catégorie n'est pas trouvée, alors on la créé
					// on cherche son parent
					$query2 = $db->getQuery(true);
					$query2->select('*')->from('#__categories')->where('path = "blog/'.$row->type);
					$cat_parent = $db->loadObject();
					echo("<pre>".var_dump($cat_parent)."</pre>");
					$db->setQuery('INSERT INTO #__categories (parent, level, path, extension, title, alias, published, access, params, metadata, created_user_id, created_time, modified_user_id, modified_time, hits, language) VALUES ('.$cat_parent->id.', 3, "blog/'.$row->type.'/'.preg_replace("/[^A-Za-z0-9 ]/", '-', $row->rubrique_label).'", "com_content", "'.$row->rubrique_label.'", "'.preg_replace("/[^A-Za-z0-9 ]/", '-', $row->rubrique_label).'", 1, 1, "{\"category_layout\":\"\",\"image\":\"\"}", "{\"author\":\"\",\"robots\":\"\"}", 42, "'.date('Y-m-d H:M:S').'", 42, "'.date('Y-m-d H:M:S').'", 0, "*")');
					//echo("<pre>".var_dump($db)."</pre>");
					$db->query();
					$catid = $db->insertid();
					echo("<pre>".var_dump($catid)."</pre>"); die();
				} else {
					$catid = $result->id;
				}
				
				//
				// on duplique l'objet source
				$article = new stdClass(); //clone $row; //new stdClass();//$row;
				// on ajoute les nouveaux champs
				//$article->published = 0;//($row->obsolete ? 2 : $article->published);
				//$article->catid = 0;
				
				// on formatte les données correctement
				$article->title = APLFunctions::escapeString($row->titre);
				$article->alias = preg_replace("/[^A-Za-z0-9 ]/", '-', $article->title);
				$article->introtext = APLFunctions::escapeString($row->message);
				$article->state = $row->visible;
				
				/*$article->contexte = APLFunctions::escapeString($article->contexte);
				$article->actions = APLFunctions::escapeString($article->actions);
				$article->benevole = APLFunctions::escapeString($article->benevole);
				$article->date_text = APLFunctions::escapeString($article->date_text);
				$article->profile = APLFunctions::escapeString($article->profile);
				$article->cout_text = APLFunctions::escapeString($article->cout_text);
				$article->published = ($row->obsolete ? 2 : $article->published);
				$article->published = ($row->visible ? 1 : $article->published);
				$article->catid = $row->categorie;
				$article->fiche_info = ($article->fiche_info != '' ? "images/apl".$article->fiche_info : "");
				$article->fiche_inscription = ($article->fiche_inscription != '' ? "images/apl".$article->fiche_inscription : "");
				//$article->fiche_custom = ($article->fiche_custom != '' ? "images/apl".$article->fiche_custom : "");
				// on supprime les attributs de trop
				unset($article->titre);
				unset($article->Glng);
				unset($article->categorie);
				unset($article->obsolete);
				unset($article->visible);
				unset($article->text);
				//echo("<pre>".var_dump($article)."</pre>");
				$query->delete('#__apl_articlesCategories')->where('id = '.$article->id);
				$db->setQuery($query);
				$db->execute();
				// on insert le nouvel objet (qui en fait contient les anciennes data de la V2 du site)
				$db->insertObject('#__apl_articlesCategories', $article );
				 * 
				 */
			}
		}
		// si une erreur survient, de toute façon, PHP aura intérompu le traitement et joomla et/ou les log apache l'auront affiché
		return true;
	}
	
}
?>
