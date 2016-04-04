<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

/*
 * On ne peut pas exécuter du code PHP dans un fichier JS,
 * à moins de modifier le php.ini
 * la parade est de définir les scripts javascripts dans un fichier php
 */

$params = JComponentHelper::getParams('com_apl');
$API_KEY=$params->get("googlemap_apikey");

/**
 * @TODO
 * . supprimer le addOverlay qui ne sert plus à rien
 *  
 */
?>

<script src="http://maps.googleapis.com/maps/api/js?key=<?php echo $API_KEY; ?>&callback=initialize"></script>

<script type="text/javascript">
// google map v3
// voir https://developers.google.com/maps/documentation/javascript/

/* la carte */
var map = null;
var geocoder = null;
var markers = [];
var currentMarker = null;
//var glat; var glng;
var marker = null;
/* les icons */
/*var gicons = [];
gicons["Rhone-Alpes"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_blue.png");
gicons["France"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_green.png");
gicons["Europe"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_orange.png");
gicons["Monde"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_red.png");
*/
var infowindow = null;

/**
 * @brief	initialize()	Fonction d'initialisation 
 */
function initialize() {
	<?php // init defaults
	$currentView = JRequest::getVar('view', '', 'get','string');
	$currentLayout = JRequest::getVar('layout', '', 'get','string');
	switch (strtolower($currentView)) {
		case "chantiers" :			// Consultation de la liste des chantiers sur le site
			$mapTypeControl = "false";
			$mapTypeControlOptions = "{}";
			$defaultCategorie = APLdb::getDefaultChantiersCategorie(JRequest::getVar('id', 10, 'get','int'));
			$zoomControl = "false";
			$zoomControlOptions = "{}";
			break;
		case "chantierscategorie" : // [admin] Edition de la catégorie de chantier
			$mapTypeControl = "true";
			$mapTypeControlOptions = "{ mapTypeIds: [	google.maps.MapTypeId.HYBRID, 
														google.maps.MapTypeId.ROADMAP, 
														google.maps.MapTypeId.SATELLITE, 
														google.maps.MapTypeId.TERRAIN ], 
										position: google.maps.ControlPosition.TOP_RIGHT,
										style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
									}";
			$zoomControl = "true";
			$zoomControlOptions = "{ position: google.maps.ControlPosition.LEFT_CENTER,
										style: google.maps.ZoomControlStyle.LARGE
									}";
			$defaultCategorie = APLdb::getDefaultChantiersCategorie(JRequest::getVar('id', 10, 'get','int'));
			break;
		case "mappemonde" :			// Consultation de la mappemonde
			$mapTypeControl = "false";
			$mapTypeControlOptions = "{}";
			$defaultCategorie = APLdb::getDefaultChantiersCategorie(999);
			$zoomControl = "false";
			$zoomControlOptions = "{}";
			break;
		default :					// par défaut : [admin] édition d'un chantier
			$mapTypeControl = "false";
			$mapTypeControlOptions = "{}";
			$defaultCategorie = APLdb::getDefaultChantiersCategorie(10);
			$zoomControl = "true";
			$zoomControlOptions = "{}";
			break;
	}
	if (property_exists($this, 'form')) {
		$GOOGLE_DEFAULT_GLAT = (isset($this->form->getField("glat")->value) ? $this->form->getField("glat")->value : $defaultCategorie->mapGlat);
		$GOOGLE_DEFAULT_GLNG = (isset($this->form->getField("glng")->value) ? $this->form->getField("glng")->value : $defaultCategorie->mapGlng);
		$GOOGLEMAP_DEFAULT_ZOOM = (isset($this->form->getField("zoomLevel")->value) ? $this->form->getField("zoomLevel")->value : $defaultCategorie->zoomLevel);
		$GOOGLEMAP_DEFAULT_TYPE = (isset($this->form->getField("mapType")->value) ? $this->form->getField("mapType")->value : $defaultCategorie->mapType);
	} else {
		$GOOGLE_DEFAULT_GLAT = $defaultCategorie->mapGlat;
		$GOOGLE_DEFAULT_GLNG = $defaultCategorie->mapGlng;
		$GOOGLEMAP_DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
		$GOOGLEMAP_DEFAULT_TYPE = $defaultCategorie->mapType;
	}
	?>
	
	geocoder = new google.maps.Geocoder();

	/*gicons["10"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_blue.png");
	gicons["20"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_green.png");
	gicons["100"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_orange.png");
	gicons["999"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_red.png");
	*/

	var myOptions = {
		zoom: <?php echo $GOOGLEMAP_DEFAULT_ZOOM; ?>,
		disableDefaultUI: true,
		mapTypeControl: <?php echo $mapTypeControl; ?>,
		mapTypeControlOptions: <?php echo $mapTypeControlOptions; ?>,
		zoomControl: <?php echo $zoomControl; ?>,
		zoomControlOptions: <?php echo $zoomControlOptions; ?>,
		center: new google.maps.LatLng(<?php echo $GOOGLE_DEFAULT_GLAT ?>, <?php echo $GOOGLE_DEFAULT_GLNG; ?>),
		mapTypeId: google.maps.MapTypeId.<?php echo $GOOGLEMAP_DEFAULT_TYPE; ?>
	}

	/*
	 * Créer la carte
	 */
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	/*
	 * Attacher les évènements à la carte
	 */
	google.maps.event.addListener(map, 'center_changed', function() {
		updateMapControls(map.getCenter(), map.getZoom(), map.getMapTypeId());
	});
	google.maps.event.addListener(map, 'zoom_changed', function() {
		updateMapControls(map.getCenter(), map.getZoom(), map.getMapTypeId());
	});
	google.maps.event.addListener(map, 'maptypeid_changed', function() {
		updateMapControls(map.getCenter(), map.getZoom(), map.getMapTypeId());
	});
	/*
	 * Créer la fenêtre d'info
	 */
	infowindow = new google.maps.InfoWindow({maxWidth: 250});
}

