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

// @brief OpenStreetMap, layer Leaflet
// @url http://wiki.openstreetmap.org/wiki/Using_OpenStreetMap
// @url http://wiki.openstreetmap.org/wiki/Deploying_your_own_Slippy_Map
// @url http://wiki.openstreetmap.org/wiki/OpenLayers
// @url http://leafletjs.com/examples/quick-start.html

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

$params = JComponentHelper::getParams('com_apl');
$API_KEY=$params->get("cloudmade_apikey");
?>

<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>

<script type="text/javascript">
/* la carte */
var map = null;
var geocoder = null;
var markers = [];
//var glat; var glng;
var marker = null;
/* les icons */
/*var gicons = [];
icons["Rhone-Alpes"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_blue.png");
icons["France"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_green.png");
icons["Europe"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_orange.png");
icons["Monde"] = new MarkerImage("http://labs.google.com/ridefinder/images/mm_20_red.png");
*/

/**
 * Fonction d'initialisation 
 * A appeler sur chaque page contenant une map
 * 
 * cette fonction est appelée par le code suivant :
 *
 *	<script type="text/javascript">
 *	function loadScript() {
 *		initialize();
 *	}
 *	
 *	window.onload = loadScript;
 *	/script>
 *	
 *	qui doit se trouver au début du fichier .php
 *	
 */
function initialize() {
	<?php // init defaults
	$currentView = JRequest::getVar('view', '', 'get','string');
	$currentLayout = JRequest::getVar('layout', '', 'get','string');
	switch (strtolower($currentView)) {
		case "chantiers" :			// [site] Consultation de la liste des chantiers
									$defaultCategorie = APLdb::getDefaultChantiersCategorie(JRequest::getVar('id', 10, 'get','int'));
									$zoomControl = "false";
									$DEFAULT_GLAT = $defaultCategorie->mapGlat;
									$DEFAULT_GLNG = $defaultCategorie->mapGlng;
									$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
									break;
		case "chantierscategorie" : // [admin] Edition de la catégorie de chantier
									$zoomControl = "true";
									$defaultCategorie = APLdb::getDefaultChantiersCategorie(JRequest::getVar('id', 10, 'get','int'));
									$DEFAULT_GLAT = $defaultCategorie->mapGlat;
									$DEFAULT_GLNG = $defaultCategorie->mapGlng;
									$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
									break;
		case "mappemonde"	:		// [site] Consultation de la mappemonde
									$defaultCategorie = APLdb::getDefaultChantiersCategorie(999);
									$zoomControl = "false";
									$DEFAULT_GLAT = $defaultCategorie->mapGlat;
									$DEFAULT_GLNG = $defaultCategorie->mapGlng;
									$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
									break;
		case "chantier"		:		switch (strtolower($currentLayout)) {
										case "edit" :	// [admin] Édition d'un chantier
														$defaultCategorie = APLdb::getDefaultChantiersCategorie(10);
														$zoomControl = "true";
														$DEFAULT_GLAT = $defaultCategorie->mapGlat;
														$DEFAULT_GLNG = $defaultCategorie->mapGlng;
														$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
														break;
										default		:	// [site] Consultation d'un chantier
														$defaultCategorie = APLdb::getDefaultChantiersCategorie(999);
														$zoomControl = "false";
														$DEFAULT_GLAT = $defaultCategorie->mapGlat;
														$DEFAULT_GLNG = $defaultCategorie->mapGlng;
														$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
														break;
									}
									break;
		default				:		// par défaut
									$defaultCategorie = APLdb::getDefaultChantiersCategorie(999);
									$zoomControl = "true";
									$DEFAULT_GLAT = $defaultCategorie->mapGlat;
									$DEFAULT_GLNG = $defaultCategorie->mapGlng;
									$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
									break;
	}
	/*if (property_exists($this, 'form')) {
		$DEFAULT_GLAT = (isset($this->form->getField("glat")->value) ? $this->form->getField("glat")->value : $defaultCategorie->mapGlat);
		$DEFAULT_GLNG = (isset($this->form->getField("glng")->value) ? $this->form->getField("glng")->value : $defaultCategorie->mapGlng);
		$DEFAULT_ZOOM = (isset($this->form->getField("zoomLevel")->value) ? $this->form->getField("zoomLevel")->value : $defaultCategorie->zoomLevel);
	} else {
		$DEFAULT_GLAT = $defaultCategorie->mapGlat;
		$DEFAULT_GLNG = $defaultCategorie->mapGlng;
		$DEFAULT_ZOOM = $defaultCategorie->zoomLevel;
	}*/
	?>
	var glat = <?php echo $DEFAULT_GLAT ?>;
	var glng = <?php echo $DEFAULT_GLNG; ?>;
	var zoom = <?php echo $DEFAULT_ZOOM; ?>;
	var zoomControl = <?php echo $zoomControl; ?>;
	// if current page contains glat/glng input, use their values to center the map
	if (elementExist('jform_glat')) { glat = document.getElementById('jform_glat').value; }
	if (elementExist('jform_glng')) { glng = document.getElementById('jform_glng').value; }
	var mapOptions = {
		zoom: zoom,
		center: [glat, glng],
		zoomControl: zoomControl,
		worldCopyJump: true
	};

	/*gicons["10"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_blue.png");
	gicons["20"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_green.png");
	gicons["100"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_orange.png");
	gicons["999"] = new google.maps.MarkerImage("http://labs.google.com/ridefinder/images/mm_20_red.png");
	*/

	/*
	 * Créer la carte
	 */
	map = L.map('map_canvas', mapOptions);
	L.tileLayer('http://{s}.tile.cloudmade.com/<?php echo $API_KEY ?>/997/256/{z}/{x}/{y}.png', {
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
		maxZoom: 18
	}).addTo(map);
}

