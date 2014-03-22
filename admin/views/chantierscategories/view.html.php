<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * apl View
 */
class aplViewchantiersCategories extends JView
{
	/**
	 * apls view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get data from the model
		$items = $this->get('Items');			// => admin/models/chantiersCategories.php
		$pagination = $this->get('Pagination');
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
 
		// Set the toolbar
		$this->addToolBar();
		
		// Ajouter le sous menu
		APLHelper::addSubmenu('chantiers');	// => admin/helpers/apl.php
		
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
		JToolBarHelper::title(JText::_('COM_APL').' : '.JText::_('COM_APL_CHANTIERS_CATEGORIES'), 'apl');
		JToolBarHelper::addNewX('chantiersCategorie.add');
		JToolBarHelper::editListX('chantiersCategorie.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('chantiersCategories.publish');
		JToolBarHelper::unpublishList('chantiersCategories.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::archiveList('chantiersCategories.archive');
		JToolBarHelper::trash('chantiersCategories.trash');
		JToolBarHelper::deleteListX(JText::_('COM_APL_AREYOUSURE'), 'chantiersCategories.delete');
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
