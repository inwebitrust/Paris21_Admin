<?php
require_once("config.php");

$EXTRACTIONS = array();

$queryExtractions = "SELECT * FROM ".$DBTABLES["extractions"]." ORDER BY date DESC" or die("Error in the consult.." . mysqli_error($link));
$resultExtractions = $link->query($queryExtractions);
while($row = mysqli_fetch_assoc($resultExtractions)) {
    array_push($EXTRACTIONS, $row);
}

$RESULTS = array(
    "extractions" => $EXTRACTIONS
);

echo json_encode($RESULTS);

?>