/**
 * @briefs	Center current map to lag,lng coordinates
 * @param	(float)		glat	latitude
 * @param	(float)		glng	longitude
 */
function centerMap(glat, glng) {
	map.setView(glat, glng);
}

/**
 * Créé un marker et l'ajoute aux tableaux de markers pour les gérer
 *
 * @param	glat, glng	coordonnées du marker
 * @param	categorie	catégorie du chantier (zone géographique)
 * @param	titre		titre affichée dans l'infobulle du marker et en <h3> de la fenétre d'infos
 * @param	desc		description affichée dans la fenétre d'infos
 * @param	href		lien qui sera actif sous le texte "En savoir plus"
 * @param	draggable	vrai si on veut que le marker soit déplaçable
 * @DEPRECATED
 * @param	addOverlay	vrai si on veut afficher le marker tout de suite, faux si on utilise un markerManager
 */
function createMarkerFromLatLng(glat, glng, categorie, titre, desc, href, draggable, addOverlay) {
	// if marker already exists in array of markers, return it instead of creating it
	if (markerExists(L.latLng(glat, glng))) {
		return getMarker(L.latLng(glat, glng));
	}
	// not found ? ok, so we create a new marker
	var marker = L.marker([glat, glng], {
		draggable: draggable,
		title: titre
	}).addTo(map);
	// create infobox
	marker.bindPopup("<div id='infoWindow' class='osm_info'><a href='" + href + "'><h3>" + titre + "</h3></a><p>" + desc + "</p><a href='" + href + "'>En savoir plus ...</a><br /><br /></div>");
	// if marker is draggable, then we should attach en event to update controls on DOM to display latlng
	// and also, if it is draggable, then it is likely the only marker on the map, so, center map to its latlng
	if (draggable) {
		marker.on('dragend', function(event) {
			//console.log(event);
			updateMarkerControls(event.target._latlng);
		});
	}
	markers.push(marker);
	//console.log(markers);
	return marker;
}

/**
 * 
 * @brief	update marker when specific controls are updated by user
 * @url		http://wiki.openstreetmap.org/wiki/Nominatim#Reverse_Geocoding_.2F_Address_lookup
 */
function updateCurrentMarker() {
	glat = document.getElementById('jform_glat').value;
	glng = document.getElementById('jform_glng').value;
	address = document.getElementById('jform_lieu').value;
	titre = document.getElementById('jform_nom').value;
	desc = document.getElementById('jform_actions').value.replace(/\n/g,"<br \/>");
	
	if (glat == "") { glat = 0; }
	if (glng == "") { glng = 0; }
	/*var marker = getMarker(L.latLng(glat, glng));
	if (marker) {
		//updateMarker(marker, address, '', titre, desc, null, true, true);
		marker.setPopupContent("<div id='infoWindow' class='osm_info'><a href='#'><h3>" + titre + "</h3></a><p>" + desc + "</p><a href='#'>En savoir plus ...</a><br /><br /></div>");
		marker.title = titre;
	} else {*/
		if (address != "") {
			// translation en glatlng pour afficher le marker
			//if (!geocoder) { return; }
			/*geocoder.geocode({'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					console.log(funcName+"(): found address at latlng = "+results[0].geometry.location.lat() +", " +results[0].geometry.location.lng());
					createMarkerFromLatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng(), '', titre, desc, null, true, true);
				} else {
					console.log(funcName+"(): Geocoder failed due to: " + status);
				}
			});*/
			var url = "http://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q="+address.replace(/ /,"+");
			//console.log("url = %s", url);
			var request = new Request({
				url: url,
				method:'get',
				onSuccess: function(r){
					//console.log("%o", r);
					var obj = JSON.parse(r);
					//console.log("%O", obj);
					var marker = getMarker(L.latLng(glat, glng));
					if (marker) {
						// update marker properties
						marker.setLatLng(L.latLng(obj[0].lat, obj[0].lon));
						marker.setPopupContent("<div id='infoWindow' class='osm_info'><a href='#'><h3>" + titre + "</h3></a><p>" + desc + "</p><a href='#'>En savoir plus ...</a><br /><br /></div>");
						marker.title = titre;
						// update controls on page if they exist (on the site creation page)
						updateMarkerControls(L.latLng(obj[0].lat, obj[0].lon));
						// center map to the marker on case it has move
						map.setView(L.latLng(obj[0].lat, obj[0].lon));	
					} else {
						// create marker
						createMarkerFromLatLng(obj[0].lat, obj[0].lon, '', titre, desc, '#', true, false);
						// center map to it
						map.setView(L.latLng(obj[0].lat, obj[0].lon));	
					}
				},
				onFailure: function(r) {
					console.error("updateCurrentMarker(): reverse geocoding has failed");
					console.error(r);
				}
			}).send();
		}
	//}
}

