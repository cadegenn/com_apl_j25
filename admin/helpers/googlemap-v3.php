<?php
/**
 * class found @url http://erlycoder.com/45/php-server-side-geocoding-with-google-maps-api-v3
 * 
 */
class GMAPv3{
	static private $url = "http://maps.google.com/maps/api/geocode/json?sensor=false";

	static public function geocode($url){
		$resp_json = self::curl_file_get_contents($url);
		//$resp_json = file_get_contents($url);
		$resp = json_decode($resp_json, true);

		if($resp['status']='OK'){
			return $resp['results'][0];
		}else{
			return false;
		}
	}
	static public function geocodeFromLatLng($lat, $lng) {
		$url = self::$url."&latlng=".urlencode($lat).",".urlencode($lng);
		return self::geocode($url);
	}
	static public function geocodeFromAddress($address) {
		$url = self::$url."&address=".urlencode($address);
		return self::geocode($url);
	}

	static public function getCountryFromLatLng($lat,$lng) {
		$results = self::geocodeFromLatLng($lat, $lng);
		//echo("<pre>"); var_dump($results); echo("</pre>"); die();
		$country = new stdClass();
		foreach ($results['address_components'] as $i => $component) {
			if ($component['types'][0] == "country") {
				$country->long_name = $component['long_name'];
				$country->short_name = $component['short_name'];
				break;
			}
		}
		
		return $country;
	}
	
	static public function getCountryFromAddress($address) {
		$results = self::geocodeFromAddress($address);
		//echo("<pre>"); var_dump($results); echo("</pre>"); die();
		$country = new stdClass();
		foreach ($results['address_components'] as $i => $component) {
			if ($component['types'][0] == "country") {
				$country->long_name = $component['long_name'];
				$country->short_name = $component['short_name'];
				break;
			}
		}
		
		return $country;
	}
	static public function getLocation($address){
		$results = self::geocodeFromAddress($address);

		return $results['geometry']['location'];
	}

	static private function curl_file_get_contents($URL){
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);

		if ($contents) return $contents; else return FALSE;
	}
}

?>