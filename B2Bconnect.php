<?php
// Start session safely
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check required session variables
if (
	!isset($_SESSION['dbusername']) ||
	!isset($_SESSION['dbpassword']) ||
	!isset($_SESSION['dbschema'])
) {
	header("Location: B2BLogin.php");
	exit;
}

// Assign session variables
$username   = $_SESSION['dbusername'];
$dbpassword = $_SESSION['dbpassword'];
$dbschema   = $_SESSION['dbschema'];
$dbhost     = "localhost";

// Connect using mysqli
$conn = new mysqli($dbhost, $username, $dbpassword, $dbschema);

// Check connection
if ($conn->connect_error) {
	die(tError('Unable to connect to B2B DB: ' . $conn->connect_error, 'B2BConnect.php'));
}