/**
 * @brief	Méthode pour vérifier si un marker existe sur la carte
 * 
 * @param	coord	coordonnées au format (glat,glng)
 * 
 * @return	true si le marker existe, false autrement
 */
function markerExists(coord) {
	//if (debug) { debug.innerHTML += dump(markers,0,1); }
	//if (markers.length == 0) { return false; }
	for (var i = 0; i < markers.length; i++) {
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getPosition().lat() !== marker.getPosition().lat())
		if (markers[i].getLatLng().equals(coord)) { return true; }
	}
	return false;
}

/**
 * Méthode pour obtenir un marker dans la liste des markers créés
 * 
 * @param	coord	coordonnées au format google.maps.LatLng
 * 
 * @return	marker	si un marker est trouvé, sinon false
 */
function getMarker(coord) {
	var funcName = arguments.callee.toString();
	funcName = funcName.substr('function '.length);				// trim off "function "
	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
	//console.log(funcName+"()");
	//console.log(funcName+"(): markers.length = " + markers.length);
	for (var i = 0; i < markers.length; i++) {
		//console.log(funcName+"(): marker["+i+"] : "+markers[i].getPosition()+" =? "+gLatLng);
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getPosition().lat() !== marker.getPosition().lat())
		if (markers[i].getLatLng().equals(coord)) {
			//console.log(funcName+"(): YES => return marker["+i+"]");
			return markers[i];
		}
	}
	return false;
}

/** 
 * @brief met à jour les contrôles du DOM avec les valeurs du marker
 * @url http://wiki.openstreetmap.org/wiki/Nominatim#Reverse_Geocoding_.2F_Address_lookup
 *
 * @param glatLng	(object)	L.latLng	@url http://leafletjs.com/reference.html#latlng
 *
 * @return void
 * 
 */
function updateMarkerControls(glatLng) {
	var funcName = arguments.callee.toString();
	funcName = funcName.substr('function '.length);				// trim off "function "
	funcName = funcName.substr(0, funcName.indexOf('('));		// trim off everything after the function name
	//console.log(funcName+"()");

	if (elementExist('jform_glat')) { document.getElementById('jform_glat').value = new Number(glatLng.lat).toPrecision(13); }
	if (elementExist('jform_glng')) { document.getElementById('jform_glng').value = new Number(glatLng.lng).toPrecision(13); }
	if (elementExist('jspan_glat')) { document.getElementById('jspan_glat').innerHTML = new Number(glatLng.lat).toPrecision(13); }
	if (elementExist('jspan_glng')) { document.getElementById('jspan_glng').innerHTML = new Number(glatLng.lng).toPrecision(13); }
	if (elementExist('jform_pays')) {
		// to know which 'zoom' value to use (4=country, 12=county) : 
		// @url http://wiki.openstreetmap.org/wiki/Nominatim/Development_overview
		var url = "http://nominatim.openstreetmap.org/reverse?format=json&lat="+new Number(glatLng.lat).toPrecision(13)+"&lon="+new Number(glatLng.lng).toPrecision(13)+"&zoom=12&addressdetails=1"
		//console.log("url = %s", url);
		var request = new Request({
			url: url,
			method:'get',
			onSuccess: function(r){
				//console.log("%o", r);
				var obj = JSON.parse(r);
				if (elementExist('jform_pays'))				{ document.getElementById('jform_pays').value = obj.address.country; }
				if (elementExist('jspan_pays'))				{ document.getElementById('jspan_pays').innerHTML = obj.address.country; }
				if (elementExist('jform_countrycode'))		{ document.getElementById('jform_countrycode').value = obj.address.country_code; }
				if (elementExist('jspan_countrycode'))		{ document.getElementById('jspan_countrycode').innerHTML = obj.address.country_code; }
				if (elementExist('jform_formattedAddress'))	{ document.getElementById('jform_formattedAddress').innerHTML = obj.display_name; }
			},
			onFailure: function(r) {
				console.error("updateMarkerControls(): reverse geocoding has failed");
				console.error(r);
			}
		}).send();
	}
}


</script>