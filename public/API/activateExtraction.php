<?php
require_once("config.php");

filter_var($_POST["source"], FILTER_SANITIZE_STRING);
filter_var($_POST["activeID"], FILTER_SANITIZE_STRING);

$queryUpdateExtractions = "UPDATE ".$DBTABLES["extractions"]." SET active = 0 WHERE source = '".$_POST["source"]."'";
echo $queryUpdateExtractions;
$link->query($queryUpdateExtractions);

$queryActivateExtraction = "UPDATE ".$DBTABLES["extractions"]." SET active = 1 WHERE id = '".$_POST["activeID"]."'";
echo $queryActivateExtraction;
$link->query($queryActivateExtraction);

?>