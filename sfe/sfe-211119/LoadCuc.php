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


//read list of files
// open this directory

$dirname =  "load/".$company."/";
//echo $dirname . "\n";

$myDirectory = opendir($dirname);
// get each entry
while($entryName = readdir($myDirectory))
{
	//only include .CUC files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".CUC"){
		$dirArray[] = $entryName;
	}
}


// close directory
closedir($myDirectory);

//	count elements in array
$indexCount	= count($dirArray);

// sort by date
sort($dirArray);


//only process latest file
$index = $indexCount-1;

if (substr("$dirArray[$index]", 0, 1) != ".")
{ // don't list hidden files
	
	$query="delete from customer_class";
	$result=mysql_query($query);

	$row = 1;
	$filename = $dirname . $dirArray[$index];
	echo $filename . "\n";
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[2])>4))
        {
    	$setstmnt = '';
	$num = count($data);
    	//set up query
	$query="insert into customer_class (Account_No,productgroup,display_flag) values (";	
      	$query = $query . "'" . $data[2] . "',";
        $query = $query . "'" . $data[3] . "',";
        $query = $query . "'" . $data[4] . "');";
	//insert row
	$result=mysql_query($query);
	}

	$row++;
		//loop to next row

	fclose($handle);
	//commit changes
	$query = "commit;";
	$result=mysql_query($query);
}

//disconnect
include 'LDclosedb.php';
?> 
