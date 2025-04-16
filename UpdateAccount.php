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
	//set IncVATFlag
	if (isset($_GET['incVATFlag'])) {
		$IncVATFlag = $_GET['incVATFlag'];
	} else {
		$IncVATFlag = "";	
	}
	//set DefToSellFlag
	if (isset($_GET['DefToSellFlag'])) {
		$DefToSellFlag = $_GET['DefToSellFlag'];
	} else {
		$DefToSellFlag = "";	
	}


       //set RRP

	if (isset($_GET['Show_rrp'])) {
		$Show_rrp = $_GET['Show_rrp'];
	} else {
		$Show_rrp = "";	
	}



        //Set RRP4

	if (isset($_GET['Show_rrp4'])) {
		$Show_rrp4 = $_GET['Show_rrp4'];
	} else {
		$Show_rrp4 = "";	
	}







	
	if ($error !== 'Y'){
		
		//call UpdateAccount
		//set up query
		$query="call UpdateAccount($cust,'$email','$markuppc','$markupval','$IncVATFlag','$DefToSellFlag','$Show_rrp','$Show_rrp4');";	

		//run query
		$result=mysql_query($query);
		
		//include reconnect class
		include 'Reconnect.php';
			
		//go back to account 
		$URL="B2BAccount.php";
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
