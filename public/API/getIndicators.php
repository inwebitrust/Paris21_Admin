<?php
require_once("config.php");

$INDICATORS = array();

$INDICATORSCODEBOOKDB = "indicators_mar2020";
$queryExtractions = "SELECT * FROM ".$DBTABLES["extractions"]." WHERE active = 1" or die("Error in the consult.." . mysqli_error($link));
$resultExtractions = $link->query($queryExtractions);
while($row = mysqli_fetch_assoc($resultExtractions)) {
    if($row["source"] == "indicators") {
    	$INDICATORSCODEBOOKDB = "indicators_".$row["id"];
    }
}

$queryIndicators = "SELECT * FROM ".$INDICATORSCODEBOOKDB." WHERE final_list = 1" or die("Error in the consult.." . mysqli_error($link));
$resultIndicators = $link->query($queryIndicators);
while($row = mysqli_fetch_assoc($resultIndicators)) {
    array_push($INDICATORS, $row);
}

$RESULTS = array(
    "indicators" => $INDICATORS
);

echo json_encode($RESULTS);

?>