<?php 

/**
 * Epguides Service Parser.
 *
 * @version v0.7, August 04, 2013
 * @copyright Websthetics <info@websthetics.gr>
 * @license http://mozilla.org/MPL/2.0/
 * 
 * @author Apostolos Kritikos <akritiko@gmail.com>
 * 
 * @package imdb-checkin
 * 
 * @description Class that retrieves information from http://www.epguides.com
 * statistics. 
 *
 * LICENSE
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public 
 * License, v. 2.0. If a copy of the MPL was not distributed with this 
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 * 
 */

class Epguides
{
	/** Movie / TV Series name (works like id) */
	private $id;
	
	/** 
	 * Constructor. It creates an instance of @link Epguides based on the Epguides
	 * Movie / TV Series ID.
	 */
	public function __construct($id) {
		$this->id = $id;
		
	}

	public function getEpisodesCSVUrl() {
		$yql = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%27http%3A%2F%2Fepguides.com%2F" . $this->id . "%2F%27%20and%20xpath%3D%27%20%2F%2Ftd[contains%28%40class%2C%22TVRage%22%29]%27&format=json&diagnostics=true&callback=";
		$json = file_get_contents($yql);
		$data = json_decode($json, TRUE);
		$url = "";
		
		$count = sizeof($data["query"]["results"]["td"]);
		for($i=0 ; $i < $count ; $i++) {
			if ( stristr( $data["query"]["results"]["td"][$i]["a"]["href"], 'exportToCSV.asp' ) ) {
				$url = $data["query"]["results"]["td"][$i]["a"]["href"];
			} 
		}
		return $url;
	}

	public function getEpisodesCSV($url) {
		$yql = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D'%0A" . $url . "'%20and%20xpath%3D'%2F%2Fpre'&format=json&diagnostics=true&callback=";
		$json = file_get_contents($yql);
		$data = json_decode($json, TRUE);

		$csvContent = $data["query"]["results"]["pre"];		

		return trim($csvContent);
	}
	
	public function getId() { 
		return $this->id; 
	} 
}
?>