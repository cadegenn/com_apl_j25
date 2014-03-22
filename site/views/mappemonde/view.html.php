<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the mappemonde Component
 */
class aplViewMappemonde extends JView
{
	// Overwriting JView display method

	function display($tpl = null) 
	{
		// Assign data to the view
		//$this->msg = $this->get('Msg');
		$this->chantier = $this->get('Items');	// ==> /components/com_apl/models/mappemonde.php
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Load scripts
		$this->addScripts();

		// add breadcrumbs
		$app    = JFactory::getApplication();
		$pathway = $app->getPathway();
		//$pathway->addItem(JText::_('COM_APL_WORLDMAP_LABEL'), 'index.php?option=com_apl&view=mappemonde');
		
		// Display the view
		parent::display($tpl);
	}

	/**
     * Add the scripts to the document.
     */
    protected function addScripts()
    {
        $doc = JFactory::getDocument();
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
