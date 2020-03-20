<?php
header('Access-Control-Allow-Origin: *');  

//PARIS 21 NEEDS TO UPDATE THIS PARAMETERES WITH THEIR SERVER CONFIG
$selectedDB = 'paris21';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';

$DBTABLES = array(
	"indicators" => "indicators_mar2020",
	"extractions" => "extractions",
	"geobase" => "geobase_2019"
);

$link = mysqli_connect($dbhost,$dbuser,$dbpass,$selectedDB) or die("Error " . mysqli_error($link));
$link->set_charset("utf8");
?>