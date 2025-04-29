<?php
// Include classes
include 'tmanerror.inc';
include 'B2Bconnect.php';

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Get session ID
$session = session_id();
if (!$session) {
	terror('Lost Session ID', 'B2BEmptyBasket.php');
	exit();
}

// Initialize error flag
$error = 'N';

// Get customer ID
if (isset($_SESSION['customerid'])) {
	$cust = (int)$_SESSION['customerid'];
} else {
	terror('Missing parameter cust', 'B2BEmptyBasket.php');
	$error = 'Y';
}

if ($error !== 'Y') {
	// Escape session ID
	$session_safe = $conn->real_escape_string($session);

	// Set up query
	$query = "CALL EmptyBasket('$session_safe', $cust)";

	// Run query
	if (!$conn->query($query)) {
		error_log('EmptyBasket query error: ' . $conn->error);
		terror('Error emptying basket', 'B2BEmptyBasket.php');
		exit();
	}

	// Close database connection
	include 'B2Bclosedb.php';

	// Go back to search page
	session_write_close();
	header("Location: B2BProdSearch.php");
	exit();

} else {
	// Problem with session
	$conn->close();
	session_write_close();
	header("Location: B2BLogin.php?error=S");
	exit();
}
?>
