#!/usr/local/bin/php
<?php

$query = "select A,B,C from (select b.offence_type as A,b.cr_month as B,count(*) as C from (select offence_type, to_char(report_date,'mm') as cr_month from crime)  b group by b.offence_type,b.cr_month order by b.offence_type, b.cr_month) One where One.C = (select max(count(*)) from (select offence_type, to_char(report_date,'mm') as cr_month1 from crime)  b1 where cr_month1=One.B group by b1.offence_type,b1.cr_month1) order by B";

///////////////////////// Highest Type of crime per month
/*
select A,B,C 
from (select b.offence_type as A,b.cr_month as B,count(*) as C 
	  from (select offence_type, to_char(report_date,'mm') as cr_month 
			from crime)  b 
	  group by b.offence_type,b.cr_month 
	  order by b.offence_type, b.cr_month) One 
where One.C = (select max(count(*)) 
			   from (select offence_type, to_char(report_date,'mm') as cr_month1 
					 from crime)  b1 
			   where cr_month1=One.B 
			   group by b1.offence_type,b1.cr_month1)
order by B
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
		}
		
$counter = 0;
while($row = oci_fetch_array($statement,OCI_BOTH)){
	$tempArray1[$counter] = $row['A'];//crime
	$tempArray2[$counter] = intval($row['C']);//COUNT
	$counter++;
	}
	
$finalVals = array();
for($i=0;$i<12;$i++)
	{
	$finalVals[$i] = array();
	}

$finalCount = 0;
for($i =0;$i<12;$i++)
	{
	array_push($finalVals[$i],$tempArray1[$i]);
	array_push($finalVals[$i],$tempArray2[$i]);				
	}
	
$final = array("title"=>"Crime Rate", "type"=>"bar","yLabel"=>"Number of crimes","xLabel"=>"Months");
$xAxis = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$final['xAxis'] = $xAxis;
$final['series'] = array();

array_push($final['series'],array("name"=>"Crime","data"=>$finalVals));

		$temp=array("text"=>"Number of Crimes");
		$temp1=array("title"=>$temp);
		$final['yAxis'] = array($temp1);	
		
echo json_encode($final);	
oci_free_statement($statement);
oci_close($connection);
?>
