<?php
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/functions.php');

$csv = getEpisodesList("arrow");
$path = "";
if ( isset( $_FILES["file"]["name"] ) ) {
	$path = uploadFile($_FILES);
}
if($path != "") {
	$checkins = getIMDBCheckIns($path);
}
?>

<style type="text/css" media="screen">
	table { background-color: #BBB; }
	th { background-color: #EEE; }
	td { background-color: #FFF; }
	h2 { text-align: center; } 
	table { width: 100%; }
</style>

<center><h1>Tests</h1></center>
<br>
<center><h3>Get episodes with Epguides</h3></center>

<form action="tests.php" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>
<br><br>


<center><h3>Get episodes with Epguides</h3></center>
<br>
<table border="0" cellspacing="1" cellpadding="3">
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
<br>

<?php if( isset($checkins) ) { ?>

<center><h3>Get check-ins with IMDb</h3></center> 
<br> 
<table border="0" cellspacing="1" cellpadding="3"> 	
	<tr> 		
		<?php foreach ($checkins->titles as $value): ?> 		
			<th><?php echo $value; ?></th> 		
		<?php endforeach; ?> 	
	</tr> 	
	<?php foreach ($checkins->data as $key => $row) : ?> 	
	<tr> 		
		<?php foreach ($row as $value) : ?>  		
			<td><?php echo $value; ?></td> 		
		<?php endforeach; ?> 	
	</tr> 	
	<?php endforeach; ?> 
</table> 
<br>

<?php } ?>

<center><h3>Get OMDB tests</h3></center>
<br>
<?php
$omdb = new OMDB();
	
print_r($omdb->getData("http://www.omdbapi.com/?s=Gilmore%20Girls"));
echo "<br><br>OMDB :: tt0848228<br><br>";
print_r($omdb->findByIMDB("tt0848228"));
echo "<br><br>OMDB :: 'True Grit', '1969'<br><br>";
print_r($omdb->findByNameAndYear("True Grit", "1969"));
?>
<br><br>

<center><h3>Get Freebase tests</h3></center>
<br>
<?php

$freebase = new Freebase('AIzaSyDxUIEJXpGkYKTca93JNsMndmzE88KQLPw');
$result = $freebase->search('Gilmore', 'all type:/tv/tv_program');

print_r($result);
?>