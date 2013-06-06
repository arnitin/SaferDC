#!/usr/local/bin/php
<?php
$query = "select A.sumtot A, A.ward_id B, average_family_income C ,average_unemployment_rate D from (select count(*) sumtot , ward_id from crime where ward_id>0 and ward_id<9 group by ward_id) A, Ward where Ward.Ward_id=A.Ward_id order by A.ward_id";

/////////////////////////////////////////////////////////////WARD and income rate
/*
select A.sumtot A, A.ward_id B, average_family_income C average_unemployment_rate D
from (select count(*) sumtot , ward_id 
	  from crime where ward_id>0 and ward_id<9 
	  group by ward_id) A, Ward 
where Ward.Ward_id=A.Ward_id order by A.ward_id
*/

$tempArray1 = array();
$tempArray2 = array();
$tempArray3 = array();
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
		$tempArray3[$s] = 0;
		}
		
$counter = 0;

while($row = oci_fetch_array($statement,OCI_BOTH)){
	$tempArray1[$counter] = intval($row['A']);//crime
	$tempArray2[$counter] = intval($row['C']);//income
	$WardIds[$counter] = "Ward ".$row['B'];//WARDID
	$tempArray3[$counter] = intval($row['D']);//income
	$counter++;
	}

$finalVals = array();

for($i=0;$i<3;$i++)
	{
	$finalVals[$i] = array();
	}

$finalCount = 0;
for($i =0;$i<8;$i++)
	{
	array_push($finalVals[0],$tempArray1[$i]);
	array_push($finalVals[1],$tempArray2[$i]);				
	array_push($finalVals[2],$tempArray3[$i]);				
	}
		
$final = array("title"=>"Crime Rate", "type"=>"bar");
$xAxis = $WardIds;
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"Crime Rate","type"=>"column","data"=>$finalVals[0]));
array_push($final['series'],array("name"=>"Income","type"=>"spline","data"=>$finalVals[1],"yAxis"=>1));
array_push($final['series'],array("name"=>"Unemployment","type"=>"spline","data"=>$finalVals[2],"yAxis"=>2));
		
$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
	
$tempp=array("text"=>"Income");
$temp2=array("title"=>$tempp,"opposite"=>"true");

$temppq=array("text"=>"Unemployment");
$temp3=array("title"=>$temppq,"opposite"=>"true");
	
$final['yAxis'] = array($temp1,$temp2,$temp3);
		
echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>