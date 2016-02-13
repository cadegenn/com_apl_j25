<?php
/**
 * @package		Volontaires Pour la Nature <http://www.apasdeloup.org>
 * @subpackage	com_apl_j25
 * @brief		Joomla! 2.5 core component
 * @copyright	Copyright (C) 2010 - 2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 * @license		Affero GNU General Public License version 3 or later; see LICENSE.txt
 * 
 * @TODO		replace hard-coded calendar img with a div with class='calendar'
 */

/** 
 *  Copyright (C) 2012-2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 *  com_apl is a joomla! 2.5 component [http://www.apasdeloup.org]
 *  
 *  This file is part of com_apl.
 * 
 *     com_apl is free software: you can redistribute it and/or modify
 *     it under the terms of the Affero GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     com_apl is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     Affero GNU General Public License for more details.
 * 
 *     You should have received a copy of the Affero GNU General Public License
 *     along with com_apl.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');
JLoader::register('APLdb', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/db.php');

$template = JFactory::getApplication()->getTemplate();

$PHOTOS_DIR=$this->baseurl."/images/apl/chantiers/photos";

$params = JComponentHelper::getParams('com_apl');
switch ($params->get("map_provider")) {
	case 'googlemap-v3'	:	require(JPATH_COMPONENT_ADMINISTRATOR . '/js/googlemap-v3.php');
							break;
	case 'osm-leaflet'	:	require(JPATH_COMPONENT_ADMINISTRATOR . '/js/osm-leaflet.php');
							break;
}
?>

<script type="text/javascript">

/**
 * TRES IMPORTANT !!
 * le 'domready' s'exécute AVANT le 'windows.onload'
 * Il faut que les scripts google soient chargés AVANT d'initialiser quoi que ce soit (une carte, un marker, etc...)
 */
window.addEvent('domready',function(){
	<?php switch ($params->get("map_provider")) :
		case 'googlemap-v3'	: ?>
			/*var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "http://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLEMAP_API_KEY; ?>&callback=initialize";
			document.body.appendChild(script);*/
			//loadScript();
		<?php break;
		case 'osm-leaflet'	: ?>
			initialize();
		<?php break;
	endswitch; ?>
});

function loadScript() {
	//mgr = new MarkerManager(map);
	<?php foreach($this->chantier as $i => $item) : ?>
			createMarkerFromLatLng(<?php echo $item->glat; ?>, <?php echo $item->glng; ?>, '<?php //echo $item->categorie_text; ?>', '<?php echo addslashes($item->nom);/*html_entity_decode($row['nom'], ENT_QUOTES, 'UTF-8')*/ ?>', '<?php echo APLFunctions::mynl2br(addslashes($item->actions));/*html_entity_decode($row['actions'], ENT_COMPAT, 'UTF-8')*/; ?><br /><p style="text-align:right;"><a href=#Liste_Commentaires><?php //echo $item->nb_commentaires; ?> commentaire(s)</a>.</p>', 'index.php?option=com_apl&view=chantier&id=<?php echo $item->id; ?>&Itemid=<?php echo JRequest::getVar('Itemid', 0, 'get','int'); ?>', false, true);			
	<?php endforeach; ?>
	//map.addControl(new GLargeMapControl3D());
	//mgr.addMarkers(markers, 1);
	//mgr.refresh();
}

window.onload = loadScript;

</script>

<h1><?php echo $this->document->title; ?></h1>
<?php //echo("<pre>");var_dump($this);echo("</pre>"); ?>
<div>
<?php foreach($this->chantier as $i => $item) : ?>
	<?php //echo("<pre>");var_dump($item); echo("</pre>"); ?>
	<div class='chantier_preview'>
		<ul class="action_tag">
				<li><!-- empty li to force "localize" to display correctly --></li>
			<?php if ($item->complet) : ?>
				<li class='action_tag_complet' title="<?php echo JText::_('COM_APL_CHANTIER_COMPLET_TOOLTIP'); ?>">[C]<!--<a href="#"><img src="<?php echo JURI::base(); ?>components/com_apl/images/ico-16x16/complet.png" alt="<?php echo JText::_('COM_APL_CHANTIER_COMPLET_TOOLTIP'); ?>" title="<?php echo JText::_('COM_APL_CHANTIER_COMPLET_TOOLTIP'); ?>" /></a>--></li>
			<?php else: ?>
				<li><!-- empty li to force 16px space --></li>
			<?php endif; ?>
			<?php if ($item->test) : ?>
				<li class='action_tag_test' title="<?php echo JText::_('COM_APL_CHANTIER_TEST_TOOLTIP'); ?>">[T]<!--<a href="#"><img src="<?php echo JURI::base(); ?>components/com_apl/images/ico-16x16/test.png" alt="<?php echo JText::_('COM_APL_CHANTIER_TEST_TOOLTIP'); ?>" title="<?php echo JText::_('COM_APL_CHANTIER_TEST_TOOLTIP'); ?>" /></a>--></li>
			<?php else: ?>
				<li><!-- empty li to force 16px space --></li>
			<?php endif; ?>
			<?php if ($item->vpn) : ?>
				<!-- CHANTIER VPN -->
				<li class='action_tag_vpn'><a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id='.$params->get("faq-tag-vpn").''); ?>"><img src="<?php echo JURI::base(); ?>components/com_apl/images/ico-16x16/apl.png" alt="<?php echo JText::_('COM_APL_CHANTIER_VPN_TOOLTIP'); ?>" title="<?php echo JText::_('COM_APL_CHANTIER_VPN_TOOLTIP'); ?>" /></a></li>
			<?php else: ?>
				<li><!-- empty li to force 16px space --></li>
			<?php endif; ?>
		</ul>
		<input type="button" class="btn_localiser" onclick="localiser(<?php echo $item->glat; ?>, <?php echo $item->glng; ?>);" value="Localiser">
		<a href='index.php?option=com_apl&view=chantier&id=<?php echo $item->id; ?>&Itemid=<?php echo JRequest::getVar('Itemid', 0, 'get','int'); ?>'><?php echo $item->nom; ?></a>
		<?php if ($item->sous_titre != '') : ?>
			<em>- <?php echo $item->sous_titre; ?></em>
		<?php endif; ?>
		<?php if ((strtotime($item->date_fin) > 0) and (strtotime($item->date_fin) <= strtotime("now"))) : ?>
			- <span class='finished'>[TERMINÉ]</span>
		<?php endif; ?>
		<br />
		<span><?php echo $item->lieu; ?></span><br />
		<img src='<?php echo $this->baseurl."/templates/".$template; ?>/images/ico_16x16/calendar.png' alt='date' /><?php echo APLFunctions::read_date_as_human($item->date_debut, $item->date_fin, $item->date_exacte); ?> <br />
	</div>
<?php endforeach; ?>
</div>
<?php //echo("<pre>");var_dump($item);echo("</pre>"); ?>

<a name='map' id='map'></a>
<div id='map_canvas'></div><br />

