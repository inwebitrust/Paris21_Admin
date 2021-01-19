<?php
require_once("config.php");
ini_set('memory_limit', '512M');
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');


if(isset($_FILES["fileToUpload"])) {
   $queryInsert = "INSERT INTO ".$DBTABLES["extractions"]." (source, date, active) VALUES ('csv', now(), 0)";
   $link->query($queryInsert);
   $newExtractionID = $link->insert_id;

   var_dump($_FILES);

   $newTableID = "datavalues_csv_".$newExtractionID;
   $queryCopyTable = "CREATE TABLE ".$newTableID." (m49 varchar(5),iso varchar(5),year varchar(20),indicator_id varchar(10),value varchar(150));";
   echo $queryCopyTable;
   $link->query($queryCopyTable);

   function loadData()
   {
      global $newTableID, $link;

      $lines = readInputFromFile($_FILES["fileToUpload"]["tmp_name"]);
      for ($i = 1; $i < count($lines); $i++){
            echo $i;
            $columns = explode(",", $lines[$i]);
            if(sizeof($columns) == 5) {
               $col4 = str_replace("\n","",$columns[4]);
               if($columns[0] == "FRAGILE") $columns[0] = "9998";
               else if($columns[0] == "LDC") $columns[0] = "9994";
               else if($columns[0] == "SIDS") $columns[0] = "9997";
               else if($columns[0] == "LANDLOCKED") $columns[0] = "9995";
               else if($columns[0] == "Low income") $columns[0] = "9993";
               else if($columns[0] == "Lower middle income") $columns[0] = "9992";
               else if($columns[0] == "Upper middle income") $columns[0] = "9999";
               else if($columns[0] == "High income") $columns[0] = "9996";

               if($columns[1] == "FRAGILE") $columns[1] = "9998";
               else if($columns[1] == "LDC") $columns[1] = "9994";
               else if($columns[1] == "SIDS") $columns[1] = "9997";
               else if($columns[1] == "LANDLOCKED") $columns[1] = "9995";
               else if($columns[1] == "Low income") $columns[1] = "9993";
               else if($columns[1] == "Lower middle income") $columns[1] = "9992";
               else if($columns[1] == "Upper middle income") $columns[1] = "9999";
               else if($columns[1] == "High income") $columns[1] = "9996";

               $queryInsertCSVData = "INSERT INTO ".$newTableID." VALUES ('".$columns[0]."', '".$columns[1]."', '".$columns[2]."', '".$columns[3]."', '".$col4."')";
               echo $queryInsertCSVData;
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