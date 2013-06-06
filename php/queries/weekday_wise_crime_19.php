#!/usr/local/bin/php
<?php
$query = "select to_char(report_date,'day') A, count(*) B from crime group by to_char(report_date,'day') order by count(*)";

/////////////////////////////////////////////////////////////crime count on weekdays

/*
select to_char(report_date,'day') A, count(*) B 
from crime
group by to_char(report_date,'day') 
*/

$tempArray1 = array();
$tempArray2 = array();

$connection = oci_connect(
	$db_username = 'nar',
	$db_password = 'abcd1234',
	$db_connection_string = '//oracle.cise.ufl.edu/orcl'
);

$statement = oci_parse($connection, $query);
oci_execute($statement);

$counter = 0;
while($row = oci_fetch_array($statement,OCI_BOTH)){
	$tempArray1[$counter] = ($row['A']);//day
	$tempArray2[$counter] = intval($row['B']);//crime
	$counter++;
	}
	
$final = array("title"=>"Crime on Weekdays", "type"=>"bar");
$xAxis = $tempArray1;
$final['xAxis'] = $xAxis;
$final['series'] = array();
$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	
array_push($final['series'],array("name"=>"Crime Count","type"=>"column","data"=>$tempArray2));
		
echo json_encode($final);	
oci_free_statement($statement);
oci_close($connection);
?>