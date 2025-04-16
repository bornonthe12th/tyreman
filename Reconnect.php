<?php

//print_r($_SESSION);


if (isset($conn))
{

	//disconnect
	mysql_close($conn);
}


else
{


	$username = $_SESSION['dbusername'];
	$dbpassword = $_SESSION['dbpassword'];
	$dbschema = $_SESSION['dbschema'];
	$dbhost = "localhost";


}

// for debugging: echo "<br /><br /><br />" . $dbhost . " " . $username . " " .  $dbpassword;


//reconnect

$conn = mysql_connect($dbhost, $username, $dbpassword,'false',65536) or die(tError('Unable to connect to b2b DB','ReConnect.php'));

mysql_select_db($dbschema) OR die("Can't select database $dbschema");
?>
