<?php
// Stop browser cache errors when clicking back button
ini_set('session.cache_limiter', 'private');

// Include classes
include 'tmanerror.inc';
include 'B2Bconnect.php';

// Get session ID
$session = session_id();

// Initialize
$error = 'N';

// Get customer ID
if (isset($_SESSION['customerid'])) {
	$cust = (int)$_SESSION['customerid'];
} else {
	terror('Missing parameter cust', 'UpdateBasket.php');
	$error = 'Y';
}

// Get line count
$num = isset($_POST['linecount']) ? (int)$_POST['linecount'] : 0;

if ($error !== 'Y') {
	// Loop through each line
	for ($i = 0; $i < $num; $i++) {
		$qty = isset($_POST["qty" . $i]) ? (int)$_POST["qty" . $i] : 1;
		$prodid = isset($_POST["prodid" . $i]) ? (int)$_POST["prodid" . $i] : 1;

		// Build query safely
		$session_safe = $conn->real_escape_string($session);
		$query = "CALL UpdateBasket('$session_safe', $cust, '$prodid', '$qty')";

		if (!$conn->query($query)) {
			error_log('UpdateBasket query error: ' . $conn->error);
		}

		// **Reconnect if needed** - only once at start would be better, but keeping your pattern
		include 'Reconnect.php';
	}

	session_write_close();

	// Updated redirect logic
	if (isset($_POST['process']) && strlen($_POST['process'])) {
		header("Location: B2BOrderRef.php");
		exit();
	} elseif (isset($_POST['prdsrch']) && strlen($_POST['prdsrch'])) {
		header("Location: B2BJump.php");
		exit();
	} else {
		header("Location: B2BViewBasket.php");
		exit();
	}

} else {
	// Session/customer ID problem
	$conn->close();
	$URL = "B2BLogin.php?error=S";
	session_write_close();
	header("Location: $URL");
	exit();
}

include 'B2Bclosedb.php';
?>
