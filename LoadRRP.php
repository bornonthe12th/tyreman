#!/usr/bin/php
<?php


if(isset($_GET['company']))                     // Is the script is being run through a browser
  $company_id = $_GET['company'];
else
  if(isset($_SERVER["argv"][1]))               // If the script is being run direct from Linux server
    $company_id = $_SERVER["argv"][1];

//include error class
include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php';

$dirname =  "load/".$company."/";
$row = 1;
$filename = $dirname . "rrps.csv";
$handle = fopen($filename, "r");

	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2))
        {
	    $num = count($data);
		$query = "select count(*) rowcount from stock where Stockcode ='" . $data[0] . "'";
		$result = mysql_query($query);
		//update stock file
		if (mysql_result($result,0,"rowcount") > 0) 
		{ 
			//set up query
			$updstmnt="update stock s ";	
			 //first col is stock code
			$whrstmnt = "where s.Stockcode ='" . $data[0] . "'";
			$setstmnt = "set s.rrp='" . $data[1] . "',";
			$setstmnt = $setstmnt ."s.rrp4='" . $data[2] . "'";
			//build query
			$query = $updstmnt . $setstmnt . $whrstmnt;
			//update row
			$result=mysql_query($query);
		 } 
			$row++;
			//loop to next row
	}
	fclose($handle);
	//commit changes
	$query = "commit;";
	$result=mysql_query($query);


//disconnect
include 'LDclosedb.php';
?> 
