<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Set some global property
$document = JFactory::getDocument();
//$document->addStyleDeclaration('.icon-48-categories, .icon-48-apl {background-image: url(components/com_apl/images/ico-48x48/apl.png);}');
//$document->addStyleDeclaration('.ico-32-import {background-image: url(components/com_apl/images/ico-128x128/import-icon.png); width:32px;}');
$document->addStyleSheet( 'components/com_apl/css/com_apl.css' );

// require helper file
JLoader::register('aplHelper', dirname(__FILE__).'/helpers/apl.php');

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by chantiers
$controller = JController::getInstance('apl');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();

?>