/**
 * @briefs	Center current map to lag,lng coordinates
 * @param	(float)		glat	latitude
 * @param	(float)		glng	longitude
 */
function centerMap(glat, glng) {
	map.setCenter(new google.maps.LatLng(glat, glng));
}

/**
 * Créé un marker et l'ajoute aux tableaux de markers pour les gérer
 *
 * @param	glat, glng	coordonnées du marker
 * @param	titre		titre affichée dans l'infobulle du marker et en <h3> de la fenétre d'infos
 * @param	desc		description affichée dans la fenétre d'infos
 * @param	href		lien qui sera actif sous le texte "En savoir plus"
 * @param	draggable	vrai si on veut que le marker soit déplaçable
 * @param	addOverlay	vrai si on veut afficher le marker tout de suite, faux si on utilise un markerManager
 */
function createMarkerFromLatLng(glat, glng, categorie, titre, desc, href, draggable, addOverlay) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	console.log(funcName+"()");

	if (markerExists(new google.maps.LatLng(glat, glng))) {
		return getMarker(new google.maps.LatLng(glat, glng));
	}
	// options du marker
	marker = new google.maps.Marker({
		position: new google.maps.LatLng(glat, glng),
		draggable: draggable,
		map: map,
		title: titre
		//animation: BOUNCE,
		//icon: gicons[categorie]
	});
	// infowindow
	marker.content = "<div id='infoWindow' class='googlemap_info'><a href='" + href + "'><h3>" + titre + "</h3></a><p>" + desc + "</p><a href='" + href + "'>En savoir plus ...</a><br /><br /></div>";
	google.maps.event.addListener(marker, 'click', function() {
		//infowindow is an instance of google.maps.InfoWindow
		infowindow.close();
		infowindow.setContent(this.content);
		infowindow.open(map, this);
	});
	
	//My infoWindow's id = infoWindow and my map object is named map 
    //Fix the scrollbar issue 
    google.maps.event.addListener(infowindow, 'domready', function() {
		document.getElementById('infoWindow').style.overflow = 'hidden';
		document.getElementById('infoWindow').parentNode.style.overflow = 'hidden';
		document.getElementById('infoWindow').parentNode.parentNode.style.overflow = 'hidden';
		//this.open(map);
    });
	
	if (draggable) {
		google.maps.event.addDomListener(marker, 'dragend', function(event) {
			//console.log(event);
			updateMarkerControls(event.latLng);
		});
		map.setCenter(marker.getPosition());
	}
	
	//google.maps.event.trigger(marker, "dragend", newEvent);
	updateMarkerControls(marker.getPosition());
	
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> ajout du marker au tableau<br />"; }
	markers.push(marker);
	//console.log(markers);
	/*if (addOverlay) {
		//map.addOverlay(marker);
		map.setCenter(marker.getPosition());
	}*/
	
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> return du marker<br />"; }
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span><br />"; }
	return marker;
}

