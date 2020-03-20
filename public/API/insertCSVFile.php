<?php
require_once("config.php");
ini_set('memory_limit', '512M');
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');


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
               
               if($columns[0] == "FRAGILE") $value = "9998";
               else if($columns[0] == "LDC") $value = "9994";
               else if($columns[0] == "SIDS") $value = "9997";
               else if($columns[0] == "LANDLOCKED") $value = "9995";
               else if($columns[0] == "Low income") $value = "9993";
               else if($columns[0] == "Lower middle income") $value = "9992";
               else if($columns[0] == "Upper middle income") $value = "9999";
               else if($columns[0] == "High income") $value = "9996";

               if($columns[1] == "FRAGILE") $value = "9998";
               else if($columns[1] == "LDC") $value = "9994";
               else if($columns[1] == "SIDS") $value = "9997";
               else if($columns[1] == "LANDLOCKED") $value = "9995";
               else if($columns[1] == "Low income") $value = "9993";
               else if($columns[1] == "Lower middle income") $value = "9992";
               else if($columns[1] == "Upper middle income") $value = "9999";
               else if($columns[1] == "High income") $value = "9996";

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