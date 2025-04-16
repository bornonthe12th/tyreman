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

$product = "";

//read list of files
// open this directory
$dirname =  "load/".$company."/";
$myDirectory = opendir($dirname);
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .STK files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".ALT"){
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
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[1])>4)) {
	    $num = count($data);
	    
	    //does it exist already?
	    $query = "select a.product_id from altstockcodes a,stock s where a.product_id = s.product_id and a.stockcode='".rtrim($data[1])."' and s.stockcode='".$data[2]."' limit 1;";
	    //echo $query . "</br>";
	    $result = mysql_query($query) or die(mysql_error());
	    $num = mysql_num_rows($result);
	    //echo $num . "</br>";
	    if ($num == 0) {
			//doesnt exist so insert
	    	//find product id
	    	$query = "select product_id from stock s where s.stockcode='".rtrim($data[2])."' limit 1;";
	    	//echo $query . "</br>";
	    	$result = mysql_query($query);
		    if (mysql_num_rows($result) > 0) {
			    $product = mysql_result($result,0,"product_id");
	    		//product found so insert rec
		    	//set up query
				$query="insert into altstockcodes (product_id,stockcode) values (";	
				$query = $query . "'" . $product . "',"; 
		    	$query = $query . "'" . rtrim($data[1]) . "');"; 
		    	//echo $query . "</br>";
		    	//run query to insert row
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
