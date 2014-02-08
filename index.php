<?php
/** 
 * This is a test file. Summarizes all the functionality of the project
 * up to now and helps the intented developer to quickly understand how
 * stuff work around here :)
 * 
 * If you are working on ws-checkings please remember to acompany every
 * major contribution with an appropriate test sample.
 */
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/functions.php');

$csv = get_episodes_list("arrow");
$path = "";
$checkind = "";

if ( isset( $_FILES["file"]["name"] ) ) {

	$path = IMDB_CHECKINS_DIR . "/" . $_FILES["file"]["name"];

	if ( file_exists( $path ) ) {
		$checkins = get_IMDB_check_ins( $path );
	} else {
		$path = upload_file( $_FILES );
		if ( $path != "" ) {
			$checkins = get_IMDB_check_ins( $path );
		}
	}
}
?>

<style type="text/css" media="screen">
	table { background-color: #BBB; }
	th { background-color: #EEE; }
	td { background-color: #FFF; }
	h2 { text-align: center; } 
	table { width: 100%; }
</style>

<center>
	<h1>WS Check Ins Test Page</h1>
	<p style="width: 60%"><em>This is a test page that summarizes all the functionality of the project as it evolves and 
		helps the intented developer to quickly understand how stuff work around here :)</em></p>
</center>
<br>
<center>
	<h3>Get IMDb check-ins from your public CSV file</h3>
	<p style="width: 60%;"><em>In order to test the IMDb check-ins functionality you need to visit IMDb, go to your account
		page, visit the check-in section and download your personalized CHECKINS.csv file in the end of the
		page. (<u>IMPORTANT NOTE</u>: Please consult IMDb's <a href="http://www.imdb.com/help/show_article?conditions" target="top">Conditions of use</a>
		before you proceed. Websthetics is not to be held liable in ant way by the actions of individuals that use ws-checkins)</em></p>
</center>

<form action="./index.php" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>
<br><br>

<?php if ( isset( $checkins ) && $checkins !== "" ) { ?>

<center><h3>Get check-ins with IMDb</h3></center> 
<br> 
<table border="0" cellspacing="1" cellpadding="3"> 	
	<tr> 		
		<?php foreach ( $checkins->titles as $value ) : ?> 		
			<th><?php echo $value; ?></th> 		
		<?php endforeach ; ?> 	
	</tr> 	
	<?php foreach ( $checkins->data as $key => $row ) : ?> 	
	<tr> 		
		<?php foreach ( $row as $value ) : ?>  		
			<td><?php echo $value; ?></td> 		
		<?php endforeach ; ?> 	
	</tr> 	
	<?php endforeach ; ?> 
</table> 
<br>
<?php } ?>

<?php if ( isset( $csv ) && $csv !== "" ) { ?>

<center><h3>Get episodes with Epguides</h3></center>
<br>
<table border="0" cellspacing="1" cellpadding="3">
	<tr>
		<?php foreach ( $csv->titles as $value ) : ?>
		<th><?php echo $value; ?></th>
		<?php endforeach ; ?>
	</tr>
	<?php foreach ( $csv->data as $key => $row ) : ?>
	<tr>
		<?php foreach ($row as $value ) : ?> 
		<td><?php echo $value; ?></td>
		<?php endforeach ; ?>
	</tr>
	<?php endforeach ; ?>
</table>
<br>
<?php } ?>

<center><h3>Get OMDB tests</h3></center>
<br>
<?php
$omdb = new OMDB();
	
print_r( $omdb->get_data( "http://www.omdbapi.com/?s=Gilmore%20Girls" ) );
echo "<br><br>OMDB :: tt0848228<br><br>";
print_r( $omdb->find_by_IMDB_ID( "tt0848228" ) );
echo "<br><br>OMDB :: 'True Grit', '1969'<br><br>";
print_r( $omdb->find_by_name_and_year( "True Grit" , "1969" ) );
?>
<br><br>

<center><h3>Get Freebase tests</h3></center>
<br>
<?php

$freebase = new Freebase( FREEBASE_API_KEY );
$result = $freebase->search( 'Gilmore' , 'all type:/tv/tv_program' );
var_dump( $result );

?>
<br><br>

<center><h3>Get All Epguides available series test</h3></center>
<br>
<?php

$path = get_all_shows( ); 
$csv = read_CSV( $path , "title" );
print_CSV( $csv , 'List of TV Series in Epguides' );

?>