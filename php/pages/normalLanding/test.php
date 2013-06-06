#!/usr/local/bin/php

<html>
<head>
<title>PHP Test</title>
<!-- THIS FILE IS JUST TO TEST PHP STUFF. WE CAN DELETE THIS FOR CHECKPOINT -->
</head>

<body>

<?php
/*
	$toDate = "SOME DATE" ;
	$query = '"SELECT LATITUDE,LONGITUDE FROM CRIME WHERE ' ;
	$query = $query.' AND REPORT_DATE < '.$toDate.' "';
	echo $query ;
*/
	$query = "SELECT LATITUDE,LONGITUDE FROM CRIME " ;
	
	$crime_type = $_GET["crimeType"] ;
	if (strlen($crime_type) > 0) {
		$query = "$query OFFENCE_TYPE LIKE '$crime_type'" ;
	}

	$psaID = $_GET["psaID"] ;
	if (strlen($psaID) > 0) {
		$query = $query.' AND PSA_ID = '.$psaID ;
	}
	else
		$query = "$query OFFENCE_TYPE LIKE '%'" ;
		$query = $query.' AND PSA_ID = '.$psaID ;
	
	$wardID = $_GET["wardID"] ;
	if (strlen($wardID) > 0) {
		$query = $query.' AND WARD_ID = '.$wardID ;
	}
	
/*	
	$fromDate = $_GET["fromDate"] ;
	if (strlen($fromDate) > 0) {
		$query = $query.' AND REPORT_DATE > '.$fromDate ;
	}
	
	$toDate = $_GET["toDate"] ;
	if (strlen($toDate) > 0) {
		$query = $query.' AND REPORT_DATE < '.$toDate ;
	} */
	
	$query = $query.' "' ;
	echo $query ; 
	
?>

</body>

</html>