<?php session_start();

//connect to users DB
$cust_db_conn = mysqli_connect("localhost", $_SESSION['dbusername'] ,$_SESSION['dbpassword'], "b2busers")
                                or die('Error connecting to MySQL server.');

//copy parms to local vars
$uid = $_SESSION['uid'];
$query="call B2BUpdateDate('$uid');";
$result=mysqli_query($cust_db_conn,$query);

//disconnect from usersdb
mysqli_close($cust_db_conn);

$URL="B2BUpdateAudit.php";
header ("Location: $URL");

?>
