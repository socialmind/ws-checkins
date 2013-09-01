<?php
/**
 * Freebase Search Wrapper.
 *
 * @version v0.7, August 02, 2013
 * @copyright Wern Ancheta (http://anchetawern.github.io) 
 * 
 * @author Wern Ancheta <ancheta.wern@gmail.com>
 * @author Apostolos Kritikos <apostolos@websthetics.gr>
 * 
 * @package ws-checkins
 * 
 * @description Class that performs search queries to http://freebase.com
 * 
 * NOTE: The source code of this class which was originally implemented by Wern Ancheta 
 * was altered by Websthetics ( http://websthetics.gr ) in order to suit the purpose of 
 * ws-checkins project.
 * (via: http://anchetawern.github.io/blog/2013/02/11/getting-started-with-freebase-api/)
 */

class Freebase{

	/** A valid Google API key to use with Freebase */
	private $api_key;

	/** 
	 * Constructor. It creates an instance of @link Freebase based using a   
	 * Google API key.
	 */
	public function __construct($api_key) {
		$this->api_key = $api_key;
	}

	/**
	 * Create a new method and call it search this will utilize the search service. Here 
	 * we have 1 required parameter ($query) and 5 optional parameters. You can see a full 
	 * list of the parameters that you can specify in the wiki for the Search API.
	 * ( http://wiki.freebase.com/wiki/ApiSearch )
	 */
	public function search( $query , $filter = '' , $start = 0 , $limit = 10 , $exact = 'false' ) {
	  if ( ! empty( $query ) ) {
	      $query = urlencode( $query );
	      $URL = 'https://www.googleapis.com/freebase/v1/search?query=' . $query;
	      $URL .= '&filter=(' . urlencode( $filter ) . ')';
	      $URL .= '&start=' . $start;
	      $URL .= '&limit=' . $limit;
	      $URL .= '&exact=' . $exact;
	      $URL .= '&key=' . $this->api_key;
	      $response = json_decode( file_get_contents( $URL ) , true );
	      
	      return $response['result'];
	  }
	}

	/**
	 * There’s also the Image Service which simply returns an image base on the entity ID. The 
	 * entity ID can be a string or a number representing the entity. There’s not really much 
	 * parameters that you can specify for the image service, be sure to check out the wiki for 
	 * the image service ( http://wiki.freebase.com/wiki/ApiImage ) to learn more. The first 
	 * parameter is the entity_id which for globally known entities can be just words separated 
	 * by underscores like we saw earlier. As you can see were not actually using the 
	 * file_get_contents method here since we only need the url for the image we only return the 
	 * url itself.
	 */
	public function image( $entity_id , $max_width = 150 , $max_height = 150 ) {
	  if( ! empty( $entity_id ) ) {
	      $URL = 'https://usercontent.googleapis.com/freebase/v1/image' . $entity_id;
	      $URL .= '?maxwidth=' . $max_width;
	      $URL .= '&maxheight=' . $max_height;
	      $URL .= '&key=' . $this->api_key;

	      return $URL;      
	  }
	}

	/** 
	 * The text service is different from the image service in that the image service actually accepts 
	 * the title or name of well-known entities as a value for the entity ID as well as the ID representing 
	 * the entity itself. But for the text service we can only utilize it once we’ve called the search service 
	 * which returns the entity ID that we need. The method has also a max_length parameter which is simply 
	 * used to specify the maximum length of the text that will be returned. 0 being no limit so it basically 
	 * returns everything it can return.
	 */
	public function text( $entity_id , $max_length = '0' ) {
	  if( ! empty( $entity_id ) ) {
	      $URL = 'https://www.googleapis.com/freebase/v1/text/' . $entity_id;
	      $URL .= '?maxlength=' . $max_length;
	      $URL .= '&key=' . $this->api_key;
	      $response = json_decode( file_get_contents( $URL ) , true );
	      
	      return $response['result'];       
	  }
	}

	/**
	 * Topic API. There’s actually 3 more services in the Freebase API which we haven’t gone over but I’ll leave 
	 * those for another day. The topic API just like the text service and image service requires an entity ID for 
	 * the request.
	 */
	public function topic( $entity_id ) {
	  if( ! empty( $entity_id ) ) {
	      $URL = 'https://www.googleapis.com/freebase/v1/topic' . $entity_id;
	      
	      return json_decode( file_get_contents( $URL ) , true );        
	  }
	}
}
?> 
