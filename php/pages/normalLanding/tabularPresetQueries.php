#!/usr/local/bin/php

<?php
	$end = "<table border='1'><tr>";					/* Indicates the beginning of a table */  
	
	$presetQueryNumber = $_POST['presetQNumber'] ;
	
	switch($presetQueryNumber) {
		case 1:
			$query = "SELECT DISTINCT OFFENCE_TYPE FROM CRIME ORDER BY OFFENCE_TYPE";
			break;
		case 2:
			$query = "SELECT DISTINCT WARD_ID FROM WARD";
			break;
		case 3:
			$query = "SELECT DISTINCT PSA_ID FROM PSA";
			break;
		case 4:
			$query = "SELECT DISTINCT WARD_ID,PSA_ID FROM CRIME WHERE WARD_ID BETWEEN 0 AND 9 GROUP BY WARD_ID, PSA_ID ORDER BY WARD_ID";
			break;
		case 5:
			$query = "SELECT WARD_ID, TRUNC(AVERAGE_POVERTY_RATE * POPULATION / 100, 0 ) People_In_Poverty, POPULATION FROM WARD";
			break;
		case 6:
			$query = "SELECT PSA_ID, TRUNC(AVERAGE_POVERTY_RATE * POPULATION / 100, 0 ) People_In_Poverty , POPULATION FROM PSA";
			break;
		case 7:
			$query = "SELECT WARD_ID, TRUNC(AVERAGE_UNEMPLOYMENT_RATE * POPULATION / 100, 0 ) Unemployed, POPULATION FROM WARD";
			break;
		case 8:
			$query = "SELECT PSA_ID, TRUNC(AVERAGE_UNEMPLOYMENT_RATE * POPULATION / 100, 0 ) Unemployed, POPULATION FROM PSA";
			break;
		default:
			alert("Please select a preset query");
	}
	
	//$query = preg_replace("/[\n\r]/"," ",$query); 		/*replace new line with a  space*/

	$connection = oci_connect(
						$db_username = 'nar',
						$db_password = 'abcd1234',
						$db_connection_string = '//oracle.cise.ufl.edu/orcl'
					);

	$statement = oci_parse($connection, $query);
	oci_execute($statement);
		
	$ncols = oci_num_fields($statement);

	/* Adding table header */ 
	$end = $end."<tr>\n";// <tr bgcolor='#FFC600'>";                         /* beginning of a row*/  /* Color Attribute : Play around or remove it */ 
	for ($i = 1; $i <= $ncols; $i++) {
		$end = $end."    <td>".oci_field_name($statement, $i)."</td>\n"; /* adding a columns to the row */ 
	}
	$end = $end."</tr>\n";                              				 /* end of row. */ 

	while($row = oci_fetch_array($statement,OCI_BOTH)){
		$end = $end."<tr>\n";// <tr bgcolor='#C6AF00'>";                     /* Color Attribute : Play around or remove it */ 
		for($j=0;$j<$ncols;$j++){
			$end = $end."    <td>".$row["$j"]."</td>\n";                  /* same as in previous comments, but now inserting the actual values while parsing the result */ 
		}
		$end = $end."</tr>\n";
	}
	$end = $end."</table>\n";											  /* end of table */ 
	
	echo "$end";
	oci_free_statement($statement);
	oci_close($connection);
?>