<?php

	/* line added to stop IE and Firefox errors displaying when the user clicks
	  <Back> button on browser from Basket - 06/06/08 */

	ini_set('session.cache_limiter','private');


	//include error class
	include 'tmanerror.inc';

	//include connect class
	include 'B2Bconnect.php';

	//get session id
	$session = session_id();

	//get price and cust
	$error = 'N';
	if (isset($_SESSION['customerid'])) {
		$cust = $_SESSION['customerid'];
	}
        else {
		terror('Missing parameter cust','UpdateBasket.php');
		$error = 'Y';
	}


	if (isset($_POST['linecount'])) {
		$num = $_POST['linecount'];
	}
        else {
		$num = 0;	
	}

	$i = 0;
	if ($error !== 'Y'){
		
		//loop round each line
		while ($i < $num) {
			//get the variables
			if (isset($_POST["qty" . $i])) {
				$qty = $_POST["qty" . $i];
			} else {
				$qty = 1;
			}
			if (isset($_POST["prodid" . $i])) {
				$prodid = $_POST["prodid" . $i];
			} else {
				$prodid = 1;
			}
			
			//call UpdateBasket
			//set up query
			$query="call UpdateBasket('$session',$cust,'$prodid','$qty');";	
			//run query
			$result=mysql_query($query);
			
			//include reconnect class
			include 'Reconnect.php';
			$i++;	
		}

		//go back to search 
		$URL="B2BViewBasket.php";
		session_write_close();



                // Updated the following code 16 11 2009 Steve C so any button pressed on the View Basket page will update basket contents

                if(isset($_POST['process']) && strlen($_POST['process']))
                {
                  header("Location: B2BOrderRef.php");
                  exit();
                }
                
                else  if(isset($_POST['prdsrch']) && strlen($_POST['prdsrch']))
                {
                  header("Location: B2BJump.php");
                  exit();
                }



		header ("Location: B2BViewBasket.php");		
		exit();





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
