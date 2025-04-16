<?php
	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//get session id
	$session = session_id();
	//get account fields
	$error = 'N';
	if (isset($_SESSION['customerid'])) {
		$cust = $_SESSION['customerid'];
	} else {
		terror('Missing parameter cust','UpdateAccount.php');
		$error = 'Y';
	}

	//set email
	if (isset($_GET['email'])) {
		$email = $_GET['email'];
	} else {
		$email = "";	
	}
	//set markuppc
	if (isset($_GET['markuppc'])) {
		$markuppc = $_GET['markuppc'];
	} else {
		$markuppc = "";	
	}
	//set markupval
	if (isset($_GET['markupval'])) {
		$markupval = $_GET['markupval'];
	} else {
		$markupval = "";	
	}
	
	if ($error !== 'Y'){
		
		//call UpdateAccount
		//set up query
		$query="call UpdateMarkup($cust,'$markuppc','$markupval');";	

		//run query
		$result=mysql_query($query);
		
		//include reconnect class
		include 'Reconnect.php';
			
		//go back to account 
		$URL="B2BMarkups.php";
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
	//include closedb class
	include 'B2Bclosedb.php';
?>
