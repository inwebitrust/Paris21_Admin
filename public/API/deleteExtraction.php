<?php
require_once("config.php");

filter_var($_POST["source"], FILTER_SANITIZE_STRING);
filter_var($_POST["extractionID"], FILTER_SANITIZE_STRING);

$tableID = "datavalues_".$_POST["source"]."_".$_POST["extractionID"];

$queryDeleteTable = "DROP TABLE ".$tableID;
echo $queryDeleteTable;
$link->query($queryDeleteTable);

$queryDeleteExtraction = "DELETE FROM ".$DBTABLES["extractions"]." WHERE id = ".$_POST["extractionID"];
echo $queryDeleteExtraction;
$link->query($queryDeleteExtraction);
?>