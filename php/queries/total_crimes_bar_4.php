#!/usr/local/bin/php
<?php

$query = "select b.offence_type as A,b.cr_month as B,count(*) as C from (select offence_type, to_char(report_date,'mm') as cr_month from crime where to_char(report_date,'yy') != '12')  b group by b.offence_type,b.cr_month order by b.offence_type, b.cr_month";

/*

select b.offence_type as A,b.cr_month as B,count(*) as C 
from (select offence_type, to_char(report_date,'mm') as cr_month 
	  from crime
	  where to_char(report_date,'yy') != '12')  b 
group by b.offence_type,b.cr_month 
order by b.offence_type, b.cr_month

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

for($s =0;$s<8*12;$s++)
	{
	$tempArray[$s] = 0;
	}

$count   = 0;
$prev    = "zzzz";
$counter = -12;
while($row = oci_fetch_array($statement,OCI_BOTH)){
	if ($prev != $row['A'])
		{
		$prev = $row['A'];
		$counter = $counter + 12;
		$crimenames[$count] = $row['A'];
		$count++;
		}
	$tempArray[$counter + (intval($row['B']) - 1)] = intval($row['C']);
	}
	
$finalVals = array();
for($i=0;$i<8;$i++)
	{
	$finalVals[$i] = array();
	}

$finalCount = 0;
for($i =0;$i<8*12;$i++)
	{
	array_push($finalVals[$finalCount],$tempArray[$i]);
	if (($i == ($finalCount+1)*12 -1))
		{
		$finalCount++;
		}				
	}
	
$final = array("title"=>"Crime Rate", "type"=>"column", "yLabel"=>"Number of crimes","xLabel"=>"Months");
$xAxis = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$final['xAxis'] = $xAxis;
$final['series'] = array();
for($i=0;$i<8;$i++)
	{
	array_push($final['series'],array("name"=>$crimenames[$i],"data"=>$finalVals[$i]));
	}
	

$plottemp=array("stacking"=>"normal");
$plottemp1=array("column"=>$plottemp);
$final['plotOptions'] = array($plottemp1);	


/*
plotOptions: {
			column: {
				stacking: 'normal',
}
}*/

$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	

echo json_encode($final);	
oci_free_statement($statement);
oci_close($connection);
?>
