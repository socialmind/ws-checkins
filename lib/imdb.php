<?php
require_once('parsecsv.lib.php');

/**
 * IMDB Check Ins Tracker.
 *
 * @version v1.0, July 21, 2013
 * @copyright Websthetics <info@websthetics.gr>
 * @license http://mozilla.org/MPL/2.0/
 * 
 * @author Apostolos Kritikos <akritiko@gmail.com>
 * 
 * @package imdb-checkin
 * 
 * @description Class that analyzes the checkin history of an IMDB user and produces
 * statistics. 
 *
 * LICENSE
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public 
 * License, v. 2.0. If a copy of the MPL was not distributed with this 
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 * 
 */
 
class IMDB_Check_Ins
{
	/** CSV handler */
	private $csv;
	/** Month name to number associative array */
	private $months = array(
		"Jan" => "1",
		"Feb" => "2",
		"Mar" => "3",
		"Apr" => "4",
		"May" => "5",
		"Jun" => "6",
		"Jul" => "7",
		"Aug" => "8",
		"Sep" => "9",
		"Oct" => "10",
		"Nov" => "11",
		"Dec" => "12"
	);
	
	/** Number of checkins in total */
	private $nof_elements_seen;
	
	/** 
	 * Associative array that holds the Elements seen 
	 * and their respective URLs 
	 */
	private $elements_seen;
	
	/** Number of TV series episodes seen */
	private $nof_tv_episodes_seen;
	
	/** 
	 * Number of TV series seen (to be separated from
	 * specific episodes 
	 */
	private $nof_tv_series_seen;
	
	/** Number of movies seen */
	private $nof_movies_seen;
	
	/** IMDB ratings average */
	private $IMDB_rating_average;
	
	/** IMDB ratings min */
	private $IMDB_rating_min;
	
	/** IMDB ratings max */
	private $IMDB_rating_max;
	
	/** User's rating average */
	private $user_rating_average;
	
	/** User's rating min */
	private $user_rating_min;
	
	/** User's rating max */
	private $user_rating_max;
	
	/** TV series episodes' genre stats */
	private $tv_genre_stats; 
	
	/** Movies' genre stats */
	private $movie_genre_stats; 
	
	/** Total genre stats */
	private $all_genre_stats; 
	
	/** TV episodes year stats */
	private $tv_year_stats; 
	
	/** Movies year stats */
	private $movie_year_stats; 
	
	/** All year stats */
	private $all_year_stats; 
	
	/** 
	 * Constructor. It creates an instance of @link IMDB_Check_Ins based on 
	 * the IMDB user's ID. Apart from initializing the attributes of the class 
	 * constructor also configures the CSV parsing settings. Currently the CSV 
	 * data are being read in reverse order and are being sorted based on the 
	 * CSV's "position" field.
	 */
	public function __construct( $csv ) {
		
		$this->csv = $csv;
		
		$this->nof_elements_seen = 0;
		$this->elements_seen = array();
		$this->nof_tv_episodes_seen = 0;
		$this->nof_tv_series_seen = 0;
		$this->nof_movies_seen = 0;
		$this->IMDB_rating_average = 0.0;
		$this->IMDB_rating_min = 999.9;
		$this->IMDB_rating_max = 0.0;
		$this->tv_genre_stats = array(); 
		$this->movie_genre_stats = array(); 
		$this->all_genre_stats = array(); 
		$this->tv_year_stats = array(); 
		$this->movie_year_stats = array(); 
		$this->all_year_stats = array(); 
	}
	
	/** 
	 * Loops through the rows of the CSV file, calculates
	 * the statistics and stores the results to appropriates
	 * attribute variables.
	 */
	public function analyze( ) {
		$this->csv->auto( $this->filename );
		$row_count = 0;
		$IMDB_rating_sum = 0;
		
		foreach ( $this->csv->data as $key => $row ) : 
			//print_r($row);
			
			//Calculates the number of elements seen by the user
			if ( $row_count == 0 ) {
				$this->nof_elements_seen = $key;
			}
			//Creates an associative array with the elements seen
			//by the user in form of "Title" => "URL". 
			$this->elements_seen[ $row["Title"] ] = $row["URL"];
			
			//Calculates the number of TV episodes seen by the user
			if ( $row["Title type"] == "TV Episode" ) {
				$this->nof_tv_episodes_seen++;
			}
			
			//Calculates the number of movies seen by the user
			if ( $row["Title type"] == "Feature Film" ) {
				$this->nof_movies_seen++;
			}
			
			//Calculates the number of TV series seen by the user
			//TODO: when calculate the series we should remove the duplicates!
			if ( $row["Title type"] == "TV Series" ) {
				$this->nof_tv_series_seen++;
			}
			
			//Calculates IMDB rating sum
			$IMDB_rating_sum = $IMDB_rating_sum + $row["IMDb Rating"];
			
			//Calculate IMDB rating max
			if ( $row["IMDb Rating"] > $this->IMDB_rating_max ) {
				$this->IMDB_rating_max = $row["IMDb Rating"];
			}
			
			//Calculate IMDB rating min
			if ( $row["IMDb Rating"] < $this->IMDB_rating_min ) {
				$this->IMDB_rating_min = $row["IMDb Rating"];
			}
			
			//Calculate genre statistics
			$genres = explode ( ", " , $row["Genres"] ); 
			if ( $row["Title type"] == "TV Episode" ) {
				//TV genre statistics
				$this->calculate_genre_statistics( $this->tv_genre_stats, $genres );
			} else if ( $row["Title type"] == "Feature Film" ) {
				//Movie genre statistics
				$this->calculate_genre_statistics( $this->movie_genre_stats , $genres );
			}
			
			//All genre statistics. What it does, is that combines TV and Movies associative arrays
			$this->all_genre_stats = $this->merge_associative_arrays( $this->tv_genre_stats , $this->movie_genre_stats );
			
			//Calculate year statistics
			if ( $row["Title type"] == "TV Episode" ) {
				//TV genre statistics
				$this->calculate_year_statistics( $this->tv_year_stats , $row["Year"] );
			} else if ( $row["Title type"] == "Feature Film" ) {
				//Movie genre statistics
				$this->calculate_year_statistics( $this->movie_year_stats , $row["Year"] );
			}
			
			//All genre statistics. What it does, is that combines TV and Movies associative arrays
			$this->all_year_stats = $this->merge_associative_arrays( $this->tv_year_stats , $this->movie_year_stats );
			$row_count++;

		endforeach ; 
		
		//Calculates IMDB rating average (all elements seen)
		$this->IMDB_rating_average = $IMDB_rating_sum / $this->nof_elements_seen;
	}
	
