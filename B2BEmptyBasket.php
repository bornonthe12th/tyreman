<?php
	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//session_start(); // start up your PHP session! 
	//get session id
	$session = session_id();
	if (!isset($session)) {
		terror('Lost Session ID','B2BEmptyBasket.php');
		$error = 'Y';
	}
	//get product qty price cust
	$error = 'N';
	if (isset($_SESSION['customerid'])) {
		$cust = $_SESSION['customerid'];
	} else {
		terror('Missing parameter cust','B2BEmptyBasket.php');
		$error = 'Y';
	}
	
	if ($error !== 'Y'){
		
		//call addtobasket
		//set up query
		$query="call EmptyBasket('$session',$cust);";	
		
		//run query
		//echo $query;
		$result=mysql_query($query);
		
		//include closedb class
		include 'B2Bclosedb.php';
		//go back to search 
		$URL="B2BProdSearch.php";
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
