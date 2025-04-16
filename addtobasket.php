<?php
	//include error class
	require 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//session_start(); // start up your PHP session! 
	//get session id
	$session = session_id();
	//get product qty price cust
	$error = 'N';
	if (isset($_SESSION['customerid'])) 
	   {
		$cust = $_SESSION['customerid'];
	   } else {
		terror('Missing parameter cust','addtobasket.php');
		$error = 'Y';
	   }

	if (isset($_GET['productid'])) 
	   {
		$prodid = $_GET['productid'];
	
	   } else {
		terror('Missing parameter productid','addtobasket.php');
		$error = 'Y';
	
	   }

	if (isset($_GET['qty'])) 
	   {
		$qty = $_GET['qty'];
	
	   } else {
		$qty = 1;
	
	   }

	if (isset($_GET['price'])) 
	   {
		$price = $_GET['price'];
	
	   } else {
		terror('Missing parameter price','addtobasket.php');
		$error = 'Y';
	
	   }

	if ($error !== 'Y')
	   {
		//call addtobasket
		//set up query
		$query="call AddToBasket('$session',$cust,'$prodid','$qty','$price');";	
		//run query
		//echo $query;
		$result=mysql_query($query);
		
		//include closedb class
		include 'B2Bclosedb.php';
		//go back to search 
		$URL="B2BViewBasket.php";
		session_write_close();
		header ("Location: $URL");	
	   } else {
		//problem with session
		//disconnect from usersdb
  		mysql_close($conn);
  		//go to login
  		// Change to the URL you want to redirect to
		$URL="B2BLogin.php?error=S";
		session_write_close();
		header ("Location: $URL");	
	   }
?>
