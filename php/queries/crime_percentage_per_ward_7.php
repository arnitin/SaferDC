#!/usr/local/bin/php
<?php
/* The Following line adds the string which represents a query into a variable called "query" A dollar sign '$' before it indicates that 'query' is a variable */ 
$query = "select A, B*100/d.sumtot B from (select ward_id A, count(*) B from crime group by ward_id order by ward_id) c,(select count(*) sumtot from crime) d,ward where A=ward.ward_id";

/* The following two lines are two more variables declared as arrays*/
$tempArray = array();
$crimenames = array();

/* Using the OCI Function to connect to the database*/
$connection = oci_connect(
	$db_username = 'nar',                                /* Username */
	$db_password = 'abcd1234',                           /* Password */ 
	$db_connection_string = '//oracle.cise.ufl.edu/orcl' /* Location of the DB */
);

$statement = oci_parse($connection, $query);             /* Binds the connection with the query and provides access through a variable called 'statememt' */
oci_execute($statement);                                 /* Will execute the query */

for($s =0;$s<8;$s++)                                     /* Initialization of 'For' Loop : Values 8 because we know the result of the query has 8 rows */
	{
	$tempArray[$s] = 0;                                  /* Initialization of an array with  0 values - This step can be omitted */ 
	}

$counter = 0;                                            /* Initialization of a counter variable with the value 0 */ 

while($row = oci_fetch_array($statement,OCI_BOTH)){      /* A 'while' loop which fetches all the rows in the result one by one. OCI_BOTH means that the column in the row can be referenced using it's name or by it's column number */
	$WardIds[$counter] = $row['A'];                      /* The Ward ID */
	$tempArray[$counter] = floatval($row['B']);          /* The percent of crime as calculated by the database */ 
	$counter++;                                          /* loop variable */ 
	}
	
$finalVals = array();                                    /* Declaring another array which will hold other arrays as it's values*/

for($i =0;$i<8;$i++){
	$finalVals[$i] = array();                            /* Each element of 'finalVals' is declared to be an array */
	array_push($finalVals[$i],$WardIds[$i]);			 /* Pair of Ward ID and corrosponding Percentage are pushed into the arrays*/  
	array_push($finalVals[$i],$tempArray[$i]);			 /* Corrosponding Percentage data is pushed into the second subarray */ 
	}

$final = array("title"=>"Percentages of Crime", "type"=>"pie"); /* Initializing the data as is required by the DCCharts.js "title"=>"value" means title tag hold that value*/
$xAxis = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); /* Filling in data for the X-Axis */
$final['xAxis'] = $xAxis;                                                                /* Array holding the values is added as the corrosponding value to 'xAxis' tag*/  

$final['series'] = array();

//array_push($final['series'],array("name"=>"CrimeLevel","data"=>$finalVals[0]));
array_push($final['series'],array("name"=>"Test","data"=>$finalVals));	

$json =  json_encode($final);                                                            /* Using PHP function to encode the data so that the JavaScript can access it */
echo $json;                                                                              /* Sendind the value by using the echo command                                */


oci_free_statement($statement);
oci_close($connection);
	
/*Please Do not delete the following commented Code*/

/*$final['series'] = array();                                                         
array_push($final['series'],array("name"=>"Test","data"=>$finalVals));	
	
$a=array("formatter"=>"%*%");
$final['tooltip']=$a;
	
	tooltip: {
					formatter: function() {
							return this.percentage+" %";
					}
				}
	*/

	/*$replace_keys[] = '"' . '%*%' . '"';
	$func = "js:function(){return this.percentage;}";
	$json = str_replace($replace_keys, $func, $json);*/
		
?>
