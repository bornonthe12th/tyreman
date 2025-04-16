<?php

$host = "localhost";
$username = "root";
$password = "syslib";
$dbname = "b2bbabush";

@ $db = new mysqli($host, $username, $password, $dbname);

if(mysqli_connect_errno())
{
    die("Connection could not be established");
}

$query = "call ProdTypeList();";
$result = $db->query($query);

$total_num_rows = $result->num_rows;

echo "The Results Are : <br>";
$dropdown = "<select name='producttype'>";
foreach ($result as $row) {
	  $dropdown .= "\r\n<option value='{$row['producttype']}'>{$row['producttype']}</option>";
	}
	$dropdown .= "\r\n</select>";
	echo $dropdown;

?>
