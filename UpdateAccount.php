<?php
include 'tmanerror.inc';
include 'B2Bconnect.php';
include 'Reconnect.php';

// Start session and get session id
$session = session_id();

// Initialize error flag
$error = 'N';

// Validate session
if (isset($_SESSION['customerid'])) {
	$cust = (int)$_SESSION['customerid'];
} else {
	terror('Missing parameter cust', 'UpdateAccount.php');
	$error = 'Y';
}

// Gather and sanitize input from POST
$email = $_POST['email'] ?? '';
$markuppc = $_POST['markuppc'] ?? '';
$markupval = $_POST['markupval'] ?? '';
$IncVATFlag = $_POST['incVATFlag'] ?? '';
$DefToSellFlag = $_POST['DefToSellFlag'] ?? '';
$Show_rrp = $_POST['Show_rrp'] ?? '';
$Show_rrp4 = $_POST['Show_rrp4'] ?? '';

// Escape user inputs safely
$emailEsc = $conn->real_escape_string($email);
$markuppcEsc = $conn->real_escape_string($markuppc);
$markupvalEsc = $conn->real_escape_string($markupval);
$IncVATFlagEsc = $conn->real_escape_string($IncVATFlag);
$DefToSellFlagEsc = $conn->real_escape_string($DefToSellFlag);
$Show_rrpEsc = $conn->real_escape_string($Show_rrp);
$Show_rrp4Esc = $conn->real_escape_string($Show_rrp4);

if ($error !== 'Y') {
	// Build safe query
	$query = "CALL UpdateAccount($cust, '$emailEsc', '$markuppcEsc', '$markupvalEsc', '$IncVATFlagEsc', '$DefToSellFlagEsc', '$Show_rrpEsc', '$Show_rrp4Esc');";

	// Run query
	if (!$conn->query($query)) {
		terror('Database error updating account: ' . $conn->error, 'UpdateAccount.php');
		exit;
	}

	// Close connection
	$conn->close();

	// Redirect back to account page
	session_write_close();
	header("Location: B2BAccount.php");

} else {
	// Handle session problem
	if ($conn) {
		$conn->close();
	}
	session_write_close();
	header("Location: B2BLogin.php?error=S");
}
exit;
