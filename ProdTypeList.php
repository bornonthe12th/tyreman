<?php

$query="call ProdTypeList();";
$username = $_SESSION['dbusername'];
$password = $_SESSION['dbpassword'];
$dbname = $_SESSION['dbschema'];
$host = "localhost";
@ $db = new mysqli($host, $username, $password, $dbname);

$result=$db->query($query);

$total_num_rows = $result->num_rows;

$dropdown = "<select name='sptype'>";

while ( $row = $result->fetch_array()) 
	{
          $dropdown .= "\r\n<option value='{$row['producttype']}'>{$row['producttype']}</option>";
        }
        $dropdown .= "\r\n</select>";
        echo $dropdown;

$_SESSION['sptype']=$sptype
?>
