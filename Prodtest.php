<?php
//open connection
$conn = mysqli_connect("localhost", "root", "syslib");
echo "database = ";
$result = mysqli_query($conn,"SHOW DATABASES"); while ($row =
mysqli_fetch_array($result)) { echo $row[0]."<br>"; }

$conn = mysqli_connect("localhost", "root", "syslib", "b2bbabush");
echo "tables = ";
$result = mysqli_query($conn,"SHOW TABLES"); while ($row =
mysqli_fetch_array($result)) { echo $row[0]."<br>"; }

##################################################################

echo "Prod types = ";
$query="call ProdTypeList();";
$result = mysqli_query($conn,$query); while ($row =
mysqli_fetch_array($result)) { echo $row[0]."<br>"; }

?>

