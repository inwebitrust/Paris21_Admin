<?php
require_once("config.php");
ini_set('memory_limit', '512M');

$fp = fopen('file.csv', 'w');

//GATHER CSV AND WORLDBANK DATABASE
$CSVDATABASE = "";
$WORLBANKDATABASE = "";

$queryExtractions = "SELECT * FROM ".$DBTABLES["extractions"]." WHERE active = 1" or die("Error in the consult.." . mysqli_error($link));
$resultExtractions = $link->query($queryExtractions);
while($row = mysqli_fetch_assoc($resultExtractions)) {
    if($row["source"] == "csv") {
    	$CSVDATABASE = "datavalues_csv_".$row["id"];
    } else if($row["source"] == "worldbank") {
    	$WORLBANKDATABASE = "datavalues_worldbank_".$row["id"];
    }
}

//END GATHER CSV AND WORLDBANK DATABASE

echo $CSVDATABASE;
echo $WORLBANKDATABASE;

$DATAVALUES = array( array("m49", "iso", "year", "indicator_id", "value") );

//GATHER CSV VALUES
$queryCSV = "SELECT * FROM ".$CSVDATABASE or die("Error in the consult.." . mysqli_error($link));
$resultCSV = $link->query($queryCSV);
while($row = mysqli_fetch_row($resultCSV)) {
	//var_dump($row);
    array_push($DATAVALUES, $row);
}
//END GATHER CSV

//GATHER WB VALUES
$queryWB = "SELECT * FROM ".$WORLBANKDATABASE or die("Error in the consult.." . mysqli_error($link));
$resultWB = $link->query($queryWB);
while($row = mysqli_fetch_row($resultWB)) {
    array_push($DATAVALUES, $row);
}
//END GATHER WB

foreach ($DATAVALUES as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

?>