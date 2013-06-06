#!/usr/local/bin/php
<?php
$query = "select latitude A,longitude B,offence_type C,psa_id D from crime ,(select aa.psa_id Thepsa,aa.A  from (select psa_id,count(*) A from crime where psa_id>90 and psa_id <1000  group by psa_id) aa, (select MAX(count(*)) cnt from crime where psa_id>90 and psa_id <1000  group by psa_id) B where B.cnt=aa.A) Qry where Qry.Thepsa=crime.psa_id";

/////////////////////////////////////////////////////////////highest crime ward
/*

select latitude,longitude,offence_type,psa_id D
from crime,
(select aa.psa_id Thepsa,aa.A 
from (select psa_id,count(*) A
      from crime    
	  where psa_id>90 and psa_id <1000
      group by psa_id) aa,
      (select MAX(count(*)) cnt
      from crime
	  where psa_id>90 and psa_id <1000
      group by psa_id) B
where B.cnt=aa.A) Qry
where Qry.Thepsa=crime.psa_id;

*/

	$connection = oci_connect(
		$db_username = 'nar',
		$db_password = 'abcd1234',
		$db_connection_string = '//oracle.cise.ufl.edu/orcl'
	);
	$statement = oci_parse($connection, $query);
	oci_execute($statement);


	$counter = 0;
	while($row = oci_fetch_array($statement,OCI_BOTH)){
		
		$tempArray1[$counter] = floatval($row['A']);//lat
		$tempArray2[$counter] = floatval($row['B']);//long
		$tempArray3[$counter] = $row['C'];//crime
		if ($counter == 0)
			{
			$area = ($row['D']); 
			}
		$counter++;
	}

	$finalCount = 0;
		
	$final['LATITUDE'] = ($tempArray1);
	$final['LONGITUDE'] = ($tempArray2);
	$final['VALUE'] = ($tempArray3);
	$final['AREA'] = ($area);
	
	echo json_encode($final);
	
?>
