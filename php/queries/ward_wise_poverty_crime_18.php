#!/usr/local/bin/php
<?php
$query = "select A.sumtot A, A.ward_id B, average_poverty_rate C from (select count(*) sumtot , ward_id from crime where ward_id>0 and ward_id<9 group by ward_id) A, Ward where Ward.Ward_id=A.Ward_id order by A.ward_id";

/////////////////////////////////////////////////////////////WARD and poverty rate
/*
select A.sumtot A, A.ward_id B, average_poverty_rate C 
from (select count(*) sumtot , ward_id 
	  from crime where ward_id>0 and ward_id<9 
	  group by ward_id) A, Ward 
where Ward.Ward_id=A.Ward_id order by A.ward_id
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

	$tempArray1[$counter] = intval($row['A']);//crime
	$tempArray2[$counter] = intval($row['C']);//poverty
	$WardIds[$counter] = "Ward " . $row['B'];//WARDID
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
		
$final = array("title"=>"Crime Rate", "type"=>"bar");
$xAxis = $WardIds;
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"Crime Rate","type"=>"column","data"=>$finalVals[0]));
array_push($final['series'],array("name"=>"Poverty","type"=>"spline","data"=>$finalVals[1],"yAxis"=>1));
		
$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
	
$tempp=array("text"=>"Percent Poverty");
$temp2=array("title"=>$tempp,"opposite"=>"true");
	
$final['yAxis'] = array($temp1,$temp2);
	
echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>