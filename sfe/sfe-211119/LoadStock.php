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
echo $dirname;
$myDirectory = opendir($dirname);
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .STK files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".STK"){
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
if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
	
	//mark all as deleted
	$query = "update stock set status = 'X';";
	$result = mysql_query($query);
	
	$row = 1;
	$filename = $dirname . $dirArray[$index];
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2))
        {
//            $data[6] = str_replace("'","",$data[6]);  // Remove the single quotes around the product type as these get added by this script STEVE C 28 10 2009

        
	    $num = count($data);
		//get branch
		$query = "select branch_id from branches where branch_code = '".$data[1]."';";
		$result = mysql_query($query);
		$num=mysql_numrows($result);
	    if ($num > 0) {
	    	$branch = mysql_result($result,0,"branch_id");
    	} else {
    		$branch = "";
		}
		if ($branch !=""){
		    //do we insert or update?
		    $query = "select count(*) rowcount from stock where Stockcode ='" . $data[2] . "' and branch_id = '".$branch."';";
		    $result = mysql_query($query);
		    //echo mysql_result($result,0,"rowcount") . "</br>"; 
		    if (mysql_result($result,0,"rowcount") > 0) {  //update
			    //set up query
				$updstmnt="update stock s ";	
			     //first col is stock code
		        $whrstmnt = "where s.Stockcode ='" . $data[2] . "' and branch_id='" .$branch. "';";
		    	$setstmnt = "set s.description='" . $data[3] . "',";
		    	$setstmnt = $setstmnt ."s.productgroup='" . $data[4] . "',";
		    	$setstmnt = $setstmnt ."s.manufacturer='" . $data[5] . "',";
		    	$setstmnt = $setstmnt ."s.producttype='" . $data[6] . "',";
		    	$setstmnt = $setstmnt ."s.stocklevel='" . $data[7] . "',";
		    	$setstmnt = $setstmnt ."s.size='" . $data[8]  . "',";
		    	$setstmnt = $setstmnt ."s.highlight='" . $data[9]  . "',";
		    	$setstmnt = $setstmnt ."s.status='A'";
	
			    //build query
			    $query = $updstmnt . $setstmnt . $whrstmnt;
			    //update row
				$result=mysql_query($query);
	    	} else { //insert
			    //set up query
				$query="insert into stock (branch_id,stockcode,description,productgroup,manufacturer,producttype,stocklevel,status,size,highlight) values (";	
			    $query = $query . "'" . $branch . "',"; 
			    $query = $query . "'" . $data[2] . "',"; 
			    $query = $query . "'" . $data[3] . "',"; 
			    $query = $query . "'" . $data[4] . "',"; 
			    $query = $query . "'" . $data[5] . "',"; 
			    $query = $query . "'" . $data[6] . "',"; 
			    $query = $query . "'" . $data[7] . "','A', ";
			    $query = $query . "'" . $data[8] . "',"; 
			    $query = $query . "'" . $data[9] . "');"; 
			    //run query to insert row
			    $result=mysql_query($query);
			}
		}
		$row++;
	}
	fclose($handle);
	//commit changes
	$query = "commit;";
	$result=mysql_query($query);
}
//for($index=0; $index < $indexCount; $index++) {
	//if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		//delete all files
		//$filename = $dirname . $dirArray[$index];
		//unlink($filename);
	//}
//}


//disconnect
include 'LDclosedb.php';
?> 
