<?php
require_once("config.php");

filter_var($_POST["source"], FILTER_SANITIZE_STRING);
filter_var($_POST["extractionID"], FILTER_SANITIZE_STRING);

if($_POST["source"] == "csv" || $_POST["source"] == "worldbank") {
	$tableID = "datavalues_".$_POST["source"]."_".$_POST["extractionID"];
} else {
	$tableID = "indicators_".$_POST["extractionID"];
}

$queryDeleteTable = "DROP TABLE ".$tableID;
echo $queryDeleteTable;
$link->query($queryDeleteTable);

$queryDeleteExtraction = "DELETE FROM ".$DBTABLES["extractions"]." WHERE id = ".$_POST["extractionID"];
echo $queryDeleteExtraction;
$link->query($queryDeleteExtraction);
?>