#!/usr/local/bin/php
<?php
$query = "select latt A,longg B, VAL C from  (select latitude latt,longitude longg,to_char(report_date,'D') Z,count(*) val from crime  where latitude!= 0 group by latitude,longitude,to_char(report_date,'D')) tabl where tabl.val = (SELECT MAX(A) FROM (select latitude,longitude,to_char(report_date,'D') Z,count(*) A from crime  where latitude!= 0 group by latitude,longitude,to_char(report_date,'D')) WHERE Z = '4')";


//select latt A,longg B, VAL C from  (select latitude latt,longitude longg,to_char(report_date,'D') Z,count(*) val from crime  group by latitude,longitude,to_char(report_date,'D')) tabl where tabl.val = (SELECT MAX(A) FROM (select latitude,longitude,to_char(report_date,'D') Z,count(*) A from crime  group by latitude,longitude,to_char(report_date,'D')) WHERE Z = '4');

/////////////////////////////////////////////////////////////highest incidence of a crime at a place on monday
 /*
 select latt A,longg B, VAL
from  (select latitude latt,longitude longg,to_char(report_date,'D') Z,count(*) val
from crime 
group by latitude,longitude,to_char(report_date,'D')) tabl
where latt!=0 and tabl.val = (SELECT MAX(A) FROM (select latitude,longitude,to_char(report_date,'D') Z,count(*) A
from crime 
group by latitude,longitude,to_char(report_date,'D'))
WHERE Z = '4');
*/
	$connection = oci_connect(
								$db_username = 'nar',
								$db_password = 'abcd1234',
								$db_connection_string = '//oracle.cise.ufl.edu/orcl'
							);
	$statement = oci_parse($connection, $query);
	oci_execute($statement);

	$counter = 0;
	while( ($row = oci_fetch_array($statement,OCI_BOTH)) && $counter < 10 ) {

		$tempArray1[$counter] = floatval($row['A']);//lat
		$tempArray2[$counter] = floatval($row['B']);//long
		$tempArray3[$counter] = /*$row[0]." '".*/$row['C'] . " crimes on Wednesdays here" ;//count
		$counter++;
	}
	//$final = array();

	$finalCount = 0;
		
	//	var_dump($tempArray1);
		
	$final['LATITUDE'] = ($tempArray1);
	$final['LONGITUDE'] = ($tempArray2);
	$final['VALUE'] = ($tempArray3);
	
	echo json_encode($final);
	
?>
