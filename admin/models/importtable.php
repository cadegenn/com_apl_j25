<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * HelloWorld Model
 */
class aplModelImportTable extends JModelAdmin
{
	/*
	 * database from wich to import data
	 */
	protected $database = "apasdelo2011";
	/*
	 * hostname where lives the database
	 */
	protected $db_host = "localhost";
	/*
	 * username to access database
	 */
	protected $db_username = "root";
	/*
	 * password of the user
	 */
	protected $db_passwd = "alpha1";
	

    /**
    * Returns a reference to the a ImportTable object, always creating it.
    *
    * @param	type	The table type to instantiate
    * @param	string	A prefix for the table class name. Optional.
    * @param	array	Configuration array for model. Optional.
    * @return	JImportTable	A database object
    * @since	2.5
    */
    public function getTable($type = 'importTable', $prefix = 'aplTable', $config = array()) 
    {
		$options = array(  
			"driver"    => "mysql",
			"database"  => $this->database,
			"select"    => true,
			"host"      => $this->db_host,
			"user"      => $this->db_username,
			"password"  => $this->db_passwd
		);
		$db_apl = JDatabaseMySQL::getInstance($options);
		$config['dbo'] = $db_apl;
		
		return JTable::getInstance($type, $prefix, $config);    // --> administrator/components/com_apl/tables/importtable.php
    }
    /**
    * Method to get the record form.
    *
    * @param	array	$data		Data for the form.
    * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
    * @return	mixed	A JForm object on success, false on failure
    * @since	2.5
    */
    public function getForm($data = array(), $loadData = true) 
    {
	   // Get the form.
	   $form = $this->loadForm('com_apl.importTable', 'importTable', // --> models/forms/importTable.xml
				   array('control' => 'jform', 'load_data' => $loadData));
	   if (empty($form)) 
	   {
		   return false;
	   }
	   return $form;
    }
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	2.5
    */
    protected function loadFormData() 
    {
	   // Check the session for previously entered form data.
	   $data = JFactory::getApplication()->getUserState('com_apl.edit.importTable.data', array());
	   if (empty($data)) 
	   {
		   $data = $this->getItem();
	   }
	   return $data;
    }

}
