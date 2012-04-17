<?php

$dbhost = 'localhost';
$dbusername = 'root';
$dbpass = 'root';
$db_name = 'forkstage';
$db_table = 'currency_converter_rates';

$conn = mysql_connect($dbhost, $dbusername, $dbpass) or die("Cannot connect to MySQL database server:<br>".mysql_error());
$db = mysql_select_db($db_name, $conn) or die("Cannot open database:<br>".mysql_error($conn));


$xmlUrl = "http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml";


// displays all the file nodes
if(!$xml=simplexml_load_file($xmlUrl))
{
    trigger_error('Error reading XML file', E_USER_ERROR);
}

$tempArray = array();
$query = "TRUNCATE TABLE currency_converter_rates";

$resultval = mysql_query($query) or die("Cannot run query:<br><b>Query:</b> ".$query."<br>".mysql_error($conn));

foreach($xml->Cube->Cube->Cube as $cube)
{
	$currency = (string) $cube->attributes()->currency;
	$rate = (string) $cube->attributes()->rate;
	
	$tempArray[$currency] = $rate;
	
	$query = "INSERT INTO {$db_table} (currency, rate) VALUES ('{$currency}', '{$rate}')";
	$resultval = mysql_query($query) or die("Cannot run query:<br><b>Query:</b> ".$query."<br>".mysql_error($conn));
}

var_dump($tempArray);