<?php
// No direct access to this file
defined('_JEXEC') or die;

/*
 * Cette class sert à afficher des options dans la page
 * [Menu] >> "Menu principal" >> "ajouter un lien de menu"
 * 
 * Un nouveau bloc s'affiche sur la droite de l'écran intitulé "Paramètres requis"
 */
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * chantiers Form Field class for the chantiers component
 */

class JFormFieldchantiers extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'chantiers';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		/*$query->select('#__apl_chantiers.id, #__apl_chantiers.nom, #__categories.title as category,catid');
		$query->from('#__apl_chantiers');
		$query->leftJoin('#__categories on catid=#__categories.id');
		$query->where('published = 1');
		$query->order('nom');
		echo("<pre>".var_dump($db)."</pre>");
		 * 
		 */
		$query->select('#__apl_chantiers.*, #__apl_chantiers_categories.nom as categorie');
		$query->from('#__apl_chantiers');
		$query->leftJoin('#__apl_chantiers_categories on #__apl_chantiers_categories.id = #__apl_chantiers.catid');
		$query->where('#__apl_chantiers.published = 1');
		$query->order('#__apl_chantiers.catid');
		$query->order('#__apl_chantiers.nom');
		$db->setQuery((string)$query);
		$chantiers = $db->loadObjectList();
		$options = array();
		if ($chantiers)
		{
			foreach($chantiers as $chantier) 
			{
				$options[] = JHtml::_('select.option', $chantier->id, html_entity_decode($chantier->nom, ENT_QUOTES, 'UTF-8').' ('.html_entity_decode($chantier->categorie, ENT_QUOTES, 'UTF-8').')');
				//$options[] = JHtml::_('select.option', $chantier->id, $chantier->nom .
				//                      ($chantier->category ? ' (' . $chantier->text . ')' : ''));
				
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}

?>
