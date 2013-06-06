#!/usr/local/bin/php
<?php
$query = "select longest A ,endo B,starto C from(select (bb.c-aa.c) longest,bb.c endo,aa.c starto from (select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY')) b from (select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A  from crime where to_char(report_date,'yy')!='12'  group by to_char(report_date,'mm'),to_char(report_date,'dd') order by to_char(report_date,'mm'), to_char(report_date,'dd'))) aa, (select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY')) b from (select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A  from crime where to_char(report_date,'yy')!='12'  group by to_char(report_date,'mm'),to_char(report_date,'dd') order by to_char(report_date,'mm'), to_char(report_date,'dd'))) bb where bb.b=aa.b+1) one where one.longest=(select max(bb.c-aa.c)  from(select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY')) b from(select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A  from crime where to_char(report_date,'yy')!='12'  group by to_char(report_date,'mm'),to_char(report_date,'dd') order by to_char(report_date,'mm'), to_char(report_date,'dd'))) aa, (select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY') ) b from(select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A  from crime where to_char(report_date,'yy')!='12'  group by to_char(report_date,'mm'),to_char(report_date,'dd')order by to_char(report_date,'mm'), to_char(report_date,'dd'))) bb where bb.b=aa.b+1)";

/////////////////////////////////////////////////////////////Longest crime free duration
/*select longest A ,endo B,starto C
from(select (bb.c-aa.c) longest,bb.c endo,aa.c starto
	 from (select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY')) b
		   from (select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A 
				 from crime where to_char(report_date,'yy')!='12' 
				 group by to_char(report_date,'mm'),to_char(report_date,'dd')
				 order by to_char(report_date,'mm'), to_char(report_date,'dd'))) aa,
		  (select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY')) b
		   from (select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A 
				 from crime where to_char(report_date,'yy')!='12' 
				 group by to_char(report_date,'mm'),to_char(report_date,'dd')
				 order by to_char(report_date,'mm'), to_char(report_date,'dd'))) bb
	 where bb.b=aa.b+1) one
where one.longest=(select max(bb.c-aa.c) 
				   from(select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY')) b
						from(select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A 
							 from crime where to_char(report_date,'yy')!='12' 
							 group by to_char(report_date,'mm'),to_char(report_date,'dd')
							 order by to_char(report_date,'mm'), to_char(report_date,'dd'))) aa,
					   (select to_date(aa||bb||'11','MMDDYY') c ,row_number() OVER (order by to_date(aa||bb||'11','MMDDYY') ) b
						from(select to_char(report_date,'mm') aa,to_char(report_date,'dd') bb ,count(*) A 
							 from crime where to_char(report_date,'yy')!='12' 
							 group by to_char(report_date,'mm'),to_char(report_date,'dd')
							 order by to_char(report_date,'mm'), to_char(report_date,'dd'))) bb
					where bb.b=aa.b+1);*/

$connection = oci_connect(
	$db_username = 'nar',
	$db_password = 'abcd1234',
	$db_connection_string = '//oracle.cise.ufl.edu/orcl'
);
$statement = oci_parse($connection, $query);
oci_execute($statement);


$counter = 0;
while($row = oci_fetch_array($statement,OCI_BOTH)){

	$tempArray1[$counter] = intval($row['A']);//days
	$tempArray2[$counter] = $row['B'];//end
	$tempArray3[$counter] = $row['C'];//start
	$counter++;
	}
$finalVals = array();

$finalCount = 0;
	
$final = array("title"=>"Longest Crime Free Duration", "type"=>"bar");
$xAxis = array($tempArray3[0] . '  to  ' . $tempArray2[0]); 
$final['xAxis'] = $xAxis;
$final['series'] = array();
$temp=array("text"=>"Number of Crimes");
$temp1=array("title"=>$temp);
$final['yAxis'] = array($temp1);	
array_push($final['series'],array("name"=>"Days","type"=>"column","data"=>$tempArray1[0]));
	
echo json_encode($final);
oci_free_statement($statement);
oci_close($connection);
?>
