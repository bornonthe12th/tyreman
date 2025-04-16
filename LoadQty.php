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
	//only include .QTY files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".QTY"){
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

//tutn off autocommit - ALS 101111
$query = "SET autocommit=0;";
$result=mysql_query($query);

if (substr("$dirArray[$index]", 0, 1) != ".")
{ // don't list hidden files
	
	$row = 1;
	$filename = $dirname . $dirArray[$index];
	echo $filename . "\n";
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[2])>4))
        {
	    $setstmnt = '';
		$num = count($data);
		//get branch
		$query="select branch_id from branches where branch_code = '".$data[1]."';";
		//usleep(200000);
		$result=mysql_query($query) or die( 'Error: ' . mysql_error() );
		$num=mysql_num_rows($result) or die( 'Error: ' . mysql_error() );
		// added die statement ALS 10/08/10
	    if ($num > 0) {
	    	$branch = mysql_result($result,0,"branch_id");
    	} else {
    		$branch = "";
		}
		if ($branch !=""){
		    $query = "select count(*) rowcount from stock where Stockcode ='" . $data[2] . "' and branch_id = '".$branch."';";
		    $result = mysql_query($query);
		    if (mysql_result($result,0,"rowcount") > 0) {  //update
			    //set up query
				$updstmnt="update stock s ";	
			     //first col is stock code
		        $whrstmnt = "where s.Stockcode ='" . $data[2] . "' and branch_id='" .$branch. "';";
		    	$setstmnt = $setstmnt ."set s.stocklevel='" . $data[7] . "'";
		    	$setstmnt = $setstmnt ." , s.compstk='" . $data[9] . "'";
		    	$setstmnt = $setstmnt ." , s.regionstk='" . $data[10] . "'";
			    //build query
			    $query = $updstmnt . $setstmnt . $whrstmnt;
			    //update row
				$result=mysql_query($query);
	    	}
		}
		$row++;
		//loop to next row
	}
	fclose($handle);
	//commit changes
	$query = "commit;";
	$result=mysql_query($query);
}
for($index=0; $index < $indexCount; $index++) {
	if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		//delete all files
		$filename = $dirname . $dirArray[$index];
		//unlink($filename);
	}
}


//disconnect
include 'LDclosedb.php';
?> 
