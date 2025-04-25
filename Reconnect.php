<?php

if (isset($conn)) {
	// Disconnect
	$conn->close();
} else {
	$username   = $_SESSION['dbusername'] ?? '';
	$dbpassword = $_SESSION['dbpassword'] ?? '';
	$dbschema   = $_SESSION['dbschema'] ?? '';
	$dbhost     = "localhost";
}

// Reconnect using mysqli
$conn = new mysqli($dbhost, $username, $dbpassword, $dbschema);

if ($conn->connect_error) {
	die(tError('Unable to connect to B2B DB', 'ReConnect.php'));
}

