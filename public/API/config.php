<?php
header('Access-Control-Allow-Origin: *');  


$selectedDB = 'paris21';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';

/*
$dbhost = '192.168.1.2';
$dbuser = 'anthony.gesec';
$dbpass = '2xzQRixT5V';
$selectedDB = 'ocde';
*/

$DBTABLES = array(
	"indicators" => "indicators_mar2020",
	"extractions" => "extractions",
	"geobase" => "geobase_2019"
)

$link = mysqli_connect($dbhost,$dbuser,$dbpass,$selectedDB) or die("Error " . mysqli_error($link));
$link->set_charset("utf8");
?>