#!/usr/local/bin/php
<?php
$query = "select one.A A,one.B B,one.C C from (select block_address A, count(*) B, offence_type C from crime where length(block_address)>0 group by block_address,offence_type) one where one.b>=all(select count(*) B from crime c where length(block_address)>0 and c.offence_type=one.C  group by block_address,offence_type) order by one.c";

//////////////////////////////////////////////////Address wise higest crime
/*

select one.A A,one.B B,one.C C 
from (select block_address A, count(*) B, offence_type C 
	  from crime where length(block_address)>0 
	  group by block_address,offence_type) one 
where one.b>=all(select count(*) B 
				 from crime c 
				 where length(block_address)>0 and c.offence_type=one.C  
				 group by block_address,offence_type)
				 order by one.c

*/

$crimenames = array();
$connection = oci_connect(
	$db_username = 'nar',
	$db_password = 'abcd1234',
	$db_connection_string = '//oracle.cise.ufl.edu/orcl'
);

$statement = oci_parse($connection, $query);
oci_execute($statement);

$counter = 0;

while($row = oci_fetch_array($statement,OCI_BOTH)){
	$Addr[$counter] = $row['A'];
	$Count[$counter] = intval($row['B']);
	$Offence[$counter] = $row['C'];
	$counter++; 
	}

$finalVals = array();
	
for($i=0;$i<$counter;$i++)
	{
	$finalVals[$i] = array();
	array_push($finalVals[$i],$Addr[$i]);			
	array_push($finalVals[$i],$Count[$i]);			
	}

$final = array("title"=>"Crime Prone Locations", "type"=>"bar");
$xAxis = $Offence;
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"Count","type"=>"bar","data"=>$finalVals));	

$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);

echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>