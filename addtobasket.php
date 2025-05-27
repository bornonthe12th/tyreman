<?php
// include error class
require 'tmanerror.inc';
// include connect class (must define $conn as mysqli)
include 'B2Bconnect.php';

// start session
//session_start();
$session = session_id();
$error = false;

// validate session customer ID
if (!isset($_SESSION['customerid'])) {
	tError('Missing parameter: cust', 'addtobasket.php');
	$error = true;
} else {
	$cust = $_SESSION['customerid'];
}

// validate GET params
$prodid = $_GET['productid'] ?? null;
$qty = $_GET['qty'] ?? 1;
$price = $_GET['price'] ?? null;

if (!$prodid) {
	tError('Missing parameter: productid', 'addtobasket.php');
	$error = true;
}

if (!$price) {
	tError('Missing parameter: price', 'addtobasket.php');
	$error = true;
}

if (!$error) {
	// sanitize values
	$session = $conn->real_escape_string($session);
	$prodid = $conn->real_escape_string($prodid);
	$qty = (int)$qty;
	$price = (float)$price;

	// prepare query
	$query = "CALL AddToBasket('$session', $cust, '$prodid', $qty, $price)";
	$result = $conn->query($query);

	if (!$result) {
		tError("Database error: " . $conn->error, 'addtobasket.php');
	}

	include 'B2Bclosedb.php';
	session_write_close();
	header("Location: B2BViewBasket.php");
	exit;
} else {
	if (isset($conn)) {
		$conn->close();
	}
	session_write_close();
	header("Location: B2BLogin.php?error=S");
	exit;
}
?>
