
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');

/**
 * aplModelImportV2Articles List Model
 */
class aplModelImportV2Articles extends JModelList
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

    /**
     * getImported()		get the already imported objects
     *
     * @return	(array)		array of objects
     */
    public function getImported()
    {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__content AS a');
		$query->order('alias ASC');
		$db->setQuery($query);
		$db->execute();
		
		return $db->loadObjectList();
    }

	/*
	 * Import articles from V2 APL database
	 */
	public function import($cid) {
		// pour afficher le debug, supprimer la redirection dans --> controllers/importv2articles.php
		//echo("<pre>".var_dump($cid)."</pre>");
		$db = JFactory::getDbo();
		
		// on initialise les objets
		$query_apl = $this->_db->getQuery(true);
		$query = $db->getQuery(true);

		// on interroge la base V2
		$query_apl->select('a.*')->from($this->database.'.actus AS a');
		if (is_array($cid)) {
			$query_apl->where('a.id IN ('.implode(",",$cid).')');				
		} else {
			$query_apl->where('a.id = '.$id);
		}
		$query_apl->select('actus_rubriques.label as rubrique_label')->leftJoin('actus_rubriques ON actus_rubriques.id = a.rubrique');
		$this->_db->setQuery($query_apl);
		//echo("<pre>".var_dump($this->_db->getQuery())."</pre>");
		//echo("<pre>".var_dump($query_apl->__toString())."</pre>");
		$this->_db->execute();
		$rows = $this->_db->loadObjectList();
		//echo("<pre>".var_dump($rows)."</pre>");
		foreach( $rows as $row ) {
			//echo("<pre>".var_dump($row)."</pre>");
			//
			// on harmonise un peu
			$row->type = ($row->type == '---' ? "actu" : $row->type);

			// on cherche la bonne catégorie de l'article (les créer au préalable)
			$db->setQuery('SELECT * FROM #__categories WHERE alias = "'.$row->type.'" LIMIT 1');
			$db->execute();
			$result = $db->loadObject();
			$catid = $result->id;

			// on cherche le bon auteur de l'article (les créer au préalable)
			$db->setQuery('SELECT * FROM #__users WHERE name = "'.$row->auteur.'" LIMIT 1');
			$db->execute();
			$result = $db->loadObject();
			$author = $result->id;
			//
			// on duplique l'objet source
			$article = new stdClass(); //clone $row; //new stdClass();//$row;
			// on ajoute les nouveaux champs
			//$article->published = 0;//($row->obsolete ? 2 : $article->published);
			//$article->catid = 0;

			// on formatte les données correctement
			//$article->title = APLFunctions::escapeString($row->titre);
			//$article->title = APLFunctions::mynl2br(stripslashes(html_entity_decode($row->titre, ENT_QUOTES, 'UTF-8')));
			$article->title = stripslashes(html_entity_decode($row->titre, ENT_QUOTES, 'UTF-8'));
			//$article->alias = preg_replace("/[^A-Za-z0-9]/", '-', APLFunctions::removeAccents($article->title));		// on enlève les accents et on remplace le reste par des tirets
			//$article->alias = preg_replace("/-+/", '-', $article->alias);
			$article->alias = JApplication::stringURLSafe($article->title);
			//$article->introtext = APLFunctions::mynl2br(stripslashes(html_entity_decode($row->message, ENT_QUOTES, 'UTF-8')));
			//$article->introtext = stripslashes(html_entity_decode($row->message, ENT_QUOTES, 'UTF-8'));
			
			/**
			 * @url http://davidwalsh.name/php-paragraph-regular-expression
			 * @comment #5
			 */
			$text = stripslashes(html_entity_decode($row->message, ENT_QUOTES, 'UTF-8'));
			$text = preg_replace('#<p>&nbsp;</p>#', '', $text);
			preg_match('#<p[^>]*>(.*)</p>#isU', $text, $matches);
			$article->introtext = $matches[0];
			$article->fulltext = preg_replace('#^<p[^>]*>.*</p>#isU', '', $text);
			//echo("<pre>matches : "); var_dump($matches); echo("</pre>");
			//echo("<pre>introtext : "); var_dump($article->introtext); echo("</pre>");
			//echo("<pre>fulltext : "); var_dump($article->fulltext); echo("</pre>");
			//$paragraphs = explode("<p", $article->introtext);
			//$paragraphs[1] .= "<p><hr id='system-readmore' />&nbsp;</p>";
			//$article->introtext = implode($paragraphs, "<p");
			
			$article->state = $row->visible;
			$article->sectionid = 0;
			$article->mask = 0;
			$article->catid = $catid;
			$article->created = $row->date;
			$article->created_by = $author;
			$article->modified = $row->date;
			$article->modified_by = $author;
			$article->publish_up = $row->date;
			$article->images = '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';
			$article->urls = '{"urla":null,"urlatext":"","targeta":"","urlb":null,"urlbtext":"","targetb":"","urlc":null,"urlctext":"","targetc":""}';
			$article->attribs ='{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
			$article->access = 1;
			$article->metadata = '{"robots":"","author":"","rights":"","xreference":""}';
			$article->language = "*";

			// si cet article a déjà été importé, on le détruit : les aliases sont uniques
			$query = $db->getQuery(true);
			$query->delete("#__content")->where('alias = "'.$article->alias.'"');
			$db->setQuery($query);
			$db->execute();
			//echo("<pre>"); var_dump($article); echo("</pre>"); die();
			// on insert le nouvel objet (qui en fait contient les anciennes data de la V2 du site)
			$db->insertObject('#__content', $article );
		} //die();
		// si une erreur survient, de toute façon, PHP aura intérompu le traitement et joomla et/ou les log apache l'auront affiché
		return true;
	}
	
}
?>
