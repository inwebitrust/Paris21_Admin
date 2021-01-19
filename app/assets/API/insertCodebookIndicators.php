<?php
require_once("config.php");
ini_set('memory_limit', '512M');
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');


if(isset($_FILES["fileToUpload"])) {
   $queryInsert = "INSERT INTO ".$DBTABLES["extractions"]." (source, date, active) VALUES ('indicators', now(), 0)";
   $link->query($queryInsert);
   $newExtractionID = $link->insert_id;

   $newTableID = "indicators_".$newExtractionID;
   $queryCopyTable = "CREATE TABLE ".$newTableID." ( id int(5) NOT NULL, name varchar(255) NOT NULL, dataviz_type varchar(255) NOT NULL, direction tinyint(1) NOT NULL DEFAULT '0', key_indicator tinyint(1) NOT NULL DEFAULT '0', family varchar(255) NOT NULL, classif varchar(255) NOT NULL, area varchar(255) NOT NULL, level varchar(255) NOT NULL, final_list tinyint(1) NOT NULL DEFAULT '0', note text NOT NULL, source text NOT NULL, definition text NOT NULL, short_def text NOT NULL, tag varchar(255) NOT NULL, timeseries int(1) NOT NULL, default_cpage varchar(3) NOT NULL, dissem_agency varchar(255) NOT NULL, methodo text NOT NULL, methodo_link text NOT NULL, datasource varchar(100) NOT NULL, datasource_id varchar(100) NOT NULL);";
   $link->query($queryCopyTable);

   function loadData()
   {
      global $newTableID, $link;

      $lines = readInputFromFile($_FILES["fileToUpload"]["tmp_name"]);
      for ($i = 1; $i < count($lines); $i++){
            $columns = explode(";", $lines[$i]);
            echo "id : ".$columns[0]." ".$columns[1]." ".sizeof($columns). "<br />";
            if(sizeof($columns) == 22) {

               $col1 = str_replace("'", "\'", $columns[1]);
               $col5 = str_replace("'", "\'", $columns[5]);
               $col6 = str_replace("'", "\'", $columns[6]);
               $col7 = str_replace("'", "\'", $columns[7]);
               $col8 = str_replace("'", "\'", $columns[8]);
               $col10 = str_replace("'", "\'", $columns[10]);
               $col11 = str_replace("'", "\'", $columns[11]);
               $col12 = str_replace("'", "\'", $columns[12]);
               $col13 = str_replace("'", "\'", $columns[13]);
               $col17 = str_replace("'", "\'", $columns[17]);
               $col18 = str_replace("'", "\'", $columns[18]);
               $col19 = str_replace("'", "\'", $columns[19]);

               $col1 = str_replace('"', '\"', $col1);
               $col5 = str_replace('"', '\"', $col5);
               $col6 = str_replace('"', '\"', $col6);
               $col7 = str_replace('"', '\"', $col7);
               $col8 = str_replace('"', '\"', $col8);
               $col10 = str_replace('"', '\"', $col10);
               $col11 = str_replace('"', '\"', $col11);
               $col12 = str_replace('"', '\"', $col12);
               $col13 = str_replace('"', '\"', $col13);
               $col17 = str_replace('"', '\"', $col17);
               $col18 = str_replace('"', '\"', $col18);
               $col19 = str_replace('"', '\"', $col19);

               $queryInsertIndicatorsData = "INSERT INTO ".$newTableID." VALUES (".$columns[0].", '".$col1."', '".$columns[2]."', ".$columns[3].", ".$columns[4].", '".$col5."', '".$col6."', '".$col7."', '".$col8."', ".$columns[9].", '".$col10."', '".$col11."', '".$col12."', '".$col13."', '".$columns[14]."', ".$columns[15].", '".$columns[16]."', '".$col17."', '".$col18."', '".$col19."', '".$columns[20]."', '".$columns[21]."')";
               echo $queryInsertIndicatorsData."<br /><br />";
               $link->query($queryInsertIndicatorsData);
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