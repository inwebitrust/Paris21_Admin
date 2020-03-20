<?php
require_once("config.php");


if(isset($_FILES["fileToUpload"])) {
   $queryInsert = "INSERT INTO ".$DBTABLES["extractions"]." (source, date, active) VALUES ('csv', now(), 0)";
   $link->query($queryInsert);
   $newExtractionID = $link->insert_id;

   $newTableID = "datavalues_csv_".$newExtractionID;
   $queryCopyTable = "CREATE TABLE ".$newTableID." (m49 varchar(5),iso varchar(5),year varchar(20),indicator_id varchar(10),value varchar(150));";
   $link->query($queryCopyTable);

   function loadData()
   {
      global $newTableID, $link;

      $lines = readInputFromFile($_FILES["fileToUpload"]["tmp_name"]);
      for ($i = 1; $i < count($lines); $i++){
            $columns = explode(",", $lines[$i]);
            if(sizeof($columns) == 5) {
               $col4 = str_replace("\n","",$columns[4]);
               
               $queryInsertCSVData = "INSERT INTO ".$newTableID." VALUES ('".$columns[0]."', '".$columns[1]."', '".$columns[2]."', '".$columns[3]."', '".$col4."')";
               $link->query($queryInsertCSVData);
            }
      }

   }

   function readInputFromFile($file)
   {
      $fh = fopen($file, 'r');
      while (!feof($fh))
      {
         $ln = fgets($fh);
         $parts[] = $ln;
      }

      fclose($fh);

      return $parts;
   }

   loadData();
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
	Uploading csv...
</body>
</html>