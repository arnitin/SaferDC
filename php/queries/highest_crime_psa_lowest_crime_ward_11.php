#!/usr/local/bin/php
<?php
$query = "select A.psa_id I,A.psa_crime C from (select psa_id,count(*) psa_crime from crime,(select ward_id , B from (select ward_id ,count(*) B from crime  where ward_id>0 and ward_id<9 group by ward_id) where B=(SELECT min(count(*))  from crime where ward_id>0 and ward_id<9 group by ward_id)) test where test.ward_id=substr(crime.psa_id,1,1) group by psa_id) A where A.psa_crime=(select max(count(*)) from crime,(select ward_id , B from (select ward_id ,count(*) B  from crime  where ward_id>0 and ward_id<9 group by ward_id) where B=(SELECT min(count(*))  from crime where ward_id>0 and ward_id<9  group by ward_id)) test where test.ward_id=substr(crime.psa_id,1,1) group by psa_id)";

/////////////////////////////////////////////////////////////lowest crime ward higest psa crime

/*

select A.psa_id I,A.psa_crime C 
from (select psa_id,count(*) psa_crime
	  from crime,(select ward_id , B 
				  from (select ward_id ,count(*) B 
					    from crime  where ward_id>0 and ward_id<9 
						group by ward_id) 
				  where B=(SELECT min(count(*))
						   from crime where ward_id>0 and ward_id<9
						   group by ward_id)) test
	  where test.ward_id=substr(crime.psa_id,1,1) 
	  group by psa_id) A 
where A.psa_crime=(select max(count(*)) 
				   from crime,(select ward_id , B 
							   from (select ward_id ,count(*) B  
									 from crime  
									 where ward_id>0 and ward_id<9 group by ward_id)
		     				   where B=(SELECT min(count(*))  
										from crime where ward_id>0 and ward_id<9
										group by ward_id)) test 
				   where test.ward_id=substr(crime.psa_id,1,1) 
				   group by psa_id)

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
	$tempArray1[$counter] = ($row['I']);//psa
	$tempArray2[$counter] = intval($row['C']);//crime
	$counter++;
	}
	
$final = array("title"=>"Highest Crime PSA in the lowst crime Ward", "type"=>"bar");
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