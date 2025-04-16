<?php

//include error class
require 'tmanerror.inc';

//include usersdb settings
require 'b2busersconfig.inc';

//include global functions class
require 'B2BFunctions.php';

session_start();

//copy parms to local vars
$username = $_SESSION['dbusername'];
$password = $_SESSION['dbpassword'];

//connect to users
$conn = mysql_connect($dbhost, $dbuser, $dbpass,'false',65536)
                or die('Error connecting to mysql');
        mysql_select_db($dbname);

//copy parms to local vars
$uid = $_SESSION['uid'];
$query="call B2BUpdateDate('$uid');";
$result=mysql_query($query);

$URL="B2BProdSearch.php";
header ("Location: $URL");

//disconnect from usersdb
mysql_close($conn);
session_write_close();

?>
