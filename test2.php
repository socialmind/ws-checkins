<?php
require_once('config.php');
require_once('functions.php');
include('./header.php');

$path = getAllShows();
echo $path;
$csv = readCSV($path, "title");
printCSV($csv);

?>