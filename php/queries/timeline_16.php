#!/usr/local/bin/php
<?php
$query = "select to_char(report_date,'mm'),to_char(report_date,'dd'),count(*) A from crime where to_char(report_date,'yy')!='12' group by to_char(report_date,'mm'),to_char(report_date,'dd') order by to_char(report_date,'mm'), to_char(report_date,'dd')";

//////////////////////////////////////////////////////Timeline of number of crimes
/*
select to_char(report_date,'mm'),to_char(report_date,'dd'),count(*) A 
from crime where to_char(report_date,'yy')!='12' 
group by to_char(report_date,'mm'),to_char(report_date,'dd') 
order by to_char(report_date,'mm'), to_char(report_date,'dd')
*/
	
$tempArray = array();
$crimenames = array();
$connection = oci_connect(
						$db_username = 'nar',
						$db_password = 'abcd1234',
						$db_connection_string = '//oracle.cise.ufl.edu/orcl'
						);

$statement = oci_parse($connection, $query);
oci_execute($statement);

$counter = 0;
while($row = oci_fetch_array($statement,OCI_BOTH)) {
		$tempArray[$counter] = intval($row['A']);
		$counter++;
	}
		
$finalVals = array();
$finalVals[0] = array();

for($i =0;$i<$counter;$i++) {
	array_push($finalVals[0],$tempArray[$i]);			
	}
	
$final = array("title"=>"Number Of Crimes Datewise", "type"=>"area","pointStart"=>"Date.UTC(2011, 0, 01)", "pointInterval"=>"24 * 3600 * 1000");
$xAxis = array("type"=>"datetime","maxZoom"=>"14 * 24 * 3600000");
$final['xAxis'] = $xAxis;
$final['series'] = array();
$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	
array_push($final['series'],array("name"=>"CrimeLevel","data"=>$finalVals[0]));
			
echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>