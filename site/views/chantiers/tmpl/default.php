<?php
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
			script.src = "http://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLEMAP_API_KEY; ?>&sensor=false&callback=initialize";
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
		<input type="button" class="btn_right" onclick="localiser(<?php echo $item->glat; ?>, <?php echo $item->glng; ?>);" value="Localiser">
		<a href='index.php?option=com_apl&view=chantier&id=<?php echo $item->id; ?>&Itemid=<?php echo JRequest::getVar('Itemid', 0, 'get','int'); ?>'><?php echo $item->nom; ?></a>
		<?php if ($item->sous_titre != '') : ?>
			<em>- <?php echo $item->sous_titre; ?></em>
		<?php endif; ?>
		<?php if ((strtotime($item->date_fin) > 0) and (strtotime($item->date_fin) <= strtotime("now"))) : ?>
			- <span class='finished'>[TERMINÉ]</span>
		<?php endif; ?>
		<br />
		<span><?php echo $item->lieu; ?></span><br />
		<img src='<?php echo $this->baseurl."/templates/".$template; ?>/images/ico_16x16/date.png' alt='date' /><?php echo APLFunctions::read_date_as_human($item->date_debut, $item->date_fin, $item->date_exacte); ?> <br />
		<?php if ($item->complet) : ?>
			<span class='complet'>[COMPLET]</span>
		<?php endif; ?>
		<?php if ($item->test) : ?>
			<span class='test'>[EN TEST]</span>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
</div>
<?php //echo("<pre>");var_dump($item);echo("</pre>"); ?>

<a name='map' id='map'></a>
<div id='map_canvas'></div><br />

