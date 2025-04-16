<?php

	//include error class
	require 'tmanerror.inc';

	//include usersdb settings
	require 'b2busersconfig.inc';

	//include global functions class
	require 'B2BFunctions.php';
	
  	//copy parms to local vars
  	$username = $_POST['username'];
  	$password = $_POST['password'];

  	//connect to users
	$conn = mysql_connect($dbhost, $dbuser, $dbpass,'false',65536) 
	        or die('Error connecting to mysql');
	mysql_select_db($dbname);

  	//set up query
  	$query="call B2BLogin('$username','$password');";
	 	
  	//run query
  	$result=mysql_query($query);
	$num=mysql_num_rows($result);

	
	
	if ($num==1)
        {
		//user found
  		$i=0;

  		session_start(); // start up your PHP session! 

  		while ($i < $num)
               {
			//set session vars
	  		$_SESSION['dbusername'] = mysql_result($result,$i,"DBuserName"); 
	  		$_SESSION['dbpassword'] = mysql_result($result,$i,"DBpassword"); 
	  		$_SESSION['dbschema'] = mysql_result($result,$i,"DBschema"); 
	  		$_SESSION['customerid'] = mysql_result($result,$i,"customer_id"); 
	  		$_SESSION['stylesheet'] = mysql_result($result,$i,"stylesheet"); 
	  		$_SESSION['printstylesheet'] = mysql_result($result,$i,"printstylesheet"); 
	  		$_SESSION['description'] =  mysql_result($result,$i,"description"); 
	  		$_SESSION['companyid'] =  mysql_result($result,$i,"company_id"); 
			$_SESSION['uid']=$username; // 120110 AS
		  		
  		    $i++;

		}
			
				
		//disconnect from usersdb
  		mysql_close($conn);
		
  		/* connect to db (php GetDefaultBranch function defined in B2BFunctions.inc 
		included within this script) and then get default branch */

  		$_SESSION['default_branch'] = GetDefaultBranch($_SESSION['customerid']); 
  		$_SESSION['selected_branch'] = GetDefaultBranch($_SESSION['customerid']); 
		// bodged select_branch to stop promotions page bugging - test box only - ALS 30/10/19
		// store session data  		  		

	
  		 		
  		//go to search
  		// Change to the URL you want to redirect to
  		tError("User logged on ",$username." - ".$password." login.php");
		$URL="B2BUpdateDate.php";
		session_write_close();
		header ("Location: $URL");

		} else {

  		//user not found, write to log
  		tError("User not found ",$username." - ".$password." login.php");
  		//disconnect from usersdb
  		mysql_close($conn);
  		//go back to login
  		$URL="B2BLogin.php?error=Y";
  		session_write_close();
		header ("Location: $URL");
	}

?> 
