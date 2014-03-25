<?php
/**
 * @package		com_apl_j25
 * @subpackage	importv2chantiers
 * @brief		MVC to import data from old database
 * @copyright	Copyright (C) 2010 - 2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 * @license		Affero GNU General Public License version 3 or later; see LICENSE.txt
 * 
 * @TODO		create osm-leaflet server-side geocoding
 */

/** 
 *  Copyright (C) 2012-2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 *  com_apl is a joomla! 2.5 component [http://www.apasdeloup.org]
 *  
 *  This file is part of com_apl.
 * 
 *     com_apl is free software: you can redistribute it and/or modify
 *     it under the terms of the Affero GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     com_apl is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     Affero GNU General Public License for more details.
 * 
 *     You should have received a copy of the Affero GNU General Public License
 *     along with com_apl.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');
//JLoader::register('GMAPv3', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/googlemap-v3.php');
$params = JComponentHelper::getParams('com_apl');
switch ($params->get("map_provider")) {
	case 'googlemap-v3'	:	JLoader::register('GMAPv3', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/googlemap-v3.php');
							break;
	case 'osm-leaflet'	:	JLoader::register('GMAPv3', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/googlemap-v3.php');
							break;
}

/**
 * aplModelImportV2Chantiers List Model
 */
class aplModelImportV2Chantiers extends JModelList
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
		//$query = $db->getQuery(true);
		//$query->select('id, params')->from('#__extensions')->where('name = "com_apl" ORDER BY id ASC LIMIT 1');
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
		$query->select('chantiers.id, nom, lieu, pays, categorie as catid, visible, categories_chantier.text as categorie');
		$query->from($this->database.'.chantiers');
		$query->leftJoin($this->database.'.categories_chantier ON categories_chantier.id = chantiers.categorie');
		$query->order('catid');
		$query->order('pays');
		$query->order('nom');
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
		$query->select('c.id, c.nom, lieu, pays, catid');
		$query->from('#__apl_chantiers AS c');
		$query->leftJoin('#__apl_chantiers_categories AS cc ON cc.id = c.catid');
		$query->order('catid');
		$query->order('pays');
		$query->order('nom');
		$db->setQuery($query);
		$db->execute();
		
		return $db->loadObjectList();
    }

	/*
	 * Import chantiers from V2 APL database
	 */
	public function import($cid) {
		// pour afficher le debug, supprimer la redirection dans --> controllers/importv2chantiers.php
		//echo("<pre>".var_dump($cid)."</pre>");
		$db = JFactory::getDbo();
		
		foreach ($cid as $key => $id) {
			// on initialise les objets
			$query_apl = $this->_db->getQuery(true);
			$query = $db->getQuery(true);
			
			// on interroge la base V2
			$query_apl->select('chantiers.*, categories_chantier.text')->from($this->database.'.chantiers')->leftJoin('categories_chantier ON categories_chantier.id = chantiers.categorie')->where('chantiers.id = '.$id);
			$this->_db->setQuery($query_apl);
			//echo("<pre>".var_dump($this->_db->getQuery())."</pre>");
			$this->_db->execute();
			$rows = $this->_db->loadObjectList();
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
				if (class_exists("GMAPv3")) {
					$chantier->countrycode = GMAPv3::getCountryFromLatLng($chantier->glat, $chantier->glng)->short_name;
				}
				if ($chantier->pays == "") {
					if (class_exists("GMAPv3")) {
						$chantier->pays = GMAPv3::getCountryFromLatLng($chantier->glat, $chantier->glng)->long_name;
					}
				} else {
					$chantier->pays = APLFunctions::escapeString($chantier->pays);
				}
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
				// @reunion 2014.03.12
				// . ne pas importer les fichiers attachés
				//$chantier->fiche_info = ($chantier->fiche_info != '' ? "images/apl".$chantier->fiche_info : "");
				//$chantier->fiche_inscription = ($chantier->fiche_inscription != '' ? "images/apl".$chantier->fiche_inscription : "");
				//$chantier->fiche_custom = ($chantier->fiche_custom != '' ? "images/apl".$chantier->fiche_custom : "");
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
