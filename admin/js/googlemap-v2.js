// JavaScript Document
/* la carte */
var map = null;
var geocoder = null;
//var gLargeMapControl3d = null;
/* les markers */
var mgr = null;
var markers = [];
var currentMarker = null;
var glat; var glng;
var marker = null;
/* les icons */
var gicons = [];
gicons["Rhone-Alpes"] = new GIcon(G_DEFAULT_ICON, "http://labs.google.com/ridefinder/images/mm_20_blue.png");
gicons["France"] = new GIcon(G_DEFAULT_ICON, "http://labs.google.com/ridefinder/images/mm_20_green.png");
gicons["Europe"] = new GIcon(G_DEFAULT_ICON, "http://labs.google.com/ridefinder/images/mm_20_orange.png");
gicons["Monde"] = new GIcon(G_DEFAULT_ICON, "http://labs.google.com/ridefinder/images/mm_20_red.png");
/*gicons["Rhone-Alpes"].iconSize = new GSize(12,20);
gicons["France"].iconSize = new GSize(12,20);
gicons["Europe"].iconSize = new GSize(12,20);
gicons["Monde"].iconSize = new GSize(12,20);
gicons["Rhone-Alpes"].iconSize = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
gicons["France"].iconSize = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
gicons["Europe"].iconSize = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
gicons["Monde"].iconSize = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";*/

function initialize(glat, glng, zoomLevel, map_type) {
	//alert("parametres : "+glat+", "+glng+", "+zoomLevel+", "+map_type);
	if (GBrowserIsCompatible()) {
		//var address = 'Dieulefit, france';
		// Recherche des coordonn�es d'un point dont on connait l'adresse :
		geocoder = new google.maps.ClientGeocoder();
		map = new GMap2(document.getElementById("map_canvas"));
		var customUI = map.getDefaultUI();
		customUI.maptypes.normal	= false;
		customUI.maptypes.satellite = true;
		customUI.maptypes.hybrid  	= false;
		customUI.maptypes.physical  = false;
		customUI.zoom.scrollwheel	= true;
		customUI.zoom.doubleclick	= true;
		customUI.controls.largemapcontrol3d		= false;
		customUI.controls.smallzoomcontrol3d	= false;
		customUI.controls.maptypecontrol		= false;
		customUI.controls.menumaptypecontrol	= false;
		customUI.controls.overviewmapcontrol	= false;
		map.setUI(customUI);
		/*gLargeMapControl3d = new GLargeMapControl3d();
		map.addControl(gLargeMapControl3d);*/
		/*geocoder.getLatLng(address, function (coord) {
			// Et centrage de la map sur les coordonn�es renvoy�es par Google :
			map.setCenter(coord, 5);
		});*/
		switch (map_type) {
			case "G_SATELLITE_MAP"	:	map.setMapType(G_SATELLITE_MAP);
										break;
			case "G_PHYSICAL_MAP"	:	map.setMapType(G_PHYSICAL_MAP);
										break;
			default					:	map.setMapType(G_SATELLITE_MAP);
										break;
		}
		//map.setMapType(G_SATELLITE_MAP);
		//map.setMapType(G_PHYSICAL_MAP);
		//map.setMapType(map_type);
		//map.setCenter(new GLatLng(44.5258408, 5.0662654), 5);
		//alert("map.setCenter(new GLatLng("+glat+", "+glng+"), "+zoomLevel);
		map.setCenter(new GLatLng(glat, glng), zoomLevel);
		//addAllMarkers();
	}
	//window.setTimeout(addAllMarkers, 0);
}

/*
 * Cr�� un marker et l'ajoute aux tableaux de markers pour les g�rer
 *
 * glat, glng => coordonn�es du marker
 * titre => titre affich�e dans l'infobulle du marker et en <h3> de la fen�tre d'infos
 * desc => description affich�e dans la fen�tre d'infos
 * href => lien qui sera actif sous le texte "En savoir plus"
 * draggable => vrai si on veut que le marker soit d�pla�able
 * addOverlay => vrai si on veut afficher le marker tout de suite, faux si on utilise un markerManager
 */
