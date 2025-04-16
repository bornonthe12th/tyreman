<?php
//session_start();

//include error class
require 'tmanerror.inc';

//include usersdb settings
require 'b2busersconfig.inc';

//include global functions class
require 'B2BFunctions.php';

//connect to users
$conn = mysql_connect($dbhost, $dbuser, $dbpass,'false',65536)
                or die('Error connecting to mysql');
        mysql_select_db($dbname);

session_start();

$query="call GetResource('alt_image');";
$result=mysql_query($query);
var_dump($result); echo "<br/>\n";
$_SESSION['altimage'] = $result;


?>
