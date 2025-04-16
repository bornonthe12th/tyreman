#!/usr/bin/php
<?php

if(isset($_GET['company']))                     // Is the script is being run through a browser
  $company_id = $_GET['company'];
else
  if(isset($_SERVER["argv"][1]))               // If the script is being run direct from Linux server
    $company_id = $_SERVER["argv"][1];

//include error class
include 'tmanerror.php';
include 'LDconfig.php';
include 'LDopendb.php';


//read list of files
// open this directory

$dirname =  "load/".$company."/";
echo $dirname . "\n";

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

if (substr("$dirArray[$index]", 0, 1) != ".")
{ // don't list hidden files
	
	$row = 1;
	$filename = $dirname . $dirArray[$index];
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[2])>4))
        {
	    $setstmnt = '';
		$num = count($data);
		//get branch
		$query = "select branch_id from branches where branch_code = '".$data[1]."';";
		//echo $query . "</br>";
		$result = mysql_query($query);
		$num=mysql_numrows($result);
	    if ($num > 0) {
	    	$branch = mysql_result($result,0,"branch_id");
    	} else {
    		$branch = "";
		}
		if ($branch !=""){
		    $query = "select count(*) rowcount from stock where Stockcode ='" . $data[2] . "' and branch_id = '".$branch."';";
		    //echo $query . "</br>";
		    $result = mysql_query($query);
		    //echo mysql_result($result,0,"rowcount") . "</br>"; 
		    if (mysql_result($result,0,"rowcount") > 0) {  //update
			    //set up query
				$updstmnt="update stock s ";	
			     //first col is stock code
		        $whrstmnt = "where s.Stockcode ='" . $data[2] . "' and branch_id='" .$branch. "';";
		    	$setstmnt = $setstmnt ."set s.stocklevel='" . $data[8] . "'";
		    	$setstmnt = $setstmnt ." , s.compstk='" . $data[10] . "'";
			    //build query
			    $query = $updstmnt . $setstmnt . $whrstmnt;
			    //echo $query . "\n";
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
