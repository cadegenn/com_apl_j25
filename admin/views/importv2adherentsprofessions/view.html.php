<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * apl View
 */
class aplViewImportV2AdherentsProfessions extends JView
{
	/**
	 * apls view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		if ($this->_models['importv2adherentsprofessions']->_db->getErrorNum() > 0) { 
			$items = "";
		} else {
			$items = $this->get('Items');			// => admin/models/importV2Chantiers.php

			// Check for errors.
			if (count($errors = $this->get('Errors'))) 
			{
				JError::raiseError(500, implode('<br />', $errors));
				return false;
			}
		}

		$pagination = $this->get('Pagination');
		
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
 
		// Set the toolbar
		$this->addToolBar();
		
		// Ajouter le sous menu
		APLHelper::addSubmenu('import');	// => admin/helpers/apl.php
		
		// Display the template
		parent::display($tpl);
		
		// Set the document
		$this->setDocument();
	}
	
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		// voir d'autres boutons dans /administrator/includes/toolbar.php
		JToolBarHelper::title(JText::_('COM_APL').' : '.JText::_('COM_APL_SUBMENU_IMPORT_ADHERENTSPROFESSIONS'), 'apl');
		//JToolBarHelper::deleteListX('', 'apl.delete');
		//JToolBarHelper::editListX('importV2Chantier.edit');
		//JToolBarHelper::addNewX('importV2Chantier.add');
		//JToolBarHelper::preferences('com_apl');
		JToolBarHelper::custom('importV2AdherentsProfessions.import','import','import',JText::_('COM_APL_IMPORT'), true);
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_apl');
	}
	
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_APL_ADMINISTRATION'));
	}
}

?>
