<?php
require_once("config.php");

filter_var($_POST["source"], FILTER_SANITIZE_STRING);

$queryInsert = "INSERT INTO ".$DBTABLES["extractions"]." (source, date, active) VALUES ('".$_POST["source"]."', now(), 0)";
$link->query($queryInsert);
$newExtractionID = $link->insert_id;

$newTableID = "datavalues_".$_POST["source"]."_".$newExtractionID;
$queryCopyTable = "CREATE TABLE ".$newTableID." (m49 varchar(5),iso varchar(5),year varchar(20),indicator_id varchar(10),value text);";
$link->query($queryCopyTable);

$queryKey = "ALTER TABLE ".$newTableID." ADD PRIMARY KEY( `m49`, `iso`, `year`, `indicator_id`)";
$link->query($queryKey);

echo $newExtractionID;
?>