function createMarkerFromLatLng(glat, glng, categorie, titre, desc, href, draggable, addOverlay) {
	//if (debug) { debug = document.getElementById("debug"); }
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);        // trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));        // trim off everything after the function name
	console.log(funcname);
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> " + dump(arguments,0,2) + "<br />"; }
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> check si le marker existe d&eacute;j&agrave;...<br />"; }
//	//if (debug) { console.log("["+funcName+"]:</span> check si le marker existe d&eacute;j&agrave;...<br />"); }
	if (markerExists(new GLatLng(glat, glng))) {
		return getMarker(new GLatLng(glat, glng));
	}
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> Cr&eacute;ation du marker : createMarkerFromLatLng(" + glat + ", " + glng + ", '" + titre + "', '" + desc + "', '" + href + "', " + draggable + ");<br />"; }
	// options du marker
	var mOptions = { 
		title: html_entity_decode(titre),
		draggable: draggable,
		bouncy: true,
		icon: gicons[categorie]
	};
	// options de la fen�tre d'infos
	var wOptions= {
		maxwidth: 50
	};
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> Cr&eacute;ation du marker<br />"; }
	marker = new GMarker(new GLatLng(glat, glng), mOptions);
	//map.addOverlay(marker);
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> ajout de l'&eacute;vennement click<br />"; }
	GEvent.addListener(marker, "click",
		function() {
			this.openInfoWindowHtml("<div class='googlemap_info'><h3>" + titre + "</h3><p>" + desc + "</p><a href='" + href + "'>En savoir plus ...</a><br /><br /></div>", wOptions);
		}
	);
	if (draggable) {
		// on initialise les controles
		document.getElementById('Glat').value = new Number(glat).toPrecision(10);
		document.getElementById('Glng').value = new Number(glng).toPrecision(10);
		document.getElementById('span_glat').innerHTML = new Number(glat).toPrecision(10);
		document.getElementById('span_glng').innerHTML = new Number(glng).toPrecision(10);
		// translation du glatlng pour extraire le pays
		geocoder.getLocations(
			new GLatLng(glat, glng),
			function(result) {
				var dialog, len, point;
				if (result.Status.code != G_GEO_SUCCESS) {
					alert("Error: "+result.Status.code)
				} else {
					/*len = result.Placemark.length;
					// le dernier objet de Placemark est le pays
					//console.log(dump(result.Placemark,3,5));
					document.getElementById('pays').value = result.Placemark[len-1].address;
					document.getElementById('span_pays').innerHTML = result.Placemark[len-1].address;*/
					// http://code.google.com/intl/fr-FR/apis/maps/documentation/javascript/v2/services.html#ReverseGeocoding
					place = result.Placemark[0];
					countryName = place.AddressDetails.Country.CountryName;
					//console.log(var_dump(place));
					document.getElementById('pays').value = countryName;
					document.getElementById('span_pays').innerHTML = countryName;
					/*len = result.Placemark.length;
					if (len > 1) {
						// le dernier objet de Placemark est le pays
						//alert("Multiple matches were found.  I'll leave it as an exercise to handle this condition");
						//console.log(dump(result.Placemark,3,5));
						document.getElementById('pays').innerHTML = result.Placemark[len-1].address;
						document.getElementById('span_pays').innerHTML = result.Placemark[len-1].address;
					} else {
						/*point = new GLatLng(
							result.Placemark[0].Point.coordinates[1],
							result.Placemark[0].Point.coordinates[0]
						);
					}*/
				}
			}
		);
//		//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> ajout de l'&eacute;vennement dragend<br />"; }
		// on update les controles � chaque d�placement du marker
		GEvent.addListener(marker, "dragend",
			function(coord) {
				//document.getElementById('Glat').value = coord.lat();
				//document.getElementById('Glng').value = coord.lng();
				document.getElementById('Glat').value = new Number(coord.lat()).toPrecision(10);
				document.getElementById('Glng').value = new Number(coord.lng()).toPrecision(10);
				document.getElementById('span_glat').innerHTML = new Number(coord.lat()).toPrecision(10);
				document.getElementById('span_glng').innerHTML = new Number(coord.lng()).toPrecision(10);
				geocoder.getLocations(
					coord,
					function(result) {
						var dialog, /*len, */place, point;
						if (result.Status.code != G_GEO_SUCCESS) {
							alert("Error: "+result.Status.code)
						} else {
							/*len = result.Placemark.length;
							// le dernier objet de Placemark est le pays
							console.log(var_dump(result.Placemark));
							document.getElementById('pays').value = result.Placemark[len-1].address;
							document.getElementById('span_pays').innerHTML = result.Placemark[len-1].address;*/
							
							// http://code.google.com/intl/fr-FR/apis/maps/documentation/javascript/v2/services.html#ReverseGeocoding
							place = result.Placemark[0];
							countryName = place.AddressDetails.Country.CountryName;
							//console.log(var_dump(place));
							document.getElementById('pays').value = countryName;
							document.getElementById('span_pays').innerHTML = countryName;
						}
					}
				);
			}
		);
	}
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> ajout du marker au tableau<br />"; }
	markers.push(marker);
	if (addOverlay) {
		map.addOverlay(marker);
		map.setCenter(marker.getLatLng());
	}
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> return du marker<br />"; }
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span><br />"; }
	return marker;
}

