<?php
/**
 * A set of generic functions.
 *
 * Includes the following functionality:
 * 
 * 	01. File handling
 * 	02. Formatting
 * 	03. IMDb related
 * 	04. Epguides related
 * 	05. OMDB related
 */
require_once('lib/imdb.php');
require_once('lib/freebase.php');
require_once('lib/omdb.php');
require_once('lib/epguides.php');
require_once('lib/parsecsv.lib.php');

// 01. File Handling //

/**
 * 
 */
function upload_file( ) {
	$temp = explode( "." , $_FILES["file"]["name"] );
	if ( end( $temp ) == "csv" ) {
		if ( $_FILES["file"]["error"] > 0 ) {
			echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
		}
		else {
			if ( file_exists( IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"] ) ) {
				echo $_FILES["file"]["name"] . " already exists. ";
			}
			else {
				move_uploaded_file( $_FILES["file"]["tmp_name"] , IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"] );
				
				return IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"];
			}
		}
	} else {
		echo "The file you uploaded must be in CSV format.";
	}
}

/**
 *
 */
function download_file( $URL , $path ) {
	$newfname = $path;
	$file = fopen ( $URL , "rb" );
	if ( $file ) {
		$newf = fopen ( $newfname , "wb" );
		if ( $newf )
		    while( ! feof( $file ) ) 
		    	fwrite( $newf , fread( $file , 1024 * 8 ) , 1024 * 8 );
	}
	if ( $file ) 
		fclose( $file );
	
	if ( $newf )
		fclose($newf);
}

/**
 * 
 */
function write_to_file( $path , $data ) {
	file_put_contents( $path , $data , 'FILE_WRITE' | LOCK_EX );
}

/** 
 * Checks if the CSV file is valid.
 * @return bool
 */
function is_valid( $path ) {
	$csv = new parseCSV( );
	$csv->auto( $path );
	if ( sizeof( $csv->titles ) > 1 )
		return true;
	else
		return false;
}

/**
 *
 */
function read_CSV( $path , $sort_by = '' , $reverse = false ) {
	$csv = new parseCSV();
	if( $sort_by != '' ) {
		$csv->sort_by = $sort_by;
	}
	$csv->sort_reverse = $reverse;
	$csv->auto( $path );
	if( is_valid( $path ) ) { //Checks if the user exists and has a valid checkin history CSV
		return $csv;
	} else {
		unlink( $path );
		die("invalid csv file!");
	}
}

// 02. FORMATTING //

/**
 * Generic function for printing the content of a .csv file
 */
function print_CSV( $csv , $title ) {
?>	
	<style type="text/css" media="screen">
		table { background-color: #BBB; }
		th { background-color: #EEE; }
		td { background-color: #FFF; }
		h2 { text-align: center; }
		table { width: 100%; }
	</style>
	<table border="0" cellspacing="1" cellpadding="3">
		<h2>
			<? echo $title; ?>
		</h2>
		<tr>
			<?php foreach ( $csv->titles as $value ) : ?>
			<th><?php echo $value; ?></th>
			<?php endforeach ; ?>
		</tr>
		<?php foreach ( $csv->data as $key => $row ) : ?>
		<tr>
			<?php foreach ( $row as $value ) : ?> 
			<td><?php echo $value; ?></td>
			<?php endforeach ; ?>
		</tr>
		<?php endforeach ; ?>
	</table>
<?php
}

// 03. IMDb RELATED //

/**
 *
 */
function get_IMDB_check_ins( $path ) {
	$csv = read_CSV( $path );
	//To have stats
	$imdb = new IMDB_Check_Ins( $csv );
	$imdb->analyze( );
	
	return $imdb->get_CSV( );
}

// 04. EPGUIDES RELATED //

/**
 *
 */
function get_episodes_list( $name ) {
	$epguides = new Epguides( $name );
	$URL = $epguides ->get_episodes_CSV_URL( );
	$path = DATA_DIR . "/". $epguides->get_ID( ) . ".csv";	
	write_to_file( $path, $epguides->get_episodes_CSV( $URL ) );
	
	return read_CSV( $path );
}

/**
 *
 */
function get_all_shows( ) {
	$path = DATA_DIR. "/" . "epguides_all.csv";
	file_put_contents( $path , fopen( "http://epguides.com/common/allshows.txt" , 'r' ) );
	
	return $path;
}

// 05. OMDB RELATED //


?>