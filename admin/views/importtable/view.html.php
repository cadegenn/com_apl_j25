<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * importTable View
 * 
 */
class aplViewImportTable extends JView
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
        // Load styleSheet
        $this->addDocStyle();

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
        JToolBarHelper::title($isNew ? JText::_('COM_APL').' : '.JText::_('COM_APL_MANAGER_IMPORTTABLE_NEW')
                                     : JText::_('COM_APL').' : '.JText::_('COM_APL_MANAGER_IMPORTTABLE_EDIT'));
        JToolBarHelper::save('importTable.save');                                      // --> administrator/components/com_apl/controllers/importTable.php::save();
        JToolBarHelper::cancel('importTable.cancel', $isNew    ? 'JTOOLBAR_CANCEL'     // --> administrator/components/com_apl/controllers/importTable.php::cancel();
                                                            : 'JTOOLBAR_CLOSE');
    }

    /**
     * Add the stylesheet to the document.
     */
    protected function addDocStyle()
    {
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_apl/css/importTable.css');
    }
    
	/*
	 * display_import_table_preview($table_requested)
	 * Affiche les n premières lignes de la table
	 */
	function display_import_table_preview($table_requested) {
		$options = array(   "driver"    => "mysql",
							"database"  => $this->models['importtable']->database,
							"select"    => true,
							"host"      => $this->models['importtable']->db_host,
							"user"      => $this->models['importtable']->db_username,
							"password"  => $this->models['importtable']->db_username
				);
		$db_apl = JDatabaseMySQL::getInstance($options);
		//echo("<pre>".var_dump($db_apl)."</pre>");

		$query = $db_apl->getQuery(true);
		// Select some fields
		$query->select('*');
		// From the hello table
		$query->from($table_requested);
		return $query;
	}
}