function updateMarkerFromLatLng(marker, glat, glng, categorie, titre, desc, href, draggable, addOverlay) {
//	var funcName = arguments.callee.toString();
//	funcName = funcName.substr('function '.length);        // trim off "function "
//	funcName = funcName.substr(0, funcName.indexOf('('));        // trim off everything after the function name
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> >> Entering function<br />"; }
	if ((marker != null) && (markerExists(marker.getLatLng()))) {
//		//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> Le marker n'existe pas...<br />"; }
		// on r�cup�re tous les param�tres manquant, existant dans le marker actuel
		if (isNull(glat)) { glat = marker.getLatLng().lat(); }
		if (isNull(glng)) { glng = marker.getLatLng().lng(); }
		if (isNull(titre)) { titre = marker.getTitle(); }
		/*if (isNull(desc)) {
			//if (debug) { debug.innerHTML += dump(document.getElementById("infoWindowHTML"),0,2); }
			//return;
			//map.getInfoWindow().show();
			var infoWindow = map.getInfoWindow().getContentContainers();
//			//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> " + dump(infoWindow,0,1) + "<br />"; }
			//desc = infoWindow[O].innerHTML;
			desc = document.getElementById("googlemap_info_desc").innerHTML;
//			if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> " + desc + "<br />"; }
			//desc = infoWindow.getElementsByTagName("p")[0].innerHTML;
//			//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> desc = " + desc; }
		}*/
		if (isNull(href)) { href = location.href; }
		if (isNull(draggable)) {
			draggable = marker.draggingEnabled();
		}
		// si le marker existe, on le supprime
		removeMarker(marker);
	}
//	//if (debug) { debug.innerHTML += "<span class='debug_funcname'>["+funcName+"]:</span> << Leaving function<br />"; }
	return createMarkerFromLatLng(glat, glng, categorie, titre, desc, href, draggable, addOverlay);
}

function updateMarker(marker, address, categorie, titre, desc, href, draggable, addOverlay) {
	if (address != "") {
		// translation en glatlng pour afficher le marker
		geocoder.getLatLng(
			address,
			function(point) {
				if (!point) {
				//alert(address + " not found");
				} else {
					updateMarkerFromLatLng(marker, point.lat(), point.lng(), categorie, titre, desc, href, draggable, addOverlay);
				}
			}
		);
	}
}

