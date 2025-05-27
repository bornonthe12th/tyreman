<?php
include 'tmanerror.inc';
include 'B2Bconnect.php';
include 'B2BFunctions.php';
include 'Reconnect.php';

$session = session_id();
$error = 'N';

// Validate session
if (!$session) {
	terror('Missing session id', 'B2BProcessOrder.php');
	$error = 'Y';
}

// Validate customer
$cust = $_SESSION['customerid'] ?? null;
if (!$cust) {
	terror('Missing parameter cust', 'B2BProcessOrder.php');
	$error = 'Y';
}

// Validate company
$companyid = $_SESSION['companyid'] ?? null;
if (!$companyid) {
	terror('Missing parameter company', 'B2BProcessOrder.php');
	$error = 'Y';
}

// Get order reference
$order_ref = $_POST['ordref'] ?? '';

if ($error !== 'Y') {

	// Check basket
	if (!IsBasketEmpty($cust, $session)) {

		include 'Reconnect.php';

		// Escape order_ref safely
		$orderRefEsc = $conn->real_escape_string($order_ref);

		// Process order
		$query = "CALL ProcessOrder('$session', $cust, '$orderRefEsc');";
		$result = $conn->query($query);

		if (!$result) {
			terror('Error processing order: ' . $conn->error, 'B2BProcessOrder.php');
			exit;
		}

		$row = $result->fetch_assoc();
		$order_id = $row['order_id'] ?? 0;

		if (empty($order_id)) {
			terror('Order processing failed: no order ID returned.', 'B2BProcessOrder.php');
			exit;
		}

		$_SESSION['orderid'] = $order_id; // store order id into session

		// Write header file
		include 'Reconnect.php';
		include 'B2BWriteHeader.php';

		// Write lines
		include 'Reconnect.php';
		include 'B2BWriteLines.php';

		// Empty basket
		include 'Reconnect.php';
		$query = "CALL EmptyBasket('$session', $cust);";
		if (!$conn->query($query)) {
			terror('Error emptying basket: ' . $conn->error, 'B2BProcessOrder.php');
			exit;
		}

		// Go to order confirmation
		$URL = "B2BOrderConfirm.php?ord=" . (int)$order_id;

	} else {
		// Basket already empty
		$URL = "B2BProdSearch.php";
	}

	session_write_close();
	header("Location: $URL");
	exit;

} else {
	if ($conn) {
		$conn->close();
	}
	session_write_close();
	header("Location: B2BLogin.php?error=S");
	exit;
}
?>
