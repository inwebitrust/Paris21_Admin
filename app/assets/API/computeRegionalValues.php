<?php
require_once("config.php");

//GATHER CSV AND WORLDBANK DATABASE
$CSVDATABASE = "";
$WORLBANKDATABASE = "";
$INDICATORSCODEBOOKDB = "indicators_mar2020";

$queryExtractions = "SELECT * FROM ".$DBTABLES["extractions"]." WHERE active = 1" or die("Error in the consult.." . mysqli_error($link));
$resultExtractions = $link->query($queryExtractions);
while($row = mysqli_fetch_assoc($resultExtractions)) {
    if($row["source"] == "csv") {
    	$CSVDATABASE = "datavalues_csv_".$row["id"];
    } else if($row["source"] == "worldbank") {
    	$WORLBANKDATABASE = "datavalues_worldbank_".$row["id"];
    } else if($row["source"] == "indicators") {
    	$INDICATORSCODEBOOKDB = "indicators_".$row["id"];
    }
}

//END GATHER CSV AND WORLDBANK DATABASE


//GATHER COUNTRIES REGIONS AND SUBREGIONS
$COUNTRIES = new stdClass();
$queryCountries = "SELECT m49, iso, region_code, subregion_code, ldc, lldc, sids, fragile, income FROM ".$DBTABLES["geobase"]." WHERE country <> ''" or die("Error in the consult.." . mysqli_error($link));
$resultCountries = $link->query($queryCountries);
while($row = mysqli_fetch_assoc($resultCountries)) {
	if($row["m49"] !== ""){
		$COUNTRIES->{$row["m49"]} = $row;
	}
}
//END GATHER COUNTRIES REGIONS AND SUBREGIONS


//GATHER INDICATORS
$INDICATORS = array();
$queryIndicators = "SELECT id, dataviz_type, datasource FROM ".$INDICATORSCODEBOOKDB." WHERE final_list = 1" or die("Error in the consult.." . mysqli_error($link));
$resultIndicators = $link->query($queryIndicators);
while($row = mysqli_fetch_assoc($resultIndicators)) {
    array_push($INDICATORS, $row);
}
//END GATHER INDICATORS


// LOOP INDICATORS

