<?php
	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
        //include global functions class
        include 'B2BFunctions.php';

	//get session id
	$session = session_id();	
	$error = 'N';
	if (!isset($session)) {
		terror('Missing sessionid','B2BProcessOrder.php');
		$error = 'Y';
	}
	//get cust
	if (isset($_SESSION['customerid'])) {
		$cust = $_SESSION['customerid'];
	} else {
		terror('Missing parameter cust','B2BProcessOrder.php');
		$error = 'Y';
	}
	//get company 
	if (isset($_SESSION['companyid'])) { 
		$companyid = $_SESSION['companyid'];
	} else {
		terror('Missing parameter company','B2BProcessOrder.php');
		$error = 'Y';
	}
	
	//get orderref
	if (isset($_POST['ordref'])) {
		$order_ref = $_POST['ordref'];
	} else {
		$order_ref = "";
	}
	
	if ($error !== 'Y'){
		
		if (!IsBasketEmpty($cust,$session))
                {
		//call processOrder
		//set up query
		//RECONNECT
		include 'Reconnect.php';
		$query="call ProcessOrder('$session',$cust,'$order_ref');";	

		//clear order reference
		$order_ref = '';
		$_POST['order_ref'] = '';
		
		//run query
		$result=mysql_query($query);
		
		$order_id = mysql_result($result,0,"order_id");
		$_SESSION['orderid'] = $order_id; // store order id to session data
		
		//RECONNECT
		include 'Reconnect.php';
		//write to order file
		//include write header file class
		include 'B2BWriteHeader.php';
		//RECONNECT
		include 'Reconnect.php';
		//lines
		include 'B2BWriteLines.php';
	
                //$FilNam = str_pad($prefix,3,' ',STR_PAD_RIGHT).str_pad($order_id, 8, "0", STR_PAD_LEFT) . ".orh";
		//$output = shell_exec("/usr/local/bin/TestOrderComplete.ksh $FilNam 2>&1");
    		//if (trim($output) == 'FAIL') {
        		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=B2BOrderIncomplete.php\">";
			//exit();	
        		//}

	
		//RECONNECT
		include 'Reconnect.php';
		//empty basket
		$query="call EmptyBasket('$session',$cust);";	 
		//run query
		$result=mysql_query($query);
		//go to order complete screen
		$URL="B2BOrderConfirm.php?ord=" . $OrderId;
		} else {
		$URL="B2BProdSearch.php";
		}
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