/**
 * Méthode pour vérifier si un marker existe sur la carte
 * 
 * @param	coord	coordonnées au format (glat,glng)
 * @return	true si le marker existe, false autrement
 */
function markerExists(coord) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	console.log(funcName+"()");
	//if (debug) { debug.innerHTML += dump(markers,0,1); }
	//if (markers.length == 0) { return false; }
	for (var i = 0; i < markers.length; i++) {
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getPosition().lat() !== marker.getPosition().lat())
		if (markers[i].getPosition().equals(coord)) { return true; }
	}
	return false;
}

/**
 * Méthode pour obtenir un marker dans la liste des markers créés
 * 
 * @param	(google.maps.LatLng)	lat lng coordinates
 * @return	(object)				marker if found | false otherwise
 */
function getMarker(gLatLng) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	console.log(funcName+"()");
//	//console.log(funcName+"(): markers.length = " + markers.length);
	for (var i = 0; i < markers.length; i++) {
//		console.log(funcName+"(): marker["+i+"] : "+markers[i].getPosition()+" =? "+gLatLng);
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getPosition().lat() !== marker.getPosition().lat())
		if (markers[i].getPosition().equals(gLatLng)) {
//			console.log(funcName+"(): YES => return marker["+i+"]");
			return markers[i];
		}
	}
	return false;
}

/**
 * Méthode pour mettre à jour un marker (point) sur une carte
 * 
 * @param	marker		point courant, null si aucun
 * @param	glat		coordonnées (latitude)
 * @param	glng		coordonnées (longitude)
 * @param	categorie	categorie du chantier relié à ce marker
 * @param	titre		titre du marker
 * @param	desc		description à afficher dans l'info-bulle du marker
 * @param	href		lien du texte "en savoir plus"
 * @param	draggable	vrai si on peut déplacer le marker
 * @param	addOverlay	
 * 
 */
function updateMarkerFromLatLng(marker, glat, glng, categorie, titre, desc, href, draggable, addOverlay) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	//console.log(funcName+"()");

	if (glat && glng) {
		marker.setPosition(new google.maps.LatLng(glat, glng));
	}
//	//console.log(funcName+"(): marker: "+marker);
//	console.log(funcName+"(): updating infowindow with content : "+titre+" - "+desc+" - "+href);
	marker.content = "<div class='googlemap_info'><h3>" + titre + "</h3><p>" + desc + "</p><a href='" + href + "'>En savoir plus ...</a><br /><br /></div>";
	//infowindow.setContent(marker.content);

	//google.maps.event.trigger(marker, "dragend");
	updateMarkerControls(marker.getPosition());
	google.maps.event.trigger(marker, "click");

	//infowindow.setContent("<div class='googlemap_info'><h3>" + titre + "</h3><p>" + desc + "</p><a href='" + href + "'>En savoir plus ...</a><br /><br /></div>");
	//if (categorie)	{	/* faire des trucs */ }
	//if (titre)		{	}
	
	// centrer la map
	map.setCenter(new google.maps.LatLng(glat, glng));
	
	return marker;
}

