<?php
require_once("config.php");

filter_var($_POST["sqltable"], FILTER_SANITIZE_STRING);
filter_var($_POST["objToInsert"], FILTER_SANITIZE_STRING);

$queryInsert = "INSERT INTO ".$_POST["sqltable"]." (m49, iso, year, indicator_id, value) VALUES ('".$_POST["objToInsert"]["m49"]."', '".$_POST["objToInsert"]["iso"]."', '".$_POST["objToInsert"]["year"]."', '".$_POST["objToInsert"]["indicator_id"]."', '".$_POST["objToInsert"]["value"]."')";
echo $queryInsert;
$link->query($queryInsert);
?>