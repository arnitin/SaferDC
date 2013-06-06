#!/usr/local/bin/php
<?php
$final = array(title => "Example of Title", type => "line");
$series = array();
$xAxis = array("JAN","FEB","MAR","APR","MAY");

$first = array("name" => "1st Alfa", "data" => array(10,50,25,15,50));
$second = array("name" => "2nd Beta", "data" => array(50,46,13,76,34,87));

array_push($series, $first);
array_push($series, $second);

$final["series"] = $series;
$final["xAxis"] = $xAxis;

echo json_encode($final);
?>