function createMarker(address, categorie, titre, desc, href, draggable) {
	// options du marker
	var mOptions = { 
		title: titre,
		draggable: draggable,
		bouncy: true
	};
	// options de la fen�tre d'infos
	var wOptions= {
		maxwidth: 50
	};
	if (address != "") {
		geocoder.getLatLng(
			address,
			function(point) {
				if (!point) {
				//alert(address + " not found");
				} else {
					createMarkerFromLatLng(point.lat(), point.lng(), categorie, titre, desc, href, draggable);
/*					//var map = new GMap2(document.getElementById("map_canvas"));
					//map.setCenter(point, 6);
					var marker = new GMarker(point, mOptions);
					//map.addOverlay(marker);
					GEvent.addListener(marker, "click",
						function() {
							marker.openInfoWindowHtml("<div class='googlemap_info'><h3>" + titre + "</h3><br /><p>" + desc + "</p><br /><a href='" + href + "'>En savoir plus ...</a><br /></div>", wOptions);
						}
					);
					GEvent.addListener(currentMarker, "dragend",
						function(coord) {
							document.getElementById('Glat').value = coord.lat();
							document.getElementById('Glng').value = coord.lng();
						}
					);
					markers.push(marker);*/
				}
			}
		);
	}
}

function addMarker(address, categorie, titre, desc, href, draggable) {
	// options du marker
	var mOptions = { 
		title: titre,
		draggable: draggable,
		bouncy: true
	};
	// options de la fen�tre d'infos
	var wOptions= {
		maxwidth: 50
	};
	if (address != "") {
		geocoder.getLatLng(
			address,
			function(coord) {
				if (!coord) {
					//alert(address + " not found");
				} else {
					//alert("createMarkerFromLatLng(coord.lat(), coord.lng(), categorie, titre, desc, href, draggable); = > createMarkerFromLatLng("+coord.lat()+", "+coord.lng()+", "+categorie+", "+titre+", "+desc+", "+href+", "+draggable+");");
					marker = createMarkerFromLatLng(coord.lat(), coord.lng(), categorie, titre, desc, href, draggable, true);
				}
			}
		);
	}
	//map.addOverlay(marker);
}

function addAdminMarker(address) {
	// options du marker
	var mOptions = { 
		//title: titre,
		draggable: true,
		bouncy: true
	};
	if (address != "") {
		geocoder.getLatLng(
			address,
			function(coord) {
				if (!coord) {
					alert(address + " not found");
				} else {
					//var map = new GMap2(document.getElementById("map_canvas"));
					map.setCenter(coord, 6);
					var currentMarker = new GMarker(coord, mOptions);
					markers.push(currentMarker);
					map.addOverlay(currentMarker);
					document.getElementById('Glat').value = coord.lat();
					document.getElementById('Glng').value = coord.lng();
					GEvent.addListener(currentMarker, "dragend",
						function(coord) {
							document.getElementById('Glat').value = coord.lat();
							document.getElementById('Glng').value = coord.lng();
						}
					);
					GEvent.addListener(marker, "click",
						function() {
							marker.openInfoWindowHtml("<div class='googlemap_info'><h3>" + titre + "</h3><br /><p>" + desc + "</p><br /><a href='" + href + "'>En savoir plus ...</a><br /></div>", wOptions);
						}
					);
				}
			}
		);
	}
}

//
// fonction g�n�rique pour modifier le marker courant
//
function updateCurrentMarker(prop, value) {
	if (markers[0] != null) {
		switch (prop) {
			case "address" : 	var address = value;
								if (address != "") {
								geocoder.getLatLng(
									address,
									function(coord) {
									  if (!coord) {
										//alert(address + " not found");
									  } else {
										//var map = new GMap2(document.getElementById("map_canvas"));
										//map.setCenter(coord);
										//var currentMarker = new GMarker(coord, mOptions);
										//markers.push(currentMarker);
										//map.addOverlay(currentMarker);
										markers[0].setLatLng(coord);
									  }
									}
								);
							}
							break;
			case "title" :	var titre = value;
							
							break;
			case "desc"	:	var desc = value;
							GEvent.addListener(markers[0], "click",
								function() {
									markers[0].openInfoWindowHtml(desc);
								}
							);
							break;
		}
	} else {
		switch (prop) {
			case "address" : 	var address = value;
								addAdminMarker(address);
								break;
		}
	}
}

