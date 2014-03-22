<?php
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
		//$this->msg = $this->get('Msg');
		//$this->msg = "APL : Chantiers";
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
		$pathway->addItem(html_entity_decode($this->chantier->nom,ENT_QUOTES, 'UTF-8'), 'index.php?option=com_apl&view=chantier&id='.$this->chantier->id.'&Itemid='.JRequest::getVar('Itemid', 0, 'get','int'));
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
        $doc->addScript('components/com_apl/js/fonctions.js');
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
