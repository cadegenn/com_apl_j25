<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * apl component helper.
 */
abstract class APLHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		switch ($submenu) {
			case 'chantiers' :	JSubMenuHelper::addEntry(JText::_('COM_APL_SUBMENU_CHANTIERS'), 'index.php?option=com_apl&view=chantiers', true);
								JSubMenuHelper::addEntry(JText::_('COM_APL_SUBMENU_CHANTIERSCATEGORIES'), 'index.php?option=com_apl&view=chantiersCategories', true);
								break;
			case 'import'	 :	JSubMenuHelper::addEntry(JText::_('COM_APL_SUBMENU_IMPORT_CHANTIERS'), 'index.php?option=com_apl&view=importV2Chantiers', true);
								JSubMenuHelper::addEntry(JText::_('COM_APL_SUBMENU_IMPORT_CHANTIERSCATEGORIES'), 'index.php?option=com_apl&view=importV2ChantiersCategories', true);
								JSubMenuHelper::addEntry(JText::_('COM_APL_SUBMENU_IMPORT_ARTICLES'), 'index.php?option=com_apl&view=importV2Articles', true);
								break;
		}
		// set some global property
		/*$document = JFactory::getDocument();
		//$document->addStyleDeclaration('.icon-48-categories ' .
		//                               '{background-image: url(../components/com_apl/images/ico-48x48/apl.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_APL_ADMINISTRATION_CATEGORIES'));
		}*/
	}
}
?>
