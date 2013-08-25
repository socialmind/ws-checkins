<?php
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/functions.php');

$path = getAllShows(); 
$csv = readCSV($path, "title");
printCSV($csv, 'List of TV Series in Epguides');

?>