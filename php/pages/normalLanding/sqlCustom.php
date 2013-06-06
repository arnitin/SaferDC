#!/usr/local/bin/php

<?php
	$end = "<table border='1'><tr>";					/* Indicates the beginning of a table */  
	$query = $_POST['query'];
	if($query == "" || $query == " ")
		$query = "Empty request";   					/* Check for  empty string */ 

	$query = preg_replace("/[\n\r]/"," ",$query); 		/*replace new line with a space*/

	$connection = oci_connect(
						$db_username = 'nar',
						$db_password = 'abcd1234',
						$db_connection_string = '//oracle.cise.ufl.edu/orcl'
					);

	$statement = oci_parse($connection, $query);
	oci_execute($statement);
		
	$ncols = oci_num_fields($statement);

	/* Adding table header */ 
	$end = $end."<tr>\n";                              				     /* beginning of a row*/
	for ($i = 1; $i <= $ncols; $i++) {
		$end = $end."    <td>".oci_field_name($statement, $i)."</td>\n"; /* adding a columns to the row */ 
	}
	$end = $end."</tr>\n";                              				 /* end of row. */ 

	while($row = oci_fetch_array($statement,OCI_BOTH)){
		$end = $end."<tr>\n";
		for($j=0;$j<$ncols;$j++){
			$end = $end."    <td>".$row["$j"]."</td>\n";                  /* same as in previous comments, but now inserting the actual values while parsing the result */ 
		}
		$end = $end."</tr>\n";
	}
	$end = $end."</table>\n";											  /* end of table */ 
	echo "$end";
	
	// VERY important to close Oracle Database Connections and free statements!
	oci_free_statement($statement);
	oci_close($connection);
	
?>