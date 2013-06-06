#!/usr/local/bin/php
<?php

$query = "select A, B, Population Z from (select ward_id A, count(*) B from crime group by ward_id order by ward_id) c,ward where A=ward.ward_id";

////////////////////////////////////////////////////////
/*

select A, B, Population Z 
from (select ward_id A, count(*) B 
	  from crime group by ward_id order by ward_id) c,ward
where A=ward.ward_id

*/
$tempArray1 = array();
$tempArray2 = array();
$WardIds    = array();

$connection = oci_connect(
	$db_username = 'nar',
	$db_password = 'abcd1234',
	$db_connection_string = '//oracle.cise.ufl.edu/orcl'
);
$statement = oci_parse($connection, $query);
oci_execute($statement);

for($s =0;$s<8;$s++)
		{
		$tempArray1[$s] = 0;
		$tempArray2[$s] = 0;
		//insert 0
		}
		
$counter = 0;

while($row = oci_fetch_array($statement,OCI_BOTH)){
	$tempArray1[$counter] = intval($row['B']);//crime
	$tempArray2[$counter] = intval($row['Z']);//population
	$WardIds[$counter] = $row['A'];//WARDID
	$counter++;
	}
$finalVals = array();
for($i=0;$i<2;$i++)
	{
	$finalVals[$i] = array();
	}

$finalCount = 0;
for($i =0;$i<8;$i++)
	{
	array_push($finalVals[0],$tempArray1[$i]);
	array_push($finalVals[1],$tempArray2[$i]);				
	}
	
$final = array("title"=>"Crime Rate", "type"=>"bar","yLabel"=>"Crime rate or population","xLabel"=>"Wards");
$xAxis = $WardIds;
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"Crime Rate","data"=>$finalVals[0]));
array_push($final['series'],array("name"=>"Population","data"=>$finalVals[1]));
$temp=array("text"=>"Wards");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	
		
echo json_encode($final);

oci_free_statement($statement);
oci_close($connection);	
?>
