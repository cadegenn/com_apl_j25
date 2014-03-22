<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * chantier Model
 */
class aplModelchantier extends JModelItem
{
	/**
	 * @var array messages
	 */
	protected $chantier;
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	2.5
	 */
	public function getTable($type = 'chantier', $prefix = 'aplTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @param  int    The corresponding id of the message to be retrieved
	 * @return string The message to be displayed to the user
	 */
	public function getChantier($id = 0) 
	{
		//$id = (isset($_GET['id']) ? intval ($_GET['id']) : 0);
		$id = JRequest::getVar('id', 0, 'get','int');
		if (!is_array($this->chantier))
		{
			$this->chantier = array();
		}
 
		if (!isset($this->chantier[$id])) 
		{
			// Get a Tablechantier instance
			$table = $this->getTable();
 
			// Load the message
			$table->load($id);
			//echo("<pre>"); var_dump($table);echo("</pre>");
			
			// Assign the message
			//$this->chantier[$id] = $table->id;
		}
 
		//return $this->chantier[$id];
		return $table;
	}
}

?>