	/**
	 * Calculates Genre Statistics. Stores genres and their 
	 * frequencies to an associative array. 
	 * 
	 * @param $genre_array
	 * @param $genres
	 */
	private function calculate_genre_statistics( &$genre_array , $genres ) {
		for ( $i = 0 ; $i < sizeof( $genres ) ; $i++ ) {
			if ( array_key_exists( $genres[$i] , $genre_array ) ) {
				$genre_array[ $genres[$i] ] = ( $genre_array[ $genres[$i] ] + 1 );
			} else {
				$genre_array[ $genres[$i] ] = 1;			
			}
		}
	}
	
	/**
	 * Calculates Genre Statistics. Stores genres and their 
	 * frequencies to an associative arrays. 
	 * 
	 * @param $year_array
	 * @param $year
	 */
	private function calculate_year_statistics( &$year_array , $year ) {
		if ( array_key_exists( $year , $year_array ) ) {
			$year_array[$year] = ( $year_array[$year] + 1 );
		} else {
			$year_array[$year] = 1;		
		}
	}
	
	/** 
	 * Merges two associative arrays to one summing up the frequencies for 
	 * identical keys.
	 * 
	 * @param $array1 
	 * @param $array2 
	 */
	private function merge_associative_arrays( $array1 , $array2 ) {
		$sums = array();
		foreach ( array_keys( $array1 + $array2 ) as $key) {
		    $sums[$key] = ( isset( $array1[$key] ) ? $array1[$key] : 0 ) + ( isset( $array2[$key] ) ? $array2[$key] : 0 );
		}
		return $sums;
	}
	
	/**
	 * Transforms an IMDB generated date to a date with 
	 * YYYYMMDD format.
	 * 
	 * @return string (date in YYYYMMDD format)
	 */
	private function IMDB_date_to_date( $IMDB_date ) {
		$array_date = explode( " " , $IMDB_date );
		return $array_date[4] . $this->months[ $array_date[1] ] . $array_date[2];
	}
	
	/**
	 * Returns the year part of an IMDB date.
	 * 
	 * @return string
	 */
	private function get_year( $IMDB_date ) {
		$array_date = explode(" " , $IMDB_date);
		return $array_date[4];
	}
	
	/**
	 * Returns the month part of an IMDB date. 
	 * 
	 * @return string
	 */
	private function get_month($IMDB_date) {
		$array_date = explode( " " , $IMDB_date );
		return $this->months[ $array_date[1] ];
	}
	
	/**
	 * Returns the day part of an IMDB date. 
	 * 
	 * @return string
	 */
	private function get_day( $IMDB_date ) {
		$array_date = explode( " " , $IMDB_date );
		return $array_date[2];
	}
	
	/**
	 * 
	 */
	public function get_CSV( ) {
		return $this->csv;
	}
	
	//GETTERS: @todo add phpdoc!
	 
	public function get_nof_elements_seen( ) { 
		return $this->nof_elements_seen; 
	} 
	
	public function get_elements_seen( ) {
		return $this->elements_seen; 
	}
	 
	public function get_nof_tv_episodes_seen( ) { 
		return $this->nof_tv_episodes_seen; 
	}
		 
	public function get_nof_tv_series_seen( ) { 
		return $this->nof_tv_series_seen; 
	} 
	
	public function get_nof_movies_seen( ) { 
		return $this->nof_movies_seen; 
	} 
	
	public function get_IMDB_rating_average( ) { 
		return $this->IMDB_rating_average; 
	}
	 
	public function get_IMDB_rating_min( ) { 
		return $this->IMDB_rating_min; 
	}
	 
	public function get_IMDB_rating_max( ) { 
		return $this->IMDB_rating_max; 
	} 
	
	public function get_user_rating_average( ) { 
		return $this->user_rating_average; 
	} 
	
	public function get_user_rating_min( ) { 
		return $this->user_rating_min; } 
	
	public function get_user_rating_max( ) { 
		return $this->user_rating_max; 
	} 
	
	public function get_tv_genre_stats( ) { 
		return $this->tv_genre_stats; 
	} 
	
	public function get_movie_genre_stats( ) { 
		return $this->movie_genre_stats; 
	} 
	
	public function get_all_genre_stats( ) { 
		return $this->all_genre_stats; 
	} 
	
	public function get_tv_year_stats( ) { 
		return $this->tv_year_stats; 
	} 
	
	public function get_movie_year_stats( ) { 
		return $this->movie_year_stats; 
	} 
	
	public function get_all_year_stats( ) { 
		return $this->all_year_stats; 
	} 
}
?>
