<?php
/**
 * A set of generic functions for ws-checking.
 *
 * Includes the following functionality: 
 * 	01. File handling
 * 	02. Formatting
 * 	03. IMDb related
 * 	04. Epguides related
 * 	05. OMDB related
 */
 
require_once('lib/db.php');
require_once('lib/imdb.php');
require_once('lib/freebase.php');
require_once('lib/omdb.php');
require_once('lib/epguides.php');
require_once('lib/parsecsv.lib.php');

// 01. FILE HANDLING //

/**
 *
 */
function uploadFile() {
	$temp = explode(".", $_FILES["file"]["name"]);
	if( end($temp) == "csv" ) {
		if ($_FILES["file"]["error"] > 0) {
			echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
		}
		else {
			if ( file_exists( IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"] ) ) {
				echo $_FILES["file"]["name"] . " already exists. ";
			}
			else {
				move_uploaded_file($_FILES["file"]["tmp_name"], IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"]);
				return IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"];
			}
		}
	} else {
		echo "The file you uploaded must be a .csv file!";
	}
}

/**
 *
 */
function downloadFile($url, $path) {

  $newfname = $path;
  $file = fopen ($url, "rb");
  if ($file) {
    $newf = fopen ($newfname, "wb");

    if ($newf)
    while(!feof($file)) {
      fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
    }
  }

  if ($file) {
    fclose($file);
  }

  if ($newf) {
    fclose($newf);
  }
 }



/**
 * 
 */
function writeToFile($path, $data) {
	file_put_contents($path, $data, FILE_WRITE | LOCK_EX);
}

/** 
 * Checks if the CSV file is valid.
 * @return bool
 */
function isValid($path) {
	$csv = new parseCSV();
	$csv->auto($path);
	if(sizeof($csv->titles) > 1) {
		return true;
	} else {
		return false;
	}
}

/**
 *
 */
function readCSV($path, $sortBy = '', $reverse = false) {
	$csv = new parseCSV();
	if($sortBy != '') {
		$csv->sort_by = $sortBy;
	}
	$csv->sort_reverse = $reverse;
	$csv->auto($path);
	
	if(isValid($path)) { //Checks if the user exists and has a valid checkin history CSV
		return $csv;
	} else {
		unlink($path);
		die("invalid csv file!");
	}
}

// 02. FORMATTING //

/**
 * Generic function for printing the content of a .csv file
 */
function printCSV($csv) {
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
			CSV File Content!
		</h2>
		<tr>
			<?php foreach ($csv->titles as $value): ?>
			<th><?php echo $value; ?></th>
			<?php endforeach; ?>
		</tr>
		<?php foreach ($csv->data as $key => $row) : ?>
		<tr>
			<?php foreach ($row as $value) : ?> 
			<td><?php echo $value; ?></td>
			<?php endforeach; ?>
		</tr>
		<?php endforeach; ?>
	</table>
<?php
}

// 03. IMDb RELATED //

/**
 *
 */
function getIMDBCheckIns($path) {
	$csv = readCSV($path);
	//To have stats
	$imdb = new imdbCheckInTracker($csv);
	$imdb->analyze();
	return $imdb->getCSV();
}

// 04. EPGUIDES RELATED //

/**
 *
 */
function getEpisodesList($name) {
	$epguides = new Epguides($name);
	$url = $epguides ->getEpisodesCSVUrl();
	$path = DATA_DIR . "/". $epguides ->getId() . ".csv";	
	writeToFile( $path, $epguides ->getEpisodesCSV($url) );
	
	return readCSV($path);
}

/**
 *
 */
function getAllShows() {
	$path = DATA_DIR. "/" . "epguides_all.csv";
	file_put_contents($path, fopen("http://epguides.com/common/allshows.txt", 'r'));
	
	return $path;
}

// 05. OMDB RELATED //


?>