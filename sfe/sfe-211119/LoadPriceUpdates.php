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
$cust="";
$prod="";

//read list of files
// open this directory 


$dirname =  "load/".$company."/";
$myDirectory = opendir($dirname);
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .qtp files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".QTP"){
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
	
	$row = 1;
	$filename = $dirname . $dirArray[$index];
	echo $filename . "\n";
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2)) {
		
	    $num = count($data);
	    //find product_id
	    $query = "select product_id from stock where Stockcode ='" . rtrim($data[2]) . "' limit 1;";
	    $result = mysql_query($query);
	    $num=mysql_num_rows($result);
	    if ($num > 0) {
	    	$prod=mysql_result($result,0,"product_id");
    	} else {
	    	$prod = "";	
    	}
	    //find customer_id
	    $query = "select customer_id from customers where account_no ='" . rtrim($data[1]) . "' limit 1;";
	    //echo $query . "</br>";
	    $result = mysql_query($query);
	    $num=mysql_numrows($result);
	    if ($num > 0) {
	    	$cust=mysql_result($result,0,"customer_id");
    	} else {
    	 	$cust="";
	    }
	    //echo $cust . " - " . $prod . "</br>";
	    if (($cust) and ($prod)){
		    //update price
		    //do we insert or update?
		    $query = "select count(*) rowcount from prices where customer_id ='" . $cust . "' and product_id='" .$prod. "';";
		    $result = mysql_query($query);
		    
		    if (mysql_result($result,0,"rowcount") > 0) {  //update
			    //set up query
				$updstmnt="update prices p ";	
			     //first col is stock code
		        $whrstmnt = "where p.product_id ='" . $prod . "' and p.customer_id='" .$cust. "';";
		    	$setstmnt = "set p.netprice='" . $data[6] . "',";
		    	$setstmnt = $setstmnt ."p.stocklevel='" . $data[4] . "',";
		    	$setstmnt = $setstmnt ."p.old=p.netprice ";
	
			    //build query
			    $query = $updstmnt . $setstmnt . $whrstmnt;
				//echo $query . "</br>";
			    //update row
				$result=mysql_query($query);
	    	} else { //insert
			    //set up query
			}
		}
		$row++;
		//usleep(100000);
		// ALS 10/08/10
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
		unlink($filename);
	}
}


//disconnect
include 'LDclosedb.php';
?> 
