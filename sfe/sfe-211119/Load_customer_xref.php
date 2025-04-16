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

$query = "delete from customer_xref " ;
$result = mysql_query($query);

$dirname =  "load/".$company."/";
$myDirectory = opendir($dirname);
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .CLG files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".CLG"){
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
	var_dump($filename);
	$handle = fopen($filename, "r");

	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2))
        {
              foreach($data as $key => $value)
        
	    //find customer_id
	    $query = "select customer_id from b2busers.users where company_id =" .$company_id. " 
		      and Cust_Admin ='" . $data[1] . "' limit 1;";
	    $result = mysql_query($query);
	    $num=mysql_num_rows($result);
	    $cust = "";
	    if ($num > 0) {
	    	$cust=mysql_result($result,0,"customer_id");
    		} else {
    		$cust = "";
		}

		//insert customer_xref
		//var_dump($cust);
		$query="insert into customer_xref (Customer_id,UserName,Password,Account_No) values ";	
		$query = $query."('" . $cust . "',";
		$query = $query."'". $data[2] . "',";
		$query = $query."'". $data[3] . "',";
		$query = $query."'". $data[4] . "') ;"; 
		//insert row
		$result=mysql_query($query);
				
		$row++;
		//loop to next row
	}
	fclose($handle);

//commit changes
$query = "commit;";
$result=mysql_query($query);
						}

//disconnect
include 'LDclosedb.php';
?> 
