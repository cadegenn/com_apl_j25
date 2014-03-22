
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * aplModelImportTables List Model
 */
class aplModelImportTables extends JModelList
{
    /*
     * database from wich to import data
     */
    protected $database = "apasdelo2011";
    /*
     * hostname where lives the database
     */
    protected $db_host = "localhost";
    /*
     * username to access database
     */
    protected $db_username = "root";
    /*
     * password of the user
     */
    protected $db_passwd = "alpha1";

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return	string	An SQL query
     */
    protected function getListQuery()
    {
		// Create a new query object.		
		//$db = JDatabase::getInstance($options);
		/*$db = JFactory::getDbo();
		echo("<pre>".var_dump($db)."</pre>");
		$query = $db->getQuery(true);
		//$query = $db->setQuery('SELECT * FROM c0arp_apl_chantiers;', 0, 0);
		$query->select('*');
		$query->from('#__apl_chantiers');
		echo("<pre>".var_dump($query)."</pre>");*/

		$options = array(   "driver"    => "mysql",
							"database"  => $this->database,
							"select"    => true,
							"host"      => $this->db_host,
							"user"      => $this->db_username,
							"password"  => $this->db_passwd
				);
		$db_apl = JDatabaseMySQL::getInstance($options);
		//echo("<pre>".var_dump($db_apl)."</pre>");

		//$query = "select * from ".$this->database.".chantiers";
		$query = "show tables from ".$this->database."; #";     // trick à 2 balles, on ajoute un commentaire en fin de ligne car joomla s'entête à ajouter " LIMIT 0,20;" à la fin de la query, même si on a dit 0,0... trop chiant.
		$db_apl->setQuery($query, 0, 0);
		//$db_apl->query();
		//echo("<pre>".var_dump($db_apl)."</pre>");
		//echo("<pre>".var_dump($query_apl)."</pre>");
		//die();

		return $db_apl->getQuery(false);//$query_apl;
    }
}
?>
