<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of chantiers component
 */
class aplController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
		// set default view if not set
		//JRequest::setVar('view', JRequest::getCmd('view', 'apl'));
		$input = JFactory::getApplication()->input;
		$input->set('i', $input->getCmd('view', 'apl'));
 
		// call parent behavior
		parent::display($cachable);
		
		//aplHelper::addSubmenu('chantiers');	// => admin/helpers/chantiers.php
	}
}

?>
