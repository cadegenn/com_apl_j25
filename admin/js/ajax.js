/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * @brief	delSpecFromDiv()	function used to delete a spec from the div and from the hidden input
 * @param	(integer)			id of spec to delete from list
 * @return	none
 */
function delSpecFromDiv(id) {
	select_specs = document.getElementById('jform_specs_list');
	//var key = select_specs.options[select_specs.selectedIndex].value;
	var div = document.getElementById('jform_specs_div');
	var input_json = document.getElementById('jform_specs');
	var obj_specs = {};

	if (input_json.value != "") {
		obj_specs = JSON.parse(input_json.value);
	}
	
	// del the current spec to object
	delete obj_specs[id];
	
	// stringify all that stuff
	input_json.value = JSON.stringify(obj_specs);
	
	// build table div
	var HTML = "<table class='specs clear'>";
	for (var key in obj_specs) { 
		if (obj_specs.hasOwnProperty(key)) {
			var label_tr;
			// looking for key's corresponding label
			for (var index in select_specs.options) {
				//if (select_specs.options[index].hasOwnProperty(value)) {
				if (select_specs.options[index].value == key) label_tr = select_specs.options[index].innerText;
				//}
			}
			var value = obj_specs[key];
			HTML += "<tr><td>" + label_tr + " : </td><td>" + value + "</td>";
                        HTML += "<td><a href='javascript:delSpecFromDiv(\""+key+"\");'><img src='components/com_velo/images/ico-16x16/delete.png' alt='delete' /></a></td>";
                        HTML += "</tr>";
		}
	}
	HTML += "</table>";
	div.innerHTML = HTML;	
}

/*
 * @brief	addSpecToDiv()	function used to add a spec to a div in a user-friendly form as well as the hidden json formated input field
 * @param	(string)	key (the spec name)
 * @param	(string)	value (the spec value)
 * @return	none
 */
function addSpecToDiv(select_specs, value) {
	var key = select_specs.options[select_specs.selectedIndex].value;
	//var label_tr = select_specs.options[select_specs.selectedIndex].innerText;
	var div = document.getElementById('jform_specs_div');
	var input_json = document.getElementById('jform_specs');
	//var array_specs = [];
	var obj_specs = {};

	if (input_json.value != "") {
		obj_specs = JSON.parse(input_json.value);
	}
	//for(var i in obj_specs) { array_specs.push([i, obj_specs[i]]); }

	// add the current spec to object
	obj_specs[key] = value;
	
	//console.log(array_specs);
	//console.log(obj_specs);
	//current_specs.push({key: value});

	// stringify all that stuff
	input_json.value = JSON.stringify(obj_specs);
	
	// build table div
	var HTML = "<table class='specs clear'>";
	for (var key in obj_specs) { 
		if (obj_specs.hasOwnProperty(key)) {
			var label_tr;
			// looking for key's corresponding label
			for (var index in select_specs.options) {
				//if (select_specs.options[index].hasOwnProperty(value)) {
				if (select_specs.options[index].value == key) {
					// we don't have acces to the query result as in fields/spec.php, so we process strings
					// the "key" is of the form "label (unit)"
					unit = select_specs.options[index].innerText.match(/\(.*\)/)[0].replace(/^\(|\)$/gm, '');
					label_tr = select_specs.options[index].innerText.replace(/ (.*)/, "");
				}
				//}
			}
			var value = obj_specs[key];
			HTML += "<tr><td>" + label_tr + " : </td><td>" + value + " " + unit + "</td>";
                        HTML += "<td><a href='javascript:delSpecFromDiv(\""+key+"\");'><img src='components/com_velo/images/ico-16x16/delete.png' alt='delete' /></a></td>";
                        HTML += "</tr>";
		}
	}
	HTML += "</table>";
	div.innerHTML = HTML;
}

/**
 * @brief	ccoClick()	function invoqued when the jform_const_composant_id dropdown is clicked
 *						it must reset the jform_marque_id dropdown
 *						and update the jform_model_id with a list of parts returned from server
 * @returns void
 */
function ccoClick() {
	// reset jform_marque_id dropdown
	if (document.getElementById("jform_marque_id")) {
		document.getElementById("jform_marque_id").selectedIndex = 0;
	}
	// update jform_model_id
	getParts();
}

/**
 * @brief	getParts()	ask server for parts with composant_id and marque_id if exists
 * @returns void
 */
function getParts() {
	var const_composant_id = 0;
	if (document.getElementById("jform_const_composant_id")) { const_composant_id = document.getElementById("jform_const_composant_id").value; }
	var marque_id = 0;
	if (document.getElementById("jform_marque_id")) { marque_id = document.getElementById("jform_marque_id").value; }
	var url='index.php?option=com_velo&format=raw&task=getParts';
	var post_data = 'const_composant_id='+const_composant_id+'&marque_id='+marque_id;
	var request = new Request({
		url: url,
		method:'post',
		data: post_data,
		onSuccess: function(responseText){
			//document.getElementById('system-debug').innerHTML=  responseText;
			$('jform_model_id').empty();
			Array.each(JSON.parse(responseText), function(optionHtml, i){
				new Element('option', {
					value: optionHtml.value,
					text: optionHtml.text
				}).inject($('jform_model_id'));
			});
		}
	}).send();
}

/*
 * @brief	getMarque()		ask serveur for a marque_id from an item_id and select it if a jform_marque_id object exist on the document
 * @since	0.0.28
 */
function getMarque() {
	var model_id = 0;
	if (document.getElementById("jform_marque_id")) { model_id = document.getElementById("jform_model_id").value; }
	var url='index.php?option=com_velo&format=raw&task=getMarque';
	var post_data = 'model_id='+model_id;
	var request = new Request({
		url: url,
		method:'post',
		data: post_data,
		onSuccess: function(responseText){
			//document.getElementById('system-debug').innerHTML=  responseText;
			//$('jform_marque_id').empty();
			console.log("%o", responseText);
			Array.each(JSON.parse(responseText), function(answer, i){
				$('jform_marque_id').set('value',answer.marque_id);
			});
		}
	}).send();
}
