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
 * chantier View
 * 
 */
class aplViewChantier extends JView
{
    /**
     * display method of Hello view
     * @return void
     */
    public function display($tpl = null) 
    {
        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) 
        {
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }
        // Assign the Data
        $this->form = $form;
        $this->item = $item;

        // Set the toolbar
        $this->addToolBar();
        // Load styleSheet & scripts
        $this->addDocStyleAndScripts();

		// Ajouter le sous menu
		APLHelper::addSubmenu('chantiers');	// => admin/helpers/apl.php
		
        // Display the template
        parent::display($tpl);		// -> ./tmpl/edit.php
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() 
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
		
		// si JCE est présent, on ajoute un bouton JCE Browser
		if (is_dir(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce')) {
			require_once(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'helpers' .DS. 'browser.php');
			$bar=& JToolBar::getInstance( 'toolbar' );
			$bar->appendButton( 'Popup', 'stats', 'Explorateur',  WFBrowserHelper::getBrowserLink(), 800, 500 );
			JToolBarHelper::divider();
		}
        JToolBarHelper::title($isNew ? JText::_('COM_APL').' : '.JText::_('COM_APL_MANAGER_CHANTIER_NEW')
                                     : JText::_('COM_APL').' : '.JText::_('COM_APL_MANAGER_CHANTIER_EDIT'));
        JToolBarHelper::apply('chantier.apply');                                      // --> administrator/components/com_apl/controllers/chantier.php::save();
        JToolBarHelper::save('chantier.save');                                      // --> administrator/components/com_apl/controllers/chantier.php::save();
        JToolBarHelper::save2new('chantier.save2new');                                      // --> administrator/components/com_apl/controllers/chantier.php::save();
        JToolBarHelper::save2copy('chantier.save2copy');                                      // --> administrator/components/com_apl/controllers/chantier.php::save();
        JToolBarHelper::cancel('chantier.cancel', $isNew    ? 'JTOOLBAR_CANCEL'     // --> administrator/components/com_apl/controllers/chantier.php::cancel();
                                                            : 'JTOOLBAR_CLOSE');
    }

    /**
     * Add the stylesheets and scripts to the document.
     */
    protected function addDocStyleAndScripts()
    {
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_apl/css/chantier.css');
        $doc->addScript('components/com_apl/js/fonctions.js');
		$params = JComponentHelper::getParams('com_apl');
		
		switch ($params->get("map_provider")) {
			case 'googlemap-v3'	:	$doc->addStyleSheet('components/com_apl/css/googlemap-v3.css');
									break;
			case 'osm-leaflet'	:	$doc->addStyleSheet('components/com_apl/css/osm-leaflet.css');
									$doc->addStyleSheet('http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css');
									break;
		}
    }
    
}
