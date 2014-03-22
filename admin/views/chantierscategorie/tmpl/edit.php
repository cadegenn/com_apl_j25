<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');
JLoader::register('APLControls', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/controls.php');
JLoader::register('APLdb', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/db.php');


// $this = aplViewChantier
// $this->_models["chantier"] = aplModelChantier
// $this->form = admin/models/forms/chantier.xml

/*$GOOGLEMAP_API_KEY="AIzaSyC_Y4i2K7sWjx87SDbSMZucFVk52Q1Fok8";
$GOOGLEMAP_DEFAULT_ZOOM=7;
$GOOGLEMAP_DEFAULT_TYPE="TERRAIN";
*/

// on charge nos librairies Google
//require(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/googlemap-v3.php');
//$document = JFactory::getDocument();
//$document->addScript(JPATH_COMPONENT_ADMINISTRATOR . '/js/googlemap-v3.js');
require(JPATH_COMPONENT_ADMINISTRATOR . '/js/googlemap-v3.php');
?>

<script type="text/javascript">

/**
 * TRES IMPORTANT !!
 * le 'domready' s'exécute AVANT le 'windows.onload'
 * Il faut que les scripts google soient chargés AVANT d'initialiser quoi que ce soit (une carte, un marker, etc...)
 */
function loadScript() {

	<?php //$catid = JRequest::getVar('id', 0, 'get','int'); ?>
	<?php //if ($catid > 0) : ?>
	document.id('jform_zoomLevel').addEvent('change',updateCurrentMap);
	document.id('jform_mapType').addEvent('change',updateCurrentMap);
}

window.onload = loadScript;

window.addEvent('domready',function(){
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLEMAP_API_KEY; ?>&sensor=false&callback=initialize";
	document.body.appendChild(script);
	//loadScript();
});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_apl&view=chantiersCategorie&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="chantiersCategorie-form">
	<div class='width-60 fltlft'>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_APL_CATEGORIE_DETAILS' ); ?></legend>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("nom")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("nom")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("link")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("link")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo JText::_( 'COM_APL_COORDS_LABEL' ); ?></div>
				<div class="td">
					<?php echo JText::_( 'COM_APL_GLAT_LABEL' ); ?> : <span name="jspan[mapGlat]" id="jspan_mapGlat"><?php echo $this->form->getField("mapGlat")->value; ?></span>
					<?php echo JText::_( 'COM_APL_GLNG_LABEL' ); ?> : <span name="jspan[mapGlng]" id="jspan_mapGlng"><?php echo $this->form->getField("mapGlng")->value; ?></span>
					<?php echo $this->form->getField("mapGlat")->input." ".$this->form->getField("mapGlng")->input; ?>
				</div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getLabel('zoomLevel'); ?></div>
				<div class="td"><?php echo $this->form->getInput('zoomLevel'); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getLabel('mapType'); ?></div>
				<div class="td"><?php echo $this->form->getInput('mapType'); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getLabel('published'); ?></div>
				<div class="td"><?php echo $this->form->getInput('published'); ?></div>
			</div>
				
		</fieldset>
	</div>
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_APL_MAP_PREVIEW'), 'meta-options'); ?>
			<div id='map_canvas'></div>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="chantiersCategorie.edit" />
                <?php JFactory::getApplication()->setUserState('com_apl.edit.chantierscategorie.id', (int) $this->item->id); ?>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php //echo("<pre>"); var_dump($this); echo("</pre>"); ?>