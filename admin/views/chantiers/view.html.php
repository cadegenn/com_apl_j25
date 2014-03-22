<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * apl View
 */
class aplViewchantiers extends JView
{
	/**
	 * apls view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get data from the model
		$items = $this->get('Items');			// => admin/models/chantiers.php
		$state = $this->get('State');
		$pagination = $this->get('Pagination');
		$categories = $this->get('Categories');
		$pays = $this->get('Pays');
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->state = $state;
		$this->pagination = $pagination;
		$this->categories = $categories;
		$this->pays = $pays;
        $this->authors = $this->get('Authors');
 
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
		JToolBarHelper::title(JText::_('COM_APL').' : '.JText::_('COM_APL_CHANTIERS'), 'apl');
		JToolBarHelper::addNewX('chantier.add');
		JToolBarHelper::editListX('chantier.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('chantiers.publish');
		JToolBarHelper::unpublishList('chantiers.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::archiveList('chantiers.archive');
		JToolBarHelper::trash('chantiers.trash');
		JToolBarHelper::deleteListX(JText::_('COM_APL_AREYOUSURE'),'chantiers.delete');
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
