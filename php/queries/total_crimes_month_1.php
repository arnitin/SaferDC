#!/usr/local/bin/php
<?php

$query = "select b.cr_month as A,count(*) as B from (select offence_type, to_char(report_date,'mm') as cr_month from crime where to_char(report_date,'yy') != '12')  b group by b.cr_month order by b.cr_month";

/*

select b.cr_month as A,count(*) as B 
from (select offence_type,
	  to_char(report_date,'mm') as cr_month 
	  from crime
	  where to_char(report_date,'yy') != '12')
b group by b.cr_month 
order by b.cr_month

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

for($s =0;$s<12;$s++){
    $tempArray[$s] = 0;
}

$counter = 0;
while($row = oci_fetch_array($statement,OCI_BOTH)){
    $tempArray[$counter] = intval($row['B']);
    $counter++;
}

$finalVals = array();
$finalVals[0] = array();

for($i =0;$i<12;$i++){
    array_push($finalVals[0],$tempArray[$i]);
}

$final = array("title"=>"Number Of Crimes", "type"=>"line", "yLabel"=>"Number of crimes", "xLabel"=>"Months");
$xAxis = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"CrimeLevel","data"=>$finalVals[0]));

$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	

echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>
