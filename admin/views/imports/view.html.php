<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * apl View
 */
class aplViewImports extends JView
{
	/**
	 * apls view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		/*
		// Get data from the model
		$items = $this->get('Items');			// => admin/models/imports.php
		$pagination = $this->get('Pagination');
 
		// Check for errors.
		/*if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
		 * 
		 */
 
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
		JToolBarHelper::title(JText::_('COM_APL').' : '.JText::_('COM_APL_SUBMENU_IMPORT_CHANTIERS'), 'apl');
		//JToolBarHelper::custom('imports.import','import','import',JText::_('COM_APL_IMPORT'), true);
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
