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



$RootDir = "/var/www/html/PHPB2B/";

echo "PHP update : \n";

$dirname =  "load/".$company."/";
$RootDir .= $dirname;
echo "Root Directory = ";
echo "$RootDir \n";
echo "Directory = ";
echo "$dirname \n";

$myDirectory = opendir($dirname);

// get each entry - read list of files
while($entryName = readdir($myDirectory)) {
	//only include .STK files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".STK"){
		$dirArray[] = $entryName;
	}
}
// close directory
closedir($myDirectory);

// count elements in array
$indexCount = count($dirArray);
// sort by date
sort($dirArray);
//only process latest file
$index = $indexCount-1;


echo "FileName = ";
$file = $RootDir . $dirArray[$index];
echo "$file \n";
echo "Records processed :- \n";

//only process latest file
if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
	
	//mark all as deleted
	$query = "delete from stock_master;";
	$result = mysql_query($query);
	
	$row = 1;
	$filename = $dirname . $dirArray[$index];
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2))
        {
        
	    $num = count($data);
			    //build query
                            $query="insert into stock_master (stockcode,wet_braking,fuel_efficiency,decibels,special,extraload,winter,runflat,noise_rating,vehicle_class,url,snow,ice_grip,ean) values (";
			    $query = $query . "'" . $data[2] .  "',"; 
			    $query = $query . "'" . $data[15] . "',"; 
			    $query = $query . "'" . $data[14] . "',"; 
			    $query = $query . "'" . $data[16] . "',"; 
			    $query = $query . "'" . $data[10] . "',"; 
			    $query = $query . "'" . $data[11] . "',";
			    $query = $query . "'" . $data[12] . "',"; 
                            $query = $query . "'" . $data[13] . "',";
                            $query = $query . "'" . $data[17] . "',";
                            $query = $query . "'" . $data[18] . "',";
                            $query = $query . "'" . $data[20] . "',";
                            $query = $query . "'" . $data[21] . "',";
                            $query = $query . "'" . $data[22] . "',";
                            $query = $query . "'" . $data[23] . "');";
			    //run query to insert row
			    $result=mysql_query($query);
		echo "\r $row";
		$row++;
	}
	fclose($handle);
	//commit changes
	$query = "commit;";
	$result=mysql_query($query);
						}
//disconnect
include 'LDclosedb.php';
?> 
