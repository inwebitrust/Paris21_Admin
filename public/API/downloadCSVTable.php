<?php
require_once("config.php");

filter_var($_GET["source"], FILTER_SANITIZE_STRING);
filter_var($_GET["id"], FILTER_SANITIZE_STRING);

$DBTable = "datavalues_".$_GET["source"]."_".$_GET["id"];
$csv_filename = 'db_export_'.date('Y-m-d').'.csv';

$csv_export = 'm49,iso,year,indicator_id,value';
$csv_export.= "\n";

// query to get data from database
$queryTable = "SELECT * FROM ".$DBTable or die("Error in the consult.." . mysqli_error($link));
$resultRows = $link->query($queryTable);


while($row = mysqli_fetch_array($resultRows)) {
    foreach ($row as $key => $value) {
    	if($key == "0" || $key == "1" || $key == "2" || $key == "3") {
    		$csv_export.= '"'.$value.'",';
    	} else if($key == "4") {
    		$csv_export.= '"'.$value.'"';
    	}
    }
    $csv_export.= "\n";

}

// Export the data and prompt a csv file for download
header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");
echo($csv_export);

?>