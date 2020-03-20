<?php
require_once("config.php");

filter_var($_POST["sqltable"], FILTER_SANITIZE_STRING);

foreach ($_POST["allObjsToInsert"] as $key => $obj) {
	$queryInsert = "INSERT INTO ".$_POST["sqltable"]." (m49, iso, year, indicator_id, value) VALUES ('".$obj["m49"]."', '".$obj["iso"]."', '".$obj["year"]."', '".$obj["indicator_id"]."', '".$obj["value"]."')";
	echo $queryInsert;
	$link->query($queryInsert);
}
?>


