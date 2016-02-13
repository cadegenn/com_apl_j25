<?php
/* 
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

$db = JFactory::getDbo();
$db->setQuery('SELECT * FROM #__apl_chantiers_categories WHERE id = '.$this->chantier->catid.' LIMIT 1');
//echo("<pre>");var_dump($this->chantier); echo("</pre>");
$categorie = $db->loadObject();
//echo("<pre>");var_dump($categorie); echo("</pre>");
$PHOTOS_DIR="images/apl/chantiers/".$categorie->nom."/photos/".$this->chantier->id;

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
function loadScript() {

	<?php $id = JRequest::getVar('id', 0, 'get','int'); ?>
	<?php if ($id > 0) : ?>
		var myMarker = createMarkerFromLatLng(<?php echo $this->chantier->glat; ?>, <?php echo $this->chantier->glng; ?>, null, null, null, "#", false, true);
		centerMap(<?php echo $this->chantier->glat; ?>, <?php echo $this->chantier->glng; ?>);
	<?php endif; ?>
}

window.onload = loadScript;

window.addEvent('domready',function(){
	<?php switch ($params->get("map_provider")) :
		case 'googlemap-v3'	: ?>
			/*var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "http://maps.googleapis.com/maps/api/js?key=<?php //echo $GOOGLEMAP_API_KEY; ?>&callback=initialize";
			document.body.appendChild(script);*/
			//loadScript();
		<?php break;
		case 'osm-leaflet'	: ?>
			initialize();
		<?php break;
	endswitch; ?>
});
</script>

<?php if ($this->chantier->vpn) : ?>
	<!-- CHANTIER VPN -->
	<div class='tag_vpn'><a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id='.$params->get("faq-tag-vpn").''); ?>"><img src="<?php echo JURI::base(); ?>components/com_apl/images/ico-32x32/apl.png" alt="<?php echo JText::_('COM_APL_CHANTIER_VPN_TOOLTIP'); ?>" title="<?php echo JText::_('COM_APL_CHANTIER_VPN_TOOLTIP'); ?>" /></a></div>
<?php endif; ?>
<?php if ($this->chantier->test) : ?>
	<!-- CHANTIER TEST -->
	<div class='tag_test'>MISSION TEST</div>
<?php endif; ?>
<?php if ((strtotime($this->chantier->date_fin) > 0) and (strtotime($this->chantier->date_fin) <= strtotime("now"))) : ?>
	<!-- CHANTIER TERMINÉ -->
	<div class='tag_finished'>( TERMINÉ )</div>
<?php endif; ?>
<?php if ($this->chantier->complet) : ?>
	<!-- CHANTIER COMPLET -->
	<div class='tag_complet'>( COMPLET )</div>
<?php endif; ?>


<h1><?php echo $this->chantier->nom; ?>
<em class="sous-titre"><?php echo $this->chantier->sous_titre; ?></em></h1>

<div></div>
<!-- Google Map -->
<div id="map_canvas" class="map_thumb"></div>

<?php
/**
 * Au hasard : on affiche ou pas une photo.
 * Au hasard : on choisis le paragraphe ou elle apparaitra
 */
if ((is_dir(getcwd().'/'.$PHOTOS_DIR))) { // and (mt_rand(0,1))) {
		// on randomize le style
		if (mt_rand(0,1)) { $class = "_left"; } else { $class = "_right"; }
		$photo = APLFunctions::getRandomImage($PHOTOS_DIR);
		if ($photo != "") {
			$photo = "<img class='photo_chantier".$class."' src='".$this->baseurl.'/'.$photo."' alt='photo_chantier.png' />";
		}
		$paragraph = mt_rand(3,6);
	} else {
		$paragraph = 0;
	}
	//$photo = APLFunctions::getRandomImage($PHOTOS_DIR);
	//$photo = "<img class='photo_chantier' src='".$this->baseurl.'/'.APLFunctions::getRandomImage($PHOTOS_DIR)."' alt='photo_chantier.png' style='".$style."' />";
	//$photo = "<img class='photo_chantier' src='".getcwd().'/'.$PHOTOS_DIR."' alt='photo_chantier.png' style='".$style."' />";
	//$paragraph=3;
?>
<div id='vpn_content'>
	<!-- DONNEES DU CHANTIER -->
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_WHERE'); ?></h3>		<?php echo((($paragraph == 1) ? $photo : ""));	echo($this->chantier->lieu); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_WHO'); ?></h3>		<?php echo((($paragraph == 2) ? $photo : ""));	echo(APLFunctions::mynl2br($this->chantier->organisateurs)); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_CONTEXT'); ?></h3>	<?php echo((($paragraph == 3) ? $photo : ""));	echo(APLFunctions::mynl2br($this->chantier->contexte)); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_ACTIONS'); ?></h3>	<?php echo((($paragraph == 4) ? $photo : ""));	echo(APLFunctions::mynl2br($this->chantier->actions)); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_I'); ?></h3>			<?php echo((($paragraph == 5) ? $photo : ""));	echo(APLFunctions::mynl2br($this->chantier->benevole)); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_WHEN'); ?></h3>		<?php echo((($paragraph == 6) ? $photo : ""));	//echo(read_date_as_human($this->chantier->date_debut, $this->chantier->date_fin, $this->chantier->date_exacte)); ?><?php echo(htmlspecialchars_decode(stripslashes($this->chantier->date_text))); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_PROFILE'); ?></h3>	<?php echo((($paragraph == 7) ? $photo : ""));	echo(APLFunctions::mynl2br($this->chantier->profile)); ?></div>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_HOWMUCH'); ?></h3>	<?php echo((($paragraph == 8) ? $photo : ""));	echo(APLFunctions::mynl2br($this->chantier->cout_text)); ?></div>
	<?php if ($this->chantier->fiche_info != "") : ?>
		<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_MORE'); ?></h3> Consultez la <a target='_blank' href='<?php echo($this->baseurl."/".$this->chantier->fiche_info); ?>'>pr&eacute;sentation d&eacute;taill&eacute;e</a> <sup><img src='<?php echo($this->baseurl."/templates/".$template); ?>/images/ico_16x16/ico_new_window.png' alt='ico_new_window' /></sup> associ&eacute;e &agrave; ce chantier.</div>
	<?php endif; ?>
	<?php if ($this->chantier->fiche_inscription != "") : ?>
		<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_REGISTER'); ?></h3> T&eacute;l&eacute;chargez la <a target='_blank' href='<?php echo($this->baseurl."/".$this->chantier->fiche_inscription); ?>'>fiche d'inscription</a> <sup><img src='<?php echo($this->baseurl."/templates/".$template); ?>/images/ico_16x16/ico_new_window.png' alt='ico_new_window' /></sup> associ&eacute;e &agrave; ce chantier.</div>
	<?php endif; ?>
	<?php if ($this->chantier->fiche_custom != "") : ?>
		<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_CUSTOM'); ?></h3> Encore plus d'infos <a target='_blank' href='<?php echo($this->baseurl."/".$this->chantier->fiche_custom); ?>'>ici</a> <sup><img src='<?php echo($this->baseurl."/templates/".$template); ?>/images/ico_16x16/ico_new_window.png' alt='ico_new_window' /></sup>.</div>
	<?php endif; ?>
</div>

<pre><?php //var_dump($this); ?></pre>
<pre><?php //var_dump($template); ?></pre>
