#!/usr/local/bin/php

<?php

  $connection = oci_connect($username = 'nar',
                            $password = 'abcd1234',
                            $connection_string = '//oracle.cise.ufl.edu/orcl');

  /* Rahul - Constructing a query based on all the values GETed to this php file. */
  
  $query = "SELECT LATITUDE,LONGITUDE,OFFENCE_TYPE,REPORT_DATE FROM CRIME WHERE " ;

  $crime_type = $_GET["crimeType"] ;
  if (strlen($crime_type) > 0) {
    $query = "$query OFFENCE_TYPE LIKE '$crime_type'" ;
  }
  else
    $query = "$query OFFENCE_TYPE LIKE '%'" ;

  $psaID = $_GET["psaID"] ;
  if (strlen($psaID) > 0) {
    $query = "$query AND PSA_ID LIKE '$psaID'" ;
  }
  else
    $query = "$query AND PSA_ID LIKE '%'" ;

  $wardID = $_GET["wardID"] ;
  if (strlen($wardID) > 0) {
    $query = "$query AND WARD_ID LIKE '$wardID'" ;
  }
  else
    $query = "$query AND WARD_ID LIKE '%'" ;
/*
  $fromDate = $_GET["fromDate"] ;
  if (strlen($fromDate) > 0) {
    $query = "$query AND REPORT_DATE > '$fromDate'" ;
  }
  else
    $query = "$query AND REPORT_DATE LIKE '%'" ;

  $toDate = $_GET["toDate"] ;
  if (strlen($toDate) > 0) {
    $query = "$query AND REPORT_DATE < '$toDate'" ;
  }
  else
    $query = "$query AND REPORT_DATE LIKE '%'" ;
*/

  //$crime_type = $_POST["crimeType"] ;
  //$crime_type = $_GET["crimeType"] ;
  //$statement = oci_parse($connection, "SELECT LATITUDE,LONGITUDE FROM CRIME WHERE OFFENCE_TYPE LIKE 'ARSON'");
  
  //$query = "SELECT LATITUDE,LONGITUDE FROM CRIME WHERE OFFENCE_TYPE LIKE '$crime_type' ";
  
  $statement = oci_parse($connection, $query) ; 
  oci_execute($statement);

  $result_string;
  $num_rows = oci_fetch_all($statement,$result_string) ;
  
  /*
  $counter = 0 ; 
  while ( $counter < $num_rows ) {
    $result_string['VALUE'][$counter] = "Type of Crime = ARSON" ;
	$counter++ ; 
  }
  */ 
  echo json_encode($result_string);

  //
  // VERY important to close Oracle Database Connections and free statements!
  //
  //echo "$query" ; // Rahul - to know what $query contains
  oci_free_statement($statement);
  oci_close($connection);

?>