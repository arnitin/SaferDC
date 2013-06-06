#!/usr/local/bin/php
<?php
$query = "select latitude A,longitude B,offence_type C,ward_id D from crime, (select aa.ward_id TheWard,aa.A  from (select ward_id,count(*) A from crime where ward_id>0 and ward_id<9 group by ward_id) aa, (select min(count(*)) cnt from crime where ward_id>0 and ward_id<9 group by ward_id) B where B.cnt=aa.A) Qry where Qry.TheWard=crime.ward_id";

/////////////////////////////////////////////////////////////Lowest crime ward
/*


select latitude,longitude,offence_type,ward_id from crime, (select aa.ward_id TheWard,aa.A  from (select ward_id,count(*) A from crime where ward_id>0 and ward_id<9 group by ward_id) aa, (select min(count(*)) cnt from crime where ward_id>0 and ward_id<9 group by ward_id) B where B.cnt=aa.A) Qry where Qry.TheWard=crime.ward_id

select latitude,longitude,offence_type,ward_id
from crime,
(select aa.ward_id TheWard,aa.A 
from (select ward_id B,count(*) A
      from crime
      where ward_id>0 and ward_id<9
      group by ward_id) aa,
      (select min(count(*)) cnt
      from crime
      where ward_id>0 and ward_id<9
      group by ward_id) B
where B.cnt=aa.A) Qry
where Qry.TheWard=crime.ward_id;

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
