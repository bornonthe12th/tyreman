<?php
	//connect to the db using session variables	
	//check variables are set
	//copy to local vars
	session_start(); // start up your PHP session!
	if(!isset($_SESSION['dbusername']))
	   {
		header("Location: B2BLogin.php");
	   }
	$username = $_SESSION['dbusername'] ;
	$dbpassword = $_SESSION['dbpassword'] ;
	$dbschema = $_SESSION['dbschema'] ;
	$dbhost = "localhost";

//connect
$conn = mysql_connect($dbhost, $username, $dbpassword,'false',65536) 
        or die(tError('Unable to connect to b2b DB','B2BConnect.php'));
mysql_select_db($dbschema);
?>
