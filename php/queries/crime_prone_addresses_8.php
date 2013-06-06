#!/usr/local/bin/php
<?php
$query = "select block_address A, count(*) B from crime  where length(block_address)>0 group by block_address order by count(*) DESC";

////////////////////////////////////////////crime prones addresss

/*
select block_address A, count(*) B 
from crime  
where length(block_address)>0 
group by block_address 
order by count(*) DESC
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
while(($row = oci_fetch_array($statement,OCI_BOTH))&&($counter<20)){
	$Addr[$counter] = $row['A'];
	$tempArray[$counter] = intval($row['B']);
	$counter++; 
	}
	
$final = array("title"=>"Crime Prone Locations", "type"=>"bar");
$xAxis = $Addr;
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"Crime Count","type"=>"bar","data"=>$tempArray));	

$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	

echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>