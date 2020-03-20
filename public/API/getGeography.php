<?php
require_once("config.php");

$GEOBASE = array();

$queryGeobase = "SELECT * FROM ".$DBTABLES["geobase"]."" or die("Error in the consult.." . mysqli_error($link));
$resultGeobase = $link->query($queryGeobase);
while($row = mysqli_fetch_assoc($resultGeobase)) {
    array_push($GEOBASE, $row);
}

$RESULTS = array(
    "geography" => $GEOBASE
);

echo json_encode($RESULTS);

?>