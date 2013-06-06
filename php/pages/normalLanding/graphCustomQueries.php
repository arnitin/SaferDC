#!/usr/local/bin/php
<?php

	$ytype = $_GET['yAxisValues'];
	$xtype = $_GET['xAxisValues'];
	
	$select     = "select offence_type A ,count (*) B";	
	if (strcasecmp($ytype,"ALL") == 0) {	
		$groupby = " group by offence_type";	
		$where = " ";
		$Crimepieces = array("ADW","ARSON","BURGLARY","HOMICIDE","ROBBERY","SEX ABUSE","STOLEN AUTO","THEFT","THEFT F/AUTO");
	    $Crimepeicecount = 9;
	}
	else {
		$groupby = " group by offence_type";	
		$input = str_replace(",","' or offence_type='",$ytype);
		$fmtString = "'" . $input . "'";
		$where = " and (offence_type=" . $fmtString . ")";
		
		
	if (strpos($ytype,',')!== false)
		{
		$Crimepieces = explode(",", $ytype);
		$Crimepeicecount = count(explode(",", $ytype));
		}
	else
		{
		$Crimepieces = array($ytype);
		$Crimepeicecount = 1;
		}
	}
	
	if (strcasecmp($xtype,"DATE") == 0)
		{
		$WardProblem = 0;
		$wardFmt = " ";
		$dateStart = $_GET['fromDate'];
		$dateEnd   = $_GET['toDate'];
		$daterange = " and report_date between to_date('".  $dateStart . "','mm/dd/yyyy') and to_date('" .  $dateEnd . "','mm/dd/yyyy') "; 
		$orderby = " ";
		}
	else
		{
		$WardProblem = 1;
		$orderby = " order by ward_id, offence_type";
		$select = $select . ",ward_id C";
		$groupby = $groupby . ",ward_id";
		$daterange = " ";
		$wards = $_GET['wardID'];
		
		if (strcasecmp($wards,"ALL") == 0) {
			$wardFmt = " and (ward_id='1' or ward_id='2' or ward_id='3' or ward_id='4' or ward_id='5' or ward_id='6' or ward_id='7' or ward_id='8')";
			$Wardpieces = array(1,2,3,4,5,6,7,8);
	        $Wardpeicecount = 8;
			}
		
		else{
			$wardinput = str_replace(",","' or ward_id='",$wards);
			$fmtString = "'" . $wardinput . "'";
			$wardFmt = " and (ward_id=" . $fmtString . ")";
			
			$Wardpieces = explode(",", $wards);
			$Wardpeicecount = count(explode(",", $wards));
			}
		$wardFmt = $wardFmt . " and ward_id > 0 and ward_id < 9"; //For the ALL condition
		}

	
	$query      = $select  . " from crime " . "where call_number>'0'" .  $where . $daterange . $wardFmt . $groupby . $orderby;

	$connection = oci_connect(
						$db_username = 'nar',
						$db_password = 'abcd1234',
						$db_connection_string = '//oracle.cise.ufl.edu/orcl'
					);
	$statement = oci_parse($connection, $query);
	oci_execute($statement);
	$counter = 0;
	

	if ($WardProblem == 0){
		
		while($row = oci_fetch_array($statement,OCI_BOTH)) {
			$tempArray1[$counter] = ($row['A']);//crime
			$tempArray2[$counter] = intval($row['B']);//count
			$counter++;
			}

		$final = array("title"=>"Crime Rate", "type"=>"bar");
		$xAxis = $tempArray1;
		$final['xAxis'] = $xAxis;
		$final['series'] = array();

		array_push($final['series'],array("name"=>"Crime Count","type"=>"column","data"=>$tempArray2));
		}
		
	else
		{
		$prev = "zzz";
		$iIndex = 0;
		$oldward = 1;
		$counter = 0;
		
		while($row = oci_fetch_array($statement,OCI_BOTH)) {
		
		if ($iIndex == $Crimepeicecount)
				{
				$iIndex = 0;
				$oldward = intval($row['C']);
				}
		
		if (($row['A']) != $Crimepieces[$iIndex])
			{
		//	echo $row['A'] . "+" . $Crimepieces[$iIndex];
				{	
				$tempArray1[$counter] = 0;
				$tempArray2[$counter] = $Crimepieces[$iIndex];
				$tempArray3[$counter] = $oldward;
				$counter++;
				$iIndex++;				
				}
			if ($iIndex != $Crimepeicecount)
				{
				$tempArray1[$counter] = intval($row['B']);;
				$tempArray2[$counter] = $row['A'];
				$tempArray3[$counter] = intval($row['C']);
				$counter++;
				$iIndex++;
				}
			}
		else
			{
			$tempArray1[$counter] = intval($row['B']);;
			$tempArray2[$counter] = $row['A'];
			$tempArray3[$counter] = intval($row['C']);
			$counter++;
			$iIndex++;
			}
		}
			
		for ($i = 0 ;$i<$Wardpeicecount;$i++)
			{
			$intermediate[$i] = array();
			}
	
		$finalCount = 0;
		for($i =0;$i<$counter;$i++)
			{
			array_push($intermediate[$finalCount],$tempArray1[$i]);
			if (($i == ($finalCount+1)*$Crimepeicecount -1))
				{
				$finalCount++;
				}
			}
		
		$final = array("title"=>"Crime Rate", "type"=>"bar");
		$xAxis = $Crimepieces;
		$final['xAxis'] = $xAxis;
		$final['series'] = array();
		for($i=0;$i<$finalCount;$i++)
			{
			array_push($final['series'],array("name" => "Ward" . (string)$Wardpieces[$i],"data"=>$intermediate[$i]));
			}
		$plottemp=array("stacking"=>"normal");
		$plottemp1=array("column"=>$plottemp);
		$final['plotOptions'] = array($plottemp1);	
		}
	
		$temp=array("text"=>"Number of Crimes");
		$temp1=array("title"=>$temp);
		$final['yAxis'] = array($temp1);

	echo json_encode($final);
?>