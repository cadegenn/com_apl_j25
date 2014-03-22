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

class JFormFieldchantierscategories extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'chantierscategories';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__apl_chantiers_categories');
		$query->where('published = 1');
		$query->order('id');
		//echo var_dump($query); die();
		$db->setQuery((string)$query);
		//echo var_dump($db); die();
		$categories = $db->loadObjectList();
		//echo var_dump($categories); die();
		$options = array();
		if ($categories)
		{
			foreach($categories as $categorie) 
			{
				//$options[] = JHtml::_('select.option', $chantier->id, $chantier->nom);
				$options[] = JHtml::_('select.option', $categorie->id, $categorie->nom);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		//echo var_dump($options); die();
		return $options;
	}
}

?>
