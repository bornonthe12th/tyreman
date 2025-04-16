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

//connect to customer DB.
$cust_db_conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname,65536)
		or die('unable to connect to b2b DB');

//setup query
$query = "SELECT * FROM priceint";

//run query
$result=mysqli_query($cust_db_conn,$query);
$num=mysqli_num_rows($result);
$row = mysqli_fetch_array($result);

	if ($num>0)
	    {
	    //find product_id
	    $query = 'select product_id from stock where Stockcode = $row["f3"] limit 1;';
		var_dump($query);
	    
      	    //run query
            $result=mysqli_query($cust_db_conn,$query);
            $num=mysqli_num_rows($result);
            $row = mysqli_fetch_array($result);

	    if ($num > 0) 
		{
	    	$prod=$row["product_id"];
		} else {
	    	$prod = "";	
    			}

var_dump($prod);
	    }

//disconnect
include 'LDclosedb.php';
?> 
