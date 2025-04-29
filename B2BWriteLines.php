<?php
include 'Reconnect.php'; // Make sure you have a valid $conn

// Ensure prefix and order_id are already available
if (!isset($prefix) || !isset($order_id)) {
	terror('Prefix or Order ID missing in WriteLines', 'B2BWriteLines.php');
}

// Get Line Details
$query = "CALL LineDetails($order_id);";
$result = $conn->query($query);
if (!$result) {
	terror('Error fetching line details: ' . $conn->error, 'B2BWriteLines.php');
}

// Prepare file
$FilNam = str_pad($prefix, 3, ' ', STR_PAD_RIGHT) . str_pad($order_id, 8, "0", STR_PAD_LEFT) . ".orl";
$folderPath = __DIR__ . "/orders";
$myFile = $folderPath . "/" . $FilNam;

// Create /orders folder if missing
if (!is_dir($folderPath)) {
	mkdir($folderPath, 0755, true);
}

// Open file
$fh = fopen($myFile, 'w');
if (!$fh) {
	terror('Cannot open file for writing: ' . $myFile, 'B2BWriteLines.php');
}

// Write lines
while ($row = $result->fetch_assoc()) {
	$line = '';

	$line .= str_pad($prefix, 3, ' ', STR_PAD_RIGHT);
	$line .= str_pad($row['order_id'], 8, '0', STR_PAD_LEFT);
	$line .= "," . str_pad($row['stockcode'] ?? '', 16, ' ', STR_PAD_RIGHT);
	$line .= "," . str_pad($row['qty'] ?? 0, 6, '0', STR_PAD_LEFT);

	$price = isset($row['price']) ? (int)round($row['price']) : 0; // cast price safely
	$line .= "," . str_pad($price, 8, '0', STR_PAD_LEFT);

	$line .= "\n";

	fwrite($fh, $line);
}

// Close file
fclose($fh);
?>
