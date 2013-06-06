#!/usr/local/bin/php

<?php
/* Rahul - have renamed normalLanding.php to mapCustomQueries.php */

  $connection = oci_connect($username = 'nar',
                            $password = 'abcd1234',
                            $connection_string = '//oracle.cise.ufl.edu/orcl');

  /* Rahul - Constructing a query based on all the values POSTed to this php file. */
  
  $query = "SELECT LATITUDE L,LONGITUDE N,OFFENCE_TYPE O,REPORT_DATE R FROM CRIME WHERE " ;

  $crime_type = $_POST["crimeType"] ;
  if (strlen($crime_type) > 0) {
    $query = "$query OFFENCE_TYPE LIKE '$crime_type'" ;
  }
  else
    $query = "$query OFFENCE_TYPE LIKE '%'" ;

  $psaID = $_POST["psaID"] ;
  if (strlen($psaID) > 0) {
    $query = "$query AND PSA_ID LIKE '$psaID'" ;
  }
  else
    $query = "$query AND PSA_ID LIKE '%'" ;

  $wardID = $_POST["wardID"] ;
  if (strlen($wardID) > 0) {
    $query = "$query AND WARD_ID LIKE '$wardID'" ;
  }
  else
    $query = "$query AND WARD_ID LIKE '%'" ;

  $fromDate = $_POST["fromDate"] ;
  if ( strlen($fromDate) > 13 || strlen($fromDate)== 0) {
    $fromDate = '01/01/2011';
  }
  
  $toDate = $_POST["toDate"] ;
  if ( strlen($toDate) > 13 || strlen($toDate) == 0 ) {
    $toDate = '12/31/2011';
  }
  
  $query = "$query AND REPORT_DATE >= TO_DATE('$fromDate','MM/DD/YYYY') AND REPORT_DATE <= TO_DATE('$toDate','MM/DD/YYYY')" ;

  /*$crime_type = $_POST["crimeType"] ;
  * $crime_type = $_POST["crimeType"] ;
  * $statement = oci_parse($connection, "SELECT LATITUDE,LONGITUDE FROM CRIME WHERE OFFENCE_TYPE LIKE 'ARSON'");
  
  * $query = "SELECT LATITUDE,LONGITUDE FROM CRIME WHERE OFFENCE_TYPE LIKE '$crime_type' ";
  */
  $statement = oci_parse($connection, $query) ; 
  oci_execute($statement);

  /* To get ALL results into $result_string
  $result_string;
  $num_rows = oci_fetch_all($statement,$result_string) ;
  */
  
  /* To assign query results to arrays row by row*/
  $counter = 0 ; 
  while ( ($row = oci_fetch_array($statement,OCI_BOTH)) /* && $counter < 1000 */ ) {
    $tempArray1[$counter] = $row['L'] ;										// L -> Latitude
	$tempArray2[$counter] = $row['N'] ;										// N -> Longitude
	$tempArray3[$counter] = $row['O'].' committed here on '.$row['R'] ;		// O -> Offenct_type , R -> Report_Date
	$counter++ ; 
  }
  
  $final['LATITUDE'] = ($tempArray1);
  $final['LONGITUDE'] = ($tempArray2);
  $final['VALUE'] = ($tempArray3);
  
  echo json_encode($final);
  
  //echo json_encode($result_string);

  //
  // VERY important to close Oracle Database Connections and free statements!
  //
  echo "$query" ; // Rahul - to know what $query contains
  oci_free_statement($statement);
  oci_close($connection);

?>