<?php
/**
 * @package		com_apl_j25
 * @subpackage	js
 * @brief		javascript library to provide custom helper functions
 * @copyright	Copyright (C) 2010 - 2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 * @license		Affero GNU General Public License version 3 or later; see LICENSE.txt
 * 
 * @TODO		
 */

/** 
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

abstract class APLFunctions {
	
	/**
	 * @brief transforme une date en texte "vague", sauf si $date_exacte = true
	 * @example "de mi-juin à fin aout"
	 * @example "du 5 juillet au 23 décembre"
	 * 
	 * @param type $date_debut
	 * @param type $date_fin
	 * @param type $date_exacte
	 * @return string
	 */
	function read_date_as_human($date_debut, $date_fin, $date_exacte=false) {
		// les dates sont au format "YYY-MM-DD hh:mm:ss"
		//setlocale(LC_ALL, "fr_FR");
		setlocale(LC_ALL, array('fr_FR', 'fr_FR.utf8', 'fr_FR.iso88591', 'fr_FR.iso885915@euro', 'fr_FR@euro'));
		
		$mois_debut = intval(date("m", strtotime($date_debut)));
		$mois_fin = intval(date("m", strtotime($date_fin)));
		$jour_debut = intval(date("d", strtotime($date_debut)));
		$jour_fin = intval(date("d", strtotime($date_fin)));

		if ($date_exacte) {
			$debut = "Du ".$jour_debut." ".strftime("%B",mktime(0, 0, 0, $mois_debut));
			$fin = $jour_fin." ".strftime("%B",mktime(0, 0, 0, $mois_fin));
			//return strtolower(htmlentities($debut." au ".$fin, ENT_COMPAT, 'iso-8859-1', false));
			return strtolower(htmlentities($debut." au ".$fin, ENT_COMPAT, 'UTF-8', false));
		} else {
			// cas toute l'ann&eacute;e
			if ($mois_debut == 1 and $mois_fin == 12 and $jour_debut == 1 and $jour_fin == 31) { return"Toute l'ann&eacute;e"; }

			// cas tout le mois
			if ($jour_debut == 1 and $jour_fin >= 28 and $mois_debut == $mois_fin) { return "Tout le mois de " . strftime("%B",mktime(0, 0, 0, $mois_debut)); }

			// cas par tranche avec préfix : "début xx", "mi-xxx" à "fin-xxx"
			if ($jour_debut <= 10) { $debut = "De d&eacute;but " . strftime("%B",mktime(0, 0, 0, $mois_debut))." "; }
			if (10 < $jour_debut and $jour_debut < 20) { $debut = "De mi-" . strftime("%B",mktime(0, 0, 0, $mois_debut))." "; }
			if (20 <= $jour_debut) { $debut = "De fin " . strftime("%B",mktime(0, 0, 0, $mois_debut))." "; }
			//debug("debut is UTF-8 ? ".mb_check_encoding($debut, 'UTF-8'));
			//debug("debut = ".$debut);
			//echo($debut);
			//if (20 <= $jour_debut) { echo("De fin " . date("d F",mktime(0, 0, 0, $mois_debut, $jour_debut))." "); }

			if ($jour_fin <= 10) { $fin = "d&eacute;but " . strftime("%B",mktime(0, 0, 0, $mois_fin))." "; }
			if (10 < $jour_fin and $jour_fin < 20) { $fin = "mi-" . strftime("%B",mktime(0, 0, 0, $mois_fin))." "; }
			if (20 <= $jour_fin) { $fin = "fin " . strftime("%B",mktime(0, 0, 0, $mois_fin))." "; }
			//debug("fin is UTF-8 ? ".mb_check_encoding($fin, 'UTF-8'));
			//debug("fin = ".$fin);
			//echo($fin);
			//if (20 <= $jour_fin) { echo("&agrave; fin " . date("d F",mktime(0, 0, 0, $mois_fin, $jour_fin))." "); }
			//return strtolower(htmlentities($debut." &agrave; ".$fin, ENT_COMPAT, 'iso-8859-1', false));
			return strtolower(htmlentities($debut." &agrave; ".$fin, ENT_COMPAT, 'UTF-8', false));
		}
	}

	/**
	 * @brief	Convert newline (\n) to HTML br tag (<br />)
	 * the PHP's function nl2br converts \n to <br />\n to keep HTML code clear. But javascript does not like it (at least javascript parameters)
	 * 
	 * @param string $text
	 * @return string	converted string
	 */
	function mynl2br($text) { 
		return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
	}
	/**
	 * Convert BR tags to nl
	 * from http://fr2.php.net/manual/en/function.nl2br.php
	 *
	 * @param string The string to convert
	 * @return string The converted string
	 */
	function br2nl($string){ 
		$return=preg_replace('/<br[[:space:]]*/?[[:space:]]*>/i',chr(13).chr(10),$string); 
		return $return; 
	}
	
	/*
	 * Converti correctement les chaines de caractères
	 * 
	 * @param string La chaine a traiter
	 * @return string La chaine convertie
	 */
	function escapeString($string) {
		// d'abord, on unEscape pour partir sur une base propre
		//$string = APLFunctions::unEscapeString($string);
		//stripslashes($string);
		$string = stripslashes(html_entity_decode(stripslashes($string)));
		// convertir les &rsquo et &lsquo en '
		$string = preg_replace('/&rsquo;/i',"'",$string);
		$string = preg_replace('/&lsquo;/i',"'",$string);
		$string = preg_replace('/&#039;/i',"'",$string);
		$string = preg_replace('/Ê¼/i',"'",$string);
		// on converti la chaine en UTF-8
		//$string = mb_convert_encoding($string,'UTF-8');
		// remplacer les <br /> et retranscrire en HTML
		// PHP >= 5.4
		//$string = htmlentities(APLFunctions::br2nl($string), ENT_QUOTES | ENT_SUBSTITUTE | ENT_DISALLOWED | ENT_HTML5, 'UTF-8');
		// PHP <= 5.3
		$string = htmlentities(APLFunctions::br2nl($string), ENT_QUOTES);//, 'UTF-8');
		return $string;
	}

	/**
	 * @brief	Décode correctement les chaines converties avec APLFunctions::escapeString
	 * 
	 * @param	string	La chaine a traiter
	 * @return	string	La chaine convertie
	 */
	function unEscapeString($string) {
		//stripslashes($string);
		// PHP >= 5.4
		//$string = html_entity_decode(stripslashes($string), ENT_QUOTES | ENT_SUBSTITUTE | ENT_DISALLOWED | ENT_HTML5, 'UTF-8');
		// PHP <= 5.3
		$string = html_entity_decode(stripslashes($string), ENT_QUOTES);
		// remplacer les \r\n par des <br />
		$string = nl2br($string);
		return $string;
	}

	/**
	 * @brief	pioche une image au hasard dans le répertoire donné
	 * 
	 * @param	string	$dir		répertoire ou chercher
	 * @param	string	$pattern	masque de recherche
	 * @return	string	chemin de l'image
	 */
	function getRandomImage($dir, $pattern="") {
		$i = 0;
		$photos = array();	
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if (($file == ".") or ($file == "..")) { continue; }
				$info = pathinfo($file);
				switch (strtolower($info['extension'])) {
					case 'jpg' :
					case 'jpeg' :
					case 'png' :
						if ((isset($pattern)) and ($pattern != "")) {
							if (preg_match("/".$pattern."/i", $file)) {
								$photos[$i] = $file;
								$i++;
							}
						} else {
							$photos[$i] = $file;
							$i++;
						}
						break;
				}
			}
			closedir($handle);
			//print_r($_SESSION['cache']['photos']);
			//print($i);
		}		
		$num_photo = rand(0, sizeof($photos) - 1);
		//echo $num_photo;
		//echo $_SESSION['cache']['photos'][$num_photo];
		//$current_photo=$_SESSION['cache']['photos'][$num_photo];
		if (isset($photos[$num_photo])) {
			return $dir."/".$photos[$num_photo];
		} else {
			return "";
		}
	}

	/**
	 * @brief	Converti les caractères accentués en leur équivalent non-accentué
	 * 
	 * @param string $chaine		La chaine à convertir
	 * @return	string	$replaced	La chaine convertie
	 */
	function removeAccents($chaine) {
		$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
		$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
		return str_replace($search, $replace, $chaine);
	}
}
?>
