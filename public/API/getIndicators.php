<?php
require_once("config.php");

$INDICATORS = array();

$queryIndicators = "SELECT * FROM ".$DBTABLES["indicators"]." WHERE final_list = 1" or die("Error in the consult.." . mysqli_error($link));
$resultIndicators = $link->query($queryIndicators);
while($row = mysqli_fetch_assoc($resultIndicators)) {
    array_push($INDICATORS, $row);
}

$RESULTS = array(
    "indicators" => $INDICATORS
);

echo json_encode($RESULTS);

?>