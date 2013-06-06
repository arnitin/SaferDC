#!/usr/local/bin/php
<?php
$query = "select latitude A,longitude B,count(*) C from crime where latitude!=0 and longitude!=0 and to_number(to_char(report_date,'mm'))>6 and to_number(to_char(report_date,'mm'))<10 group by latitude,longitude order by count(*) desc";

/////////////////////////////////////////////////////////////Crime concentration third q
	/*select latitude,longitude,count(*)
	from crime
	where latitude!=0 and longitude!=0 and to_number(to_char(report_date,'mm'))>6 and to_number(to_char(report_date,'mm'))<10
	group by latitude,longitude
	order by count(*) desc;*/

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
		$tempArray3[$counter] = ''.intval($row['C']).' Crimes were committed here';//count
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
