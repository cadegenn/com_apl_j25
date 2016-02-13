<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');

$template = JFactory::getApplication()->getTemplate();

$db = JFactory::getDbo();
$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_apl"');
$params = json_decode( $db->loadResult(), true );
$GOOGLEMAP_API_KEY=$params['map_apikey'];
//$GOOGLEMAP_API_KEY="AIzaSyC_Y4i2K7sWjx87SDbSMZucFVk52Q1Fok8";
$GOOGLEMAP_DEFAULT_ZOOM=1;
$GOOGLEMAP_DEFAULT_TYPE="TERRAIN";

$db->setQuery('SELECT * FROM #__apl_chantiers_categories WHERE id = '.$this->chantier->catid.' LIMIT 1');
//echo("<pre>");var_dump($this->chantier); echo("</pre>");
$categorie = $db->loadObject();
//echo("<pre>");var_dump($categorie); echo("</pre>");
$PHOTOS_DIR="images/apl/chantiers/".$categorie->nom."/photos/".$this->chantier->id;
?>


<script type="text/javascript">
// google map v3
// voir https://developers.google.com/maps/documentation/javascript/
function initialize() {
	var myOptions = {
		zoom: <?php echo $GOOGLEMAP_DEFAULT_ZOOM; ?>,
		disableDefaultUI: true,
		center: new google.maps.LatLng(<?php echo $this->chantier->glat; ?>, <?php echo $this->chantier->glng; ?>),
		mapTypeId: google.maps.MapTypeId.<?php echo $GOOGLEMAP_DEFAULT_TYPE; ?>
	}
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $this->chantier->glat; ?>, <?php echo $this->chantier->glng; ?>),
		map: map,
		title:"<?php echo $this->chantier->nom; ?>"
	});
}

function loadScript() {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLEMAP_API_KEY; ?>&callback=initialize";
	document.body.appendChild(script);
}

window.onload = loadScript;
</script>

<?php if ($this->chantier->test) : ?>
	<!-- CHANTIER TEST -->
	<div class='mission_test'>MISSION TEST</div>
<?php endif; ?>
<?php if ($this->chantier->complet) : ?>
	<!-- CHANTIER COMPLET -->
	<div class='complet'>( COMPLET )</div>
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
<?php if ($this->chantier->fiche_info != "") : ?>
	<div class='detail_chantier'><h3><?php echo JText::_('COM_APL_CHANTIER_REGISTER'); ?></h3> T&eacute;l&eacute;chargez la <a target='_blank' href='<?php echo($this->baseurl."/".$this->chantier->fiche_inscription); ?>'>fiche d'inscription</a> <sup><img src='<?php echo($this->baseurl."/templates/".$template); ?>/images/ico_16x16/ico_new_window.png' alt='ico_new_window' /></sup> associ&eacute;e &agrave; ce chantier.</div>
<?php endif; ?>

<pre><?php //var_dump($this); ?></pre>
<pre><?php //var_dump($template); ?></pre>
