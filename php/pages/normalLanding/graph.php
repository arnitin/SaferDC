#!/usr/local/bin/php
<?php
/*
	$type = $_GET['type'];
	$dateStart = $_GET['dateStart'];
	$dateEnd   = $_GET['dateEnd'];

	if (strcasecmp($type,"all") == 0) {	
		$groupby = " group by offence_type";	
		$where = "";
	}
	else {
		$groupby = "";
		$where = " and offence_type=" . $type;
	}
		
	$select = "count (*)";	

	$query = "select offence_type A ," . $select . " B" . " from crime " . "where " . "report_date between to_date('" .  $dateStart . "','mm/dd/yyyy' and to_date('" .  $dateEnd . "','mm/dd/yyyy' "  . $where . $groupby;

	echo $query;

	$connection = oci_connect(
						$db_username = 'nar',
						$db_password = 'abcd1234',
						$db_connection_string = '//oracle.cise.ufl.edu/orcl'
					);
	$statement = oci_parse($connection, $query);
	oci_execute($statement);

	while($row = oci_fetch_array($statement,OCI_BOTH)) {
		$tempArray1[$counter] = ($row['A']);//crime
		$tempArray2[$counter] = intval($row['B']);//count
		//$psaIds[$counter] = $row['B'];//psaID
		$counter++;
	}

	$final = array("title"=>"Crime Rate", "type"=>"bar");
	$xAxis = $tempArray1;
	$final['xAxis'] = $xAxis;
	$final['series'] = array();

	array_push($final['series'],array("name"=>"Crime Count","type"=>"column","data"=>$tempArray2));
	//	array_push($final['series'],array("name"=>"Income","type"=>"spline","data"=>$finalVals[1],"yAxis"=>1));

	/*$temp=array("text"=>"Number of Crimes");
	$temp1=array("title"=>$temp);
	
	$tempp=array("text"=>"Income");
	$temp2=array("title"=>$tempp,"opposite"=>"true");
	
	$final['yAxis'] = array($temp1,$temp2);		
	*/
	echo json_encode($final);
?>