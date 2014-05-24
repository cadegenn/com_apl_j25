<?php
/* 
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
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the chantiers Component
 */
class aplViewchantier extends JView
{
	// Overwriting JView display method

	function display($tpl = null) {
		// Assign data to the view
		$this->chantier = $this->get('Chantier');	// ==> /components/com_apl/models/chantier.php
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// add breadcrumbs
		$app    = JFactory::getApplication();
		$pathway = $app->getPathway();
		//$pathway->addItem(html_entity_decode($this->chantier->nom,ENT_QUOTES, 'UTF-8'), 'index.php?option=com_apl&view=chantier&id='.$this->chantier->id.'&Itemid='.JRequest::getVar('Itemid', 0, 'get','int'));
		$pathway->addItem(JText::_('COM_APL_CHANTIER_DETAILS'));
		// Load styleSheet & scripts
        $this->addDocStyleAndScripts();
		
		// Display the view
		parent::display($tpl);
	}

    /**
     * Add the stylesheets and scripts to the document.
     */
    protected function addDocStyleAndScripts()
    {
        $doc = JFactory::getDocument();
        //$doc->addStyleSheet('components/com_apl/css/chantier.css');
        $doc->addScript('administrator/components/com_apl/js/fonctions.js');
		$params = JComponentHelper::getParams('com_apl');
		
		switch ($params->get("map_provider")) {
			case 'googlemap-v3'	:	//$doc->addStyleSheet('components/com_apl/css/googlemap-v3.css');
									break;
			case 'osm-leaflet'	:	//$doc->addStyleSheet('components/com_apl/css/osm-leaflet.css');
									$doc->addStyleSheet('http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css');
									break;
		}
    }
    
	
}

?>
