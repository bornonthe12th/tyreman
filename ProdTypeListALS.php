<!DOCTYPE html>
<html>
<body>

<?php

$query="call ProdTypeList();";
$host = "localhost";
@ $db = new mysqli($host, root, syslib, b2bbabush);

if(mysqli_connect_errno())
{
    die("Connection could not be established");
}

$sptype = '';

$result=$db->query($query);

$total_num_rows = $result->num_rows;
?>

<li>Product Type</li>

<?php
echo "$total_num_rows <br>";
echo "The Results Are : <br>";

while($row = $result->fetch_array())
{
	$scrp = substr(trim($row['producttype']),0,18);
	$scrp .= "<br>";
    	echo $scrp;
}

?>

</body>
</html>
