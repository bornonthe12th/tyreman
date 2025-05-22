<?php
// Define DB connection credentials if not already
$dbhost = $dbhost ?? 'localhost';
$dbuser = $dbuser ?? 'your_mysql_user';
$dbpass = $dbpass ?? 'your_mysql_password';
$masterDb = 'b2busers'; // The initial schema to connect to

// Connect to the database server (initially connect to the master schema to get dbschema)
$conn = new mysqli($dbhost, $dbuser, $dbpass, $masterDb);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error . PHP_EOL);
}


// Sanitize company_id
if (!isset($company_id)) {
	exit("Missing company parameter\n");
}

$company_id = (int)$company_id;

// Lookup company schema
$query = "SELECT dbschema FROM companies WHERE company_id = $company_id";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
	exit("Invalid Company\n");
}

$row = $result->fetch_assoc();

$company = $row['dbschema'];
$dbname = $company; // You use this later

// Reconnect to the correct company schema
$conn->select_db($dbname);


function safeQuery(mysqli $conn, string $query, string $context = ''): bool {
	if (!$conn->query($query)) {
		$msg = "[ERROR] Query failed" . ($context ? " in $context" : '') .
			":\n$query\nMySQL Error: " . $conn->error . "\n";
		file_put_contents('sql_errors.log', $msg, FILE_APPEND);
		exit($msg); // Exit if critical
	}
	return true;
}
?>