/**
 * Méthode pour mettre à jour un marker (point) sur une carte
 * 
 * @param	marker		point courant, null si aucun
 * @param	address		adresse complète
 * @param	categorie	categorie du chantier relié à ce marker
 * @param	titre		titre du marker
 * @param	desc		description à afficher dans l'info-bulle du marker
 * @param	href		lien du texte "en savoir plus"
 * @param	draggable	vrai si on peut déplacer le marker
 * @param	addOverlay	
 * 
 */
function updateMarker(marker, address, categorie, titre, desc, href, draggable, addOverlay) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	//console.log(funcName+"()");

	if (address != "") {
		// translation en glatlng pour afficher le marker
		//if (!geocoder) { return; }
		geocoder.geocode({'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
//				console.log(funcName+"(): found new address at latlng = "+results[0].geometry.location.lat() +", " +results[0].geometry.location.lng());
				updateMarkerFromLatLng(marker, results[0].geometry.location.lat(), results[0].geometry.location.lng(), categorie, titre, desc, href, draggable, addOverlay);
			} else {
//				console.log(funcName+"(): Geocoder failed due to: " + status);
			}
		});
	}
}

/** @brief met à jour les contrôles du DOM avec les valeurs du marker
 *
 * @param glatLng	objet google.maps.LatLng
 *
 * @return void
 */
function updateMarkerControls(glatLng) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	//console.log(funcName+"()");

	if (elementExist('jform_glat')) { document.getElementById('jform_glat').value = new Number(glatLng.lat()).toPrecision(13); }
	if (elementExist('jform_glng')) { document.getElementById('jform_glng').value = new Number(glatLng.lng()).toPrecision(13); }
	if (elementExist('jspan_glat')) { document.getElementById('jspan_glat').innerHTML = new Number(glatLng.lat()).toPrecision(13); }
	if (elementExist('jspan_glng')) { document.getElementById('jspan_glng').innerHTML = new Number(glatLng.lng()).toPrecision(13); }
	if (elementExist('jform_pays')) {
		geocoder.geocode({'latLng': glatLng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[1]) {
					//console.log(results[1]);
					document.getElementById('jform_formattedAddress').innerHTML = results[1].formatted_address;
					var found=false; var i=0;
					var countryName = "";
					while (!found) {
//						//console.log(funcName+"(): results[1].address_components["+i+"].types[0] = "+results[1].address_components[i].types[0]);
						//console.log(results[1].address_components[i].types[0]);
						if (results[1].address_components[i].types[0] == "country") {
							countryName = results[1].address_components[i].long_name;
							countryCode = results[1].address_components[i].short_name;
							found = true;
						}
						i++;
						if (i > results[1].address_components.length - 1) { break; }
					}
					document.getElementById('jform_pays').value = countryName;
					document.getElementById('jspan_pays').innerHTML = countryName;
					document.getElementById('jform_countrycode').value = countryCode;
					document.getElementById('jspan_countrycode').innerHTML = countryCode;
					//map.setZoom(11);
					/*marker = new google.maps.Marker({
						position: latlng,
						map: map
					});*/
					//infowindow.setContent(results[1].formatted_address);
					//infowindow.open(map, marker);
				}
			} else {
				//alert("Geocoder failed due to: " + status);
				console.log("Geocoder failed due to: " + status);
			}
		});
	}
}

/** @brief met à jour les contrôles du DOM avec les valeurs de la carte
 *
 * @param glatLng	objet google.maps.LatLng
 *
 * @return void
 */
