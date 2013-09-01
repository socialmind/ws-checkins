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
	public function __construct( $id ) {
		$this->id = $id;
	}

	public function get_episodes_CSV_URL( ) {
		$YQL = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%27http%3A%2F%2Fepguides.com%2F" . $this->id . "%2F%27%20and%20xpath%3D%27%20%2F%2Ftd[contains%28%40class%2C%22TVRage%22%29]%27&format=json&diagnostics=true&callback=";
		$JSON = file_get_contents( $YQL );
		$data = json_decode( $JSON , TRUE );
		$URL = "";
		
		$count = sizeof( $data["query"]["results"]["td"] );
		for ( $i=0 ; $i < $count ; $i++ ) {
			if ( stristr( $data["query"]["results"]["td"][$i]["a"]["href"] , 'exportToCSV.asp' ) ) {
				$URL = $data["query"]["results"]["td"][$i]["a"]["href"];
			} 
		}
		
		return $URL;
	}

	public function get_episodes_CSV( $URL ) {
		$YQL = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D'%0A" . $URL . "'%20and%20xpath%3D'%2F%2Fpre'&format=json&diagnostics=true&callback=";
		$JSON = file_get_contents( $YQL );
		$data = json_decode( $JSON , TRUE);
		$csvContent = $data["query"]["results"]["pre"];		

		return trim( $csvContent );
	}
	
	public function get_ID() { 
		return $this->id; 
	} 
}
?>