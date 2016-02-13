<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

// $this = aplViewChantier
// $this->_models["chantier"] = aplModelChantier
// $this->form = admin/models/forms/chantier.xml

$GOOGLEMAP_API_KEY="AIzaSyC_Y4i2K7sWjx87SDbSMZucFVk52Q1Fok8";
$GOOGLEMAP_DEFAULT_ZOOM=7;
$GOOGLEMAP_DEFAULT_TYPE="TERRAIN";

?>

<script type="text/javascript">
// google map v3
// voir https://developers.google.com/maps/documentation/javascript/

var geocoder;

function initialize() {
	geocoder = new google.maps.Geocoder();
	var myOptions = {
		zoom: <?php echo $GOOGLEMAP_DEFAULT_ZOOM; ?>,
		disableDefaultUI: true,
		center: new google.maps.LatLng(<?php echo $this->form->getField("Glat")->value; ?>, <?php echo $this->form->getField("Glng")->value; ?>),
		mapTypeId: google.maps.MapTypeId.<?php echo $GOOGLEMAP_DEFAULT_TYPE; ?>
	}
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $this->form->getField("Glat")->value; ?>, <?php echo $this->form->getField("Glng")->value; ?>),
		draggable: true,
		map: map,
		title:"<?php echo $this->form->getField("nom")->value; ?>"
	});
	
	// on déclare une fonction pour mettre à jour les champs glat/glng lorsque l'on déplace le marker
	google.maps.event.addDomListener(marker, 'dragend',
		function(event) {
			document.getElementById('jform_Glat').innerHTML = new Number(event.latLng.lat()).toPrecision(10);
			document.getElementById('jform_Glng').innerHTML = new Number(event.latLng.lng()).toPrecision(10);
			geocoder.geocode({'latLng': event.latLng}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						//console.log(results[1]);
						document.getElementById('jform_formattedAddress').innerHTML = results[1].formatted_address;
						var found=false; var i=0;
						var countryName = "";
						while (!found) {
							//console.log(results[1].address_components[i].types[0]);
							if (results[1].address_components[i].types[0] == "country") {
								countryName = results[1].address_components[i].long_name;
								found = true;
							}
							i++;
							if (i > results[1].address_components.length - 1) { break; }
						}
						document.getElementById('jform_pays').innerHTML = countryName;
						//map.setZoom(11);
						/*marker = new google.maps.Marker({
							position: latlng,
							map: map
						});*/
						//infowindow.setContent(results[1].formatted_address);
						//infowindow.open(map, marker);
					}
				} else {
					alert("Geocoder failed due to: " + status);
				}
			});
		}
	);
}

function loadScript() {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLEMAP_API_KEY; ?>&callback=initialize";
	document.body.appendChild(script);
}

window.onload = loadScript;
</script>

<form action="<?php echo JRoute::_('index.php?option=com_apl&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="chantier-form">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_APL_CHANTIER_DETAILS' ); ?></legend>
		<div id='map_canvas'></div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("nom")->label; ?></div><?php //onblur="alert(document.getElementById('categorie').options[document.getElementById('categorie').value].text);updateMarkerFromLatLng(marker,null,null,document.getElementById('categorie').options[document.getElementById('categorie').value].text,this.value,document.getElementById('actions').value,null,true,true);"?>
			<div class="td"><?php echo $this->form->getField("nom")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("lieu")->label; ?></div><?php //onblur="updateMarker(marker,this.value,document.getElementById('categorie').value,document.getElementById('nom').value,document.getElementById('actions').value,null,true,true);//googleMapLocaliseChantier(this.value);" ?>
			<div class="td"><?php echo $this->form->getField("lieu")->input; //." ".$this->form->getField("pays")->label." ".$this->form->getField("pays")->input; ?><br />
							<?php echo JText::_('COM_APL_CHANTIER_ADDRESS_BY_GOOGLE'); ?> : <span name="jform[formattedAddress]" id="jform_formattedAddress"></span><br />
							<?php echo JText::_('COM_APL_CHANTIER_CATEGORIE_LABEL')." ".$this->form->getField("categorie")->input; ?> <?php echo $this->form->getField("Glat")->label; ?><span name="jform[Glat]" id="jform_Glat"><?php echo $this->form->getField("Glat")->value; ?></span> <?php echo $this->form->getField("Glng")->label; ?> <span name="jform_Glng" id="jform_Glng"><?php echo $this->form->getField("Glng")->value;?></span> / <span name="jform[pays]" id="jform_pays"><?php echo $this->form->getField("pays")->value; ?></span><br />
							<span class="help"><?php echo JText::_('COM_APL_CHANTIER_GLATLNG_HELP'); ?></span>
			</div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("organisateurs")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("organisateurs")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("contexte")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("contexte")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("actions")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("actions")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("benevole")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("benevole")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("date_text")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("date_debut")->label." ".$this->form->getField("date_debut")->input." ".$this->form->getField("date_fin")->label." ".$this->form->getField("date_fin")->input." ".$this->form->getField("date_exacte")->input." ".$this->form->getField("date_exacte")->label; ?><br />
							<?php echo $this->form->getField("date_text")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("profile")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("profile")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("cout_text")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("cout")->label." ".$this->form->getField("cout")->input; ?><br />
							<?php echo $this->form->getField("cout_text")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("fiche_info")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("fiche_info")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("fiche_inscription")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("fiche_inscription")->input; ?></div>
		</div>
		<div class="tr">
			<div class="th"><?php echo $this->form->getField("fiche_custom")->label; ?></div>
			<div class="td"><?php echo $this->form->getField("fiche_custom")->input; ?></div>
		</div>
		<hr />
		<div class="tr">
			<div class="th"><?php echo JText::_('COM_APL_CHANTIER_OPTIONS'); ?></div>
			<div class="td"><?php echo $this->form->getField("test")->input." ".$this->form->getField("test")->label." ".$this->form->getField("complet")->input." ".$this->form->getField("complet")->label; ?></div>
		</div>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="chantier.edit" />
                <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
