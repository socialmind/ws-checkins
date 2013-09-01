<?php
/**
 * OMDB API Wrapper.
 *
 * @version v0.7, August 02, 2013
 * @copyright Shane Mc Cormack (https://github.com/ShaneMcC/moviemanager)
 * 
 * @author Shane Mc Cormack <hanemcc@gmail.com> 
 * @author Apostolos Kritikos <apostolos@websthetics.gr>
 * 
 * @package ws-checkins
 * 
 * @description Class that performs search queries using the OMDB API (http://www.omdbapi.com/)
 * 
 * NOTE: The source code of this class which was originally implemented by Shane Mc Cormack 
 * was altered by Websthetics ( http://websthetics.gr ) in order to suit the purpose of 
 * ws-checkins project.
 * (via: https://github.com/ShaneMcC/moviemanager/blob/master/api/OMDB.php)
 */

class OMDB {

	/** Default URL for the OMDB API service */
	private $URL = 'http://www.omdbapi.com/';

	/** 
	 * Performs search using the OMDB API using a search query given in the
	 * form of a url (e.g. http://www.omdbapi.com/?s=Star Wars) and returns 
	 * the result in array form.
	 * 
	 * @param $URL 
	 * @return array
	 */
	public function get_data( $URL ) {
		$data = json_decode( file_get_contents( $URL ) , true );
		//$result = strtolower( $data['Response'] ) == 'true';
		//unset( $data['Response'] );

		//return array( $result , $data );
		return $data;
	}

	/**
	 * Performs search using the OMDB API using a search query using the title
	 * year of release and returns the result in array form.
	 * 
	 * @param $title The title of the movie / TV Episode
	 * @param $year The year the movie / TV Episode was released
	 * @return array
	 */
	public function find_by_name_and_year( $title, $year ) {
		return $this->get_data( sprintf( '%s?t=%s&y=%d' , $this->URL , urlencode($title) , $year ) );
	}

	/** 
	 * Performs search using the OMDB API using a search query using the IMDB id of
	 * the movie / TV Episode and returns the result in array form.
	 * 
	 * @param $ID The IMDB id for the movie / TV Episode
	 * @return array
	 */
	public function find_by_IMDB_ID( $ID ) {
		return $this->get_data( sprintf('%s?i=%s' , $this->URL , urlencode( $ID ) ) );	
	}
}
?>