function updateMapControls(glatLng, zoomLevel, mapType) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	//console.log(funcName+"()");
//	//console.log(funcName+"(): zoomLevel = " + zoomLevel + " / new_mapType = " + new String(mapType).toUpperCase());

	if (elementExist('jform_mapGlat')) { document.getElementById('jform_mapGlat').value = new Number(glatLng.lat()).toPrecision(13); }
	if (elementExist('jform_mapGlng')) { document.getElementById('jform_mapGlng').value = new Number(glatLng.lng()).toPrecision(13); }
	if (elementExist('jspan_mapGlat')) { document.getElementById('jspan_mapGlat').innerHTML = new Number(glatLng.lat()).toPrecision(13); }
	if (elementExist('jspan_mapGlng')) { document.getElementById('jspan_mapGlng').innerHTML = new Number(glatLng.lng()).toPrecision(13); }
	if (elementExist('jform_zoomLevel')) { document.getElementById('jform_zoomLevel').value = zoomLevel; }
	if (elementExist('jform_mapType')) { document.getElementById('jform_mapType').value = new String(mapType).toUpperCase(); }
}

function updateCurrentMarker() {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	console.log(funcName+"()");
	/*
	address = document.getElementById('jform_lieu').value;
	categorie = document.getElementById('jform_catid').value;
	new_titre = document.getElementById('jform_nom').value;
	new_desc = document.getElementById('jform_actions').value;
	*/
	//alert("ça marche");
	//updateMarker(marker, address, categorie, titre, desc, null, true, true);
	current_glat = document.getElementById('jform_glat').value.valueOf();
	current_glng = document.getElementById('jform_glng').value.valueOf();
	new_address = document.getElementById('jform_adresse').value;
	new_titre = document.getElementById('jform_nom').value;
	new_desc = document.getElementById('jform_actions').value.replace(/\n/g,"<br \/>");
//	console.log("%s(): current_glat = %f / current_glng = %f", funcName, current_glat, current_glng);
	// goecode adress into valid glat/glng
	//new_glatLng = geocodeAddress(new_address);
//	//console.log(funcName+"():" + new_glatLng);
	var coords = new google.maps.LatLng(current_glat, current_glng);
//	console.log("%s(): %o", funcName, coords);
	var currentMarker = getMarker(coords);
	if (currentMarker == false) {
		if (new_address != "") {
			// translation en glatlng pour afficher le marker
			//if (!geocoder) { return; }
			geocoder.geocode({'address': new_address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
//					console.log(funcName+"(): found address at latlng = "+results[0].geometry.location.lat() +", " +results[0].geometry.location.lng());
					createMarkerFromLatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng(), '', new_titre, new_desc, null, true, true);
				} else {
//					console.log(funcName+"(): Geocoder failed due to: " + status);
				}
			});
		}		
	} else {
		updateMarker(currentMarker, new_address, '', new_titre, new_desc, null, true, true);
	}
}

/** @brief update map with values in DOM controls
 * 
 */
function updateCurrentMap() {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);				// trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
//	//console.log(funcName+"()");
	
	new_zoomLevel = parseFloat(document.getElementById('jform_zoomLevel').value);
	//new_zoomLevel = new Number(document.getElementById('jform_zoomLevel').value);
	//new_mapType = document.getElementById('jform_mapType').value;
	new_mapType = new String(document.getElementById('jform_mapType').value).toLowerCase();
	//new_mapType = "google.maps.MapTypeId."+document.getElementById('jform_mapType').value;
//	//console.log(funcName+"(): zoomLevel = " + new_zoomLevel + " / new_mapType = " + new_mapType);
	
	var mapOptions = {
		zoom: new_zoomLevel,
		mapTypeId: new_mapType
	}
	//map.setZoom(new_zoomLevel);
	//map.setMapTypeId(new_mapType);
	map.setOptions(mapOptions);
}

/**
 * @brief	localiser un marker sur une carte
 * @param	float	glat	latitude
 * @param	float	glng	longitude
 * 
 * @warn	anchor with name/id map has to exist : <a name='map' id='map'></a>
 */
function localiser(glat, glng) {
	var glatlng = new google.maps.LatLng(glat, glng);
	// on centre la carte sur les coordonnées du marker
	map.setCenter(glatlng);
	// on cherche le marker
	var marker = getMarker(glatlng);
	// fire click event
	google.maps.event.trigger(marker, 'click');
	// gently scroll to map
	var myFx = new Fx.Scroll(window).toElement('map', 'y');
}
</script>
