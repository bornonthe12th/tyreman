#!/usr/bin/php
<?php
if(isset($_GET['company']))                     // Is the script is being run through a browser
  $company_id = $_GET['company'];
else
  if(isset($_SERVER["argv"][1]))               // If the script is being run direct from Linux server
    $company_id = $_SERVER["argv"][1];

//open connection
$conn = mysql_connect("localhost", "root", "syslib");
//select database
mysql_select_db($company_id,$conn);
// create sql statement
$sql = "SELECT order_lines.order_id, qty, price, orders.order_date , stock.stockcode, stock.description from order_lines , orders , stock where stock.product_id = order_lines.product_id and orders.order_id = order_lines.order_id";
//execute sql
$result = mysql_query($sql,$conn) or die(mysql_error());
// get no. of rows in result
$num = mysql_num_rows($result);
echo " Rows = $num"."\n";
// loop results and disp.
while ($workArray = mysql_fetch_array($result)) {
	$order_id = $workArray['order_id'];
	$qty = $workArray['qty'];
	$price = $workArray['price'];
	$stockcode = $workArray['stockcode'];
	$description = $workArray['description'];
	$date = $workArray['order_date'];
	$Cdate = substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,0,4);
	$Htime = substr($date,11,2);
	$Mtime = substr($date,14,2);
	echo "$order_id ,$qty ,$price ,$stockcode ,$description ,$date ,$Cdate ,$Htime ,$Mtime "."\n";
	}
?> 