function googleMapUpdateAdminMarker0() {
	var titre = document.getElementById('titre').value;
	var address = document.getElementById('lieu').value;
	var desc = document.getElementById('contexte').value;
	geocoder.getLatLng(
		address,
		function(coord) {
		  if (!coord) {
			alert(address + " not found");
		  } else {
			//var map = new GMap2(document.getElementById("map_canvas"));
			//map.setCenter(coord, 6);
			//var currentMarker = new GMarker(coord, mOptions);
			//markers.push(currentMarker);
			//map.addOverlay(currentMarker);
			markers[0].setLatLng(coord);
		  }
		}
	);
	GEvent.addListener(markers[0], "click",
		function() {
			markers[0].openInfoWindowHtml("<h3>"+titre+"</h3>"+desc);
		}
	);
}

function googleMapSetCenter(coord) {
	if (GBrowserIsCompatible()) {
		map.setCenter(coord);
	}
}

function googleMapLocaliseChantier(address) {
	geocoder.getLatLng(address, function (coord) {
		if (coord) {
			googleMapSetCenter(glat, glng);
		}
	});
	updateCurrentMarker("address", address);
}

function openMarkerInfoWindow(coord) {
	if (markerExists(coord)) {
		marker = getMarker(coord);
		// on simule un click sur le marker
		GEvent.trigger(marker, 'click');
	}
}

function markerExists(coord) {
	//if (debug) { debug.innerHTML += dump(markers,0,1); }
	//if (markers.length == 0) { return false; }
	for (var i = 0; i < markers.length; i++) {
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getLatLng().lat() !== marker.getLatLng().lat())
		if (markers[i].getLatLng().equals(coord)) { return true; }
	}
	return false;
}

function markersHideAll() {
	for (var i = 0; i < markers.length; i++) {
		markers[i].hide();
	}
}

function getMarker(coord) {
	for (var i = 0; i < markers.length; i++) {
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getLatLng().lat() !== marker.getLatLng().lat())
		if (markers[i].getLatLng().equals(coord)) { return markers[i]; }
	}
}

/*
 * Supprime un marker du tableau markers et de la carte map
 *
 * retourne true si un marker a �t� trouv� et supprim�, faux si aucun marker n'a �t� trouv�
 */
function removeMarker(marker) {
	for (var i = 0; i < markers.length; i++) {
		//if (debug) { debug.innerHTML += dump(markers[i],0,1); }
		//if (m.getLatLng().lat() !== marker.getLatLng().lat())
		if (markers[i].getLatLng().equals(marker.getLatLng())) {
			markers.splice(i,1);
			map.removeOverlay(marker);
			return true;
		}
	}
	return false
}

function googleMapDebug() {
	var debugDiv = document.getElementById('debug');
	debugDiv.innerHTML = "<table><tr><td>Informations de debuggage : </td></tr>";
	//for (m in markers) {
	//	debugDiv.innerHTML += "<tr><td>" + m + "</td></tr><br />";
		//for (prop in m) {
		//	debugDiv.innerHTML += "<tr><td>" + prop + "</td></tr><br />";
		//}
	//}
	
	debugDiv.innerHTML += "<tr><td>Fin.</td></tr></table>";		
	debugDiv.innerHTML += dump(currentMarker,0,1);
}

function updateControls(glat_ctrl, glng_ctrl, zoomLevel_ctrl) {
	var coord = new GLatLng(map.getCenter());
	glat_ctrl.value = map.getCenter().lat(); //new Number(coord.lat()).toPrecision(10);
	glng_ctrl.value = map.getCenter().lng(); //new Number(coord.lng()).toPrecision(10);
	zoomLevel_ctrl.value = map.getZoom();
}