foreach ($INDICATORS as $key => $indicator) {

	$INDICATORDATABASE = $CSVDATABASE;
	if($indicator["datasource"] == "worldbank") {
		$INDICATORDATABASE = $WORLBANKDATABASE;
	}
	$indicatorRegionalValues = new stdClass();
	$worldCode = "1";
	$indicatorRegionalValues->{$worldCode} = new stdClass();

	//TEST IF ORDINAL TYPE WITH DISCRETE VALUES
	if($indicator["dataviz_type"] == "ordinal") {
		//echo "is ordinal <br />";
		$queryIndicatorData = "SELECT * FROM ".$INDICATORDATABASE." WHERE indicator_id = ".$indicator["id"];
		
		//LOOP ON INDICATOR VALUES AND APPEND VALUES TO $indicatorRegionalValues OBJ
		$resultIndicatorData = $link->query($queryIndicatorData);
		while($row = mysqli_fetch_assoc($resultIndicatorData)) {
			$indicatorValue = $row["value"];
			if($row["value"] == "") $indicatorValue = "0";

			if(isset($COUNTRIES->{$row["m49"]})) {
				$countryRegion = $COUNTRIES->{$row["m49"]}["region_code"];
				$countrySubregion = $COUNTRIES->{$row["m49"]}["subregion_code"];
				$countryLDC = $COUNTRIES->{$row["m49"]}["ldc"];
				$countryLLDC = $COUNTRIES->{$row["m49"]}["lldc"];
				$countrySIDS = $COUNTRIES->{$row["m49"]}["sids"];
				$countryFragile = $COUNTRIES->{$row["m49"]}["fragile"];
				$countryIncome = $COUNTRIES->{$row["m49"]}["income"];
				
				//Specific World
				if(!isset($indicatorRegionalValues->{$worldCode}->{$row["year"]})){
					$indicatorRegionalValues->{$worldCode}->{$row["year"]} = new stdClass();
				}
				if(!isset($indicatorRegionalValues->{$worldCode}->{$row["year"]}->{$indicatorValue})){
					$indicatorRegionalValues->{$worldCode}->{$row["year"]}->{$indicatorValue} = array(
						"value" => $indicatorValue,
						"nb" => 0
					);
				}
				$indicatorRegionalValues->{$worldCode}->{$row["year"]}->{$indicatorValue}["nb"] += 1;

				if($countryRegion !== "") {
					$countryRegion = $COUNTRIES->{$row["m49"]}["region_code"];
					$countrySubregion = $COUNTRIES->{$row["m49"]}["subregion_code"];
					if(!isset($indicatorRegionalValues->{$countryRegion})){
						$indicatorRegionalValues->{$countryRegion} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{$countryRegion}->{$row["year"]})){
						$indicatorRegionalValues->{$countryRegion}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{$countryRegion}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{$countryRegion}->{$row["year"]}->{$indicatorValue} = array(
							"value" => $indicatorValue,
							"nb" => 0
						);
					}
					$indicatorRegionalValues->{$countryRegion}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}

				if($countrySubregion !== "") {
					if(!isset($indicatorRegionalValues->{$countrySubregion})){
						$indicatorRegionalValues->{$countrySubregion} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{$countrySubregion}->{$row["year"]})){
						$indicatorRegionalValues->{$countrySubregion}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{$countrySubregion}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{$countrySubregion}->{$row["year"]}->{$indicatorValue} = array(
							"value" => $indicatorValue,
							"nb" => 0
						);
					}
					$indicatorRegionalValues->{$countrySubregion}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}

				if($countryIncome !== "") {
					if(!isset($indicatorRegionalValues->{$countryIncome})){
						$indicatorRegionalValues->{$countryIncome} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{$countryIncome}->{$row["year"]})){
						$indicatorRegionalValues->{$countryIncome}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{$countryIncome}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{$countryIncome}->{$row["year"]}->{$indicatorValue} = array("value" => $indicatorValue, "nb" => 0);
					}
					$indicatorRegionalValues->{$countryIncome}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}

				if($countryLDC == "1") {
					if(!isset($indicatorRegionalValues->{"9994"})){
						$indicatorRegionalValues->{"9994"} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9994"}->{$row["year"]})){
						$indicatorRegionalValues->{"9994"}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9994"}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{"9994"}->{$row["year"]}->{$indicatorValue} = array("value" => $indicatorValue, "nb" => 0);
					}
					$indicatorRegionalValues->{"9994"}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}

				if($countryLLDC == "1") {
					if(!isset($indicatorRegionalValues->{"9995"})){
						$indicatorRegionalValues->{"9995"} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9995"}->{$row["year"]})){
						$indicatorRegionalValues->{"9995"}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9995"}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{"9995"}->{$row["year"]}->{$indicatorValue} = array("value" => $indicatorValue, "nb" => 0);
					}
					$indicatorRegionalValues->{"9995"}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}

				if($countrySIDS == "1") {
					if(!isset($indicatorRegionalValues->{"9997"})){
						$indicatorRegionalValues->{"9997"} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9997"}->{$row["year"]})){
						$indicatorRegionalValues->{"9997"}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9997"}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{"9997"}->{$row["year"]}->{$indicatorValue} = array("value" => $indicatorValue, "nb" => 0);
					}
					$indicatorRegionalValues->{"9997"}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}

				if($countryFragile == "1") {
					if(!isset($indicatorRegionalValues->{"9998"})){
						$indicatorRegionalValues->{"9998"} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9998"}->{$row["year"]})){
						$indicatorRegionalValues->{"9998"}->{$row["year"]} = new stdClass();
					}
					if(!isset($indicatorRegionalValues->{"9998"}->{$row["year"]}->{$indicatorValue})){
						$indicatorRegionalValues->{"9998"}->{$row["year"]}->{$indicatorValue} = array("value" => $indicatorValue, "nb" => 0);
					}
					$indicatorRegionalValues->{"9998"}->{$row["year"]}->{$indicatorValue}["nb"] += 1;
				}
			}
		}

		//LOOP ON $indicatorRegionalValues AND INSERT or UPDATE values for region
		//region => year => indicator => values
		foreach ($indicatorRegionalValues as $regionM49 => $regionData) {
			foreach ($regionData as $year => $yearData) {
				$selectYearData = "SELECT m49 FROM ".$INDICATORDATABASE." WHERE m49 = '".$regionM49."' AND year = '".$year."' AND indicator_id = '".$indicator["id"]."'";
				$resultYearData = $link->query($selectYearData);

				$updateType = "insert";
				while($row = mysqli_fetch_assoc($resultYearData)) {
					$updateType = "update";
				}

				if($updateType == "insert") {
					$sqlInsertRegion = "INSERT INTO ".$INDICATORDATABASE." VALUES ( '".$regionM49."', '', '".$year."', '".$indicator["id"]."', '".json_encode($yearData)."')";
					//echo $sqlInsertRegion."<br />";
					$link->query($sqlInsertRegion);
				} else {
					$sqlUpdateRegion = "UPDATE ".$INDICATORDATABASE." SET value = '".json_encode($yearData)."' WHERE m49 = '".$regionM49."' AND year = '".$year."' AND indicator_id = '".$indicator["id"]."'";
					//echo $sqlUpdateRegion."<br />";
					$link->query($sqlUpdateRegion);
				}
				
			}
		}
		
	} else {

		if($indicator["datasource"] == "worldbank") {

			//echo "is non-ordinal worldbank : <br />";
			$queryIndicatorData = "SELECT * FROM ".$INDICATORDATABASE." WHERE indicator_id = ".$indicator["id"];
			$resultIndicatorData = $link->query($queryIndicatorData);

			while($row = mysqli_fetch_assoc($resultIndicatorData)) {
				if($row["value"] != ""){
					$indicatorValue = floatval($row["value"]);
					if(isset($COUNTRIES->{$row["m49"]})) {
						$countryRegion = $COUNTRIES->{$row["m49"]}["region_code"];
						$countrySubregion = $COUNTRIES->{$row["m49"]}["subregion_code"];
						$countryLDC = $COUNTRIES->{$row["m49"]}["ldc"];
						$countryLLDC = $COUNTRIES->{$row["m49"]}["lldc"];
						$countrySIDS = $COUNTRIES->{$row["m49"]}["sids"];
						$countryFragile = $COUNTRIES->{$row["m49"]}["fragile"];
						$countryIncome = $COUNTRIES->{$row["m49"]}["income"];

						//Specific World
						if(!isset($indicatorRegionalValues->{$worldCode}->{$row["year"]})){
							$indicatorRegionalValues->{$worldCode}->{$row["year"]} = array(
								"value" => 0,
								"nb" => 0
							);
						}
						$indicatorRegionalValues->{$worldCode}->{$row["year"]}["value"] += $indicatorValue;
						$indicatorRegionalValues->{$worldCode}->{$row["year"]}["nb"] += 1;

						if($countryRegion !== "") {
							if(!isset($indicatorRegionalValues->{$countryRegion})){
								$indicatorRegionalValues->{$countryRegion} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{$countryRegion}->{$row["year"]})){
								$indicatorRegionalValues->{$countryRegion}->{$row["year"]} = array(
									"value" => 0,
									"nb" => 0
								);
							}

							$indicatorRegionalValues->{$countryRegion}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{$countryRegion}->{$row["year"]}["nb"] += 1;
						}

						if($countrySubregion !== "") {
							if(!isset($indicatorRegionalValues->{$countrySubregion})){
								$indicatorRegionalValues->{$countrySubregion} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{$countrySubregion}->{$row["year"]})){
								$indicatorRegionalValues->{$countrySubregion}->{$row["year"]} = array(
									"value" => 0,
									"nb" => 0
								);
							}
							
							$indicatorRegionalValues->{$countrySubregion}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{$countrySubregion}->{$row["year"]}["nb"] += 1;
						}

						if($countryIncome !== "") {
							if(!isset($indicatorRegionalValues->{$countryIncome})){
								$indicatorRegionalValues->{$countryIncome} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{$countryIncome}->{$row["year"]})){
								$indicatorRegionalValues->{$countryIncome}->{$row["year"]} = array(
									"value" => 0,
									"nb" => 0
								);
							}
							$indicatorRegionalValues->{$countryIncome}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{$countryIncome}->{$row["year"]}["nb"] += 1;
						}

						if($countryLDC == "1") {
							if(!isset($indicatorRegionalValues->{"9994"})){
								$indicatorRegionalValues->{"9994"} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{"9994"}->{$row["year"]})){
								$indicatorRegionalValues->{"9994"}->{$row["year"]} = array( "value" => 0, "nb" => 0);
							}
							$indicatorRegionalValues->{"9994"}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{"9994"}->{$row["year"]}["nb"] += 1;
						}

						if($countryLLDC == "1") {
							if(!isset($indicatorRegionalValues->{"9995"})){
								$indicatorRegionalValues->{"9995"} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{"9995"}->{$row["year"]})){
								$indicatorRegionalValues->{"9995"}->{$row["year"]} = array( "value" => 0, "nb" => 0 );
							}
							$indicatorRegionalValues->{"9995"}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{"9995"}->{$row["year"]}["nb"] += 1;
						}

						if($countrySIDS == "1") {
							if(!isset($indicatorRegionalValues->{"9997"})){
								$indicatorRegionalValues->{"9997"} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{"9997"}->{$row["year"]})){
								$indicatorRegionalValues->{"9997"}->{$row["year"]} = array( "value" => 0, "nb" => 0 );
							}
							$indicatorRegionalValues->{"9997"}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{"9997"}->{$row["year"]}["nb"] += 1;
						}

						if($countryFragilecountryFragile == "1") {
							if(!isset($indicatorRegionalValues->{"9998"})){
								$indicatorRegionalValues->{"9998"} = new stdClass();
							}
							if(!isset($indicatorRegionalValues->{"9998"}->{$row["year"]})){
								$indicatorRegionalValues->{"9998"}->{$row["year"]} = array( "value" => 0, "nb" => 0 );
							}
							$indicatorRegionalValues->{"9998"}->{$row["year"]}["value"] += $indicatorValue;
							$indicatorRegionalValues->{"9998"}->{$row["year"]}["nb"] += 1;
						}
					}
				}
			}

			//LOOP ON $indicatorRegionalValues AND INSERT or UPDATE values for region
			//region => year => values
			var_dump($indicatorRegionalValues);
			foreach ($indicatorRegionalValues as $regionM49 => $regionData) {
				foreach ($regionData as $year => $yearData) {
					$selectYearData = "SELECT m49 FROM ".$INDICATORDATABASE." WHERE m49 = '".$regionM49."' AND year = '".$year."' AND indicator_id = '".$indicator["id"]."'";
					$resultYearData = $link->query($selectYearData);

					$updateType = "insert";
					while($row = mysqli_fetch_assoc($resultYearData)) {
						$updateType = "update";
					}

					$pctValue = $yearData["value"] / $yearData["nb"];

					if($updateType == "insert") {
						$sqlInsertRegion = "INSERT INTO ".$INDICATORDATABASE." VALUES ( '".$regionM49."', '', '".$year."', '".$indicator["id"]."', '".$pctValue."')";
						//echo $sqlInsertRegion."<br />";
						$link->query($sqlInsertRegion);
					} else {
						$sqlUpdateRegion = "UPDATE ".$INDICATORDATABASE." SET value = '".$pctValue."' WHERE m49 = '".$regionM49."' AND year = '".$year."' AND indicator_id = '".$indicator["id"]."'";
						//echo $sqlUpdateRegion."<br />";
						$link->query($sqlUpdateRegion);
					}
					
				}
			}
		}
	}
}

?>