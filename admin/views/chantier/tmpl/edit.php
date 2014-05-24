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

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

// Import library dependencies
JLoader::register('APLFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');
JLoader::register('APLControls', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/controls.php');
JLoader::register('APLdb', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/db.php');

//jimport('joomla.application.component.helper');

// $this = aplViewChantier
// $this->_models["chantier"] = aplModelChantier
// $this->form = admin/models/forms/chantier.xml

/*$GOOGLEMAP_API_KEY="AIzaSyC_Y4i2K7sWjx87SDbSMZucFVk52Q1Fok8";
$GOOGLEMAP_DEFAULT_ZOOM=7;
$GOOGLEMAP_DEFAULT_TYPE="TERRAIN";
*/
//echo("<pre>"); var_dump($params); echo("</pre>");
// on charge nos librairies Google
//require(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/googlemap-v3.php');
//$document = JFactory::getDocument();
//$document->addScript(JPATH_COMPONENT_ADMINISTRATOR . '/js/googlemap-v3.js');
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
		var myMarker = createMarkerFromLatLng(<?php echo $this->form->getField("glat")->value; ?>, <?php echo $this->form->getField("glng")->value; ?>, <?php echo addslashes($this->form->getField("catid")->value); ?>, "<?php echo addslashes($this->form->getField("nom")->value); ?>", "<?php echo addslashes(APLFunctions::mynl2br($this->form->getField("actions")->value)); ?>", "#", true, true);
	<?php endif; ?>
	//console.log(myMarker);
	document.id('jform_nom').addEvent('change',updateCurrentMarker);
	document.id('jform_adresse').addEvent('change',updateCurrentMarker);
	document.id('jform_actions').addEvent('change',updateCurrentMarker);
}

window.onload = loadScript;

window.addEvent('domready',function(){
	<?php switch ($params->get("map_provider")) :
		case 'googlemap-v3'	: ?>
		<?php break;
		case 'osm-leaflet'	: ?>
			initialize();
		<?php break;
	endswitch; ?>
});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_apl&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="chantier-form">
	<div class='width-60 fltlft'>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_APL_CHANTIER_DETAILS' ); ?> <small>(<?php echo $this->item->id; ?>)</small></legend>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("nom")->label; ?></div><?php //onblur="alert(document.getElementById('categorie').options[document.getElementById('categorie').value].text);updateMarkerFromLatLng(marker,null,null,document.getElementById('categorie').options[document.getElementById('categorie').value].text,this.value,document.getElementById('actions').value,null,true,true);"?>
				<div class="td"><?php echo html_entity_decode($this->form->getField("nom")->input); ?></div>
				<div class="th"><?php echo $this->form->getField("sous_titre")->label; ?></div><?php //onblur="alert(document.getElementById('categorie').options[document.getElementById('categorie').value].text);updateMarkerFromLatLng(marker,null,null,document.getElementById('categorie').options[document.getElementById('categorie').value].text,this.value,document.getElementById('actions').value,null,true,true);"?>
				<div class="td"><?php echo html_entity_decode($this->form->getField("sous_titre")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("lieu")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("lieu")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("adresse")->label; ?><br />
								<span class="help"><?php echo JText::_('COM_APL_CHANTIER_ADRESSE_DESC'); ?></span>
				</div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("adresse")->input); //." ".$this->form->getField("pays")->label." ".$this->form->getField("pays")->input; ?><br />
								<?php echo JText::_('COM_APL_CHANTIER_ADDRESS_BY_GOOGLE'); ?> : <span name="jform[formattedAddress]" id="jform_formattedAddress"></span><br />
								<?php //echo JText::_('COM_APL_CHANTIER_CATEGORIE_LABEL')." ".$this->form->getField("catid")->input; ?>
								<label style="float: left;" for="jform_catid"><?php echo JText::_('COM_APL_CHANTIER_CATEGORIE_LABEL');?></label> <?php echo APLcontrols::selectChantiersCategories($this->item->catid); ?>
								<?php echo $this->form->getField("glat")->label; ?><span name="jspan[glat]" id="jspan_glat"><?php echo $this->form->getField("glat")->value; ?></span> <?php echo $this->form->getField("glng")->label; ?> <span name="jspan[glng]" id="jspan_glng"><?php echo $this->form->getField("glng")->value;?></span> <label>/</label>
								<?php echo $this->form->getField("glat")->input." ".$this->form->getField("glng")->input; ?>
								<?php echo $this->form->getField("countrycode")->input; ?> <span name="jspan[countrycode]" id="jspan_countrycode" style="display: none;"><?php echo $this->form->getField("countrycode")->value; ?></span>
								<?php echo $this->form->getField("pays")->input; ?> <span name="jspan[pays]" id="jspan_pays"><?php echo $this->form->getField("pays")->value; ?></span>
								<br /><span class="help"><?php echo JText::_('COM_APL_CHANTIER_GLATLNG_HELP'); ?></span>
				</div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("organisateurs")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("organisateurs")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("contexte")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("contexte")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("actions")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("actions")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("benevole")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("benevole")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("date_text")->label; ?></div>
				<div class="td">
					<ul class="adminformlist-inline">
						<li><?php echo $this->form->getField("date_debut")->label; ?></li>
						<li><?php echo $this->form->getField("date_debut")->input; ?></li>
						<li><?php echo $this->form->getField("date_fin")->label; ?></li>
						<li><?php echo $this->form->getField("date_fin")->input; ?></li>
					</ul>
					<?php echo $this->form->getField("date_exacte")->label; ?>
					<?php echo $this->form->getField("date_exacte")->input; ?>
					
					<?php echo html_entity_decode($this->form->getField("date_text")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("profile")->label; ?></div>
				<div class="td"><?php echo html_entity_decode($this->form->getField("profile")->input); ?></div>
			</div>
			<div class="tr">
				<div class="th"><?php echo $this->form->getField("cout_text")->label; ?></div>
				<div class="td"><?php echo $this->form->getField("cout")->input." ".$this->form->getField("cout")->label; ?><br />
								<?php echo html_entity_decode($this->form->getField("cout_text")->input); ?></div>
			</div>
		</fieldset>
	</div>
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_APL_MAP_PREVIEW'), 'meta-options'); ?>
			<div id='map_canvas'></div>
		<?php echo JHtml::_('sliders.end'); ?>

		<?php echo JHtml::_('sliders.start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_APL_FIELDSET_PUBLISHING'), 'meta-options'); ?>
				<fieldset class="panelform">
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('published'); ?>
						<?php echo $this->form->getInput('published'); ?></li>
						
						<li><?php echo $this->form->getLabel('publish_up'); ?>
						<?php echo $this->form->getInput('publish_up'); ?></li>

						<li><?php echo $this->form->getLabel('publish_down'); ?>
						<?php echo $this->form->getInput('publish_down'); ?></li>
						
						<li><?php echo $this->form->getField("vpn")->label; ?>
							<?php echo $this->form->getField("vpn")->input; ?></li>
						
						<li><?php echo $this->form->getField("test")->label; ?>
							<?php echo $this->form->getField("test")->input; ?></li>
						
						<li><?php echo $this->form->getField("complet")->label; ?>
							<?php echo $this->form->getField("complet")->input; ?></li>
					</ul>
				</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>

		<?php echo JHtml::_('sliders.start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_APL_FIELDSET_ATTACHED_FILES'), 'meta-options'); ?>
				<fieldset class="panelform">
					<ul class="adminformlist">
						<li><?php echo $this->form->getField("fiche_info")->label; ?>
						<?php echo $this->form->getField("fiche_info")->input; ?>
						<?php if (is_dir(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce')) : ?>
							<a title="File Browser" href="<?php echo WFBrowserHelper::getBrowserLink('jform_fiche_info');?>" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">Open File Browser</a></li>
						<?php endif; ?>
						<li><?php echo $this->form->getField("fiche_inscription")->label; ?>
						<?php echo $this->form->getField("fiche_inscription")->input; ?>
						<?php if (is_dir(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce')) : ?>
							<a title="File Browser" href="<?php echo WFBrowserHelper::getBrowserLink('jform_fiche_inscription');?>" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">Open File Browser</a></li>
						<?php endif; ?>
						<li><?php echo $this->form->getField("fiche_custom")->label; ?>
						<?php echo $this->form->getField("fiche_custom")->input; ?>
						<?php if (is_dir(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce')) : ?>
							<a title="File Browser" href="<?php echo WFBrowserHelper::getBrowserLink('jform_fiche_custom');?>" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">Open File Browser</a></li>
						<?php endif; ?>
						
					</ul>
				</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="chantier.edit" />
		<?php JFactory::getApplication()->setUserState('com_apl.edit.chantier.id', (int) $this->item->id); ?>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<!--<pre>
    <?php //echo var_dump($this); ?>
</pre>-->