<?php
include 'Reconnect.php'; // Make sure you have a valid $conn

// Get prefix
$query = "CALL GetPrefix($companyid);";
$result = $conn->query($query);
if (!$result) {
    terror('Error fetching prefix: ' . $conn->error, 'B2BWriteHeader.php');
}
$row = $result->fetch_assoc();
$prefix = $row['prefix'] ?? '';
$uid = $_SESSION['uid'] ?? '';

// Reconnect if needed
include 'Reconnect.php';

// Get header details
$query = "CALL HeaderDetails($order_id);";
$result = $conn->query($query);
if (!$result) {
    terror('Error fetching order header details: ' . $conn->error, 'B2BWriteHeader.php');
}
$row = $result->fetch_assoc();
if (!$row) {
    terror('No header data found for order.', 'B2BWriteHeader.php');
}

// Build filename
$FilNam = str_pad($prefix, 3, ' ', STR_PAD_RIGHT) . str_pad($order_id, 8, "0", STR_PAD_LEFT) . ".orh";
$myFile = __DIR__ . "/orders/" . $FilNam; // safer path

// Try to open file
$fh = fopen($myFile, 'w');
if (!$fh) {
    terror('Cannot open file for writing: ' . $myFile, 'B2BWriteHeader.php');
}

// Prepare string data safely
$stringData = str_pad($prefix, 3, ' ', STR_PAD_RIGHT);
$stringData .= "," . str_pad($row['order_id'], 8, '0', STR_PAD_LEFT);
$stringData .= "," . str_pad($row['account_no'], 8, ' ', STR_PAD_RIGHT);

// Email (trim, escape commas)
$email = str_replace(',', ' ', $row['Email'] ?? '');
$stringData .= "," . str_pad($email, 50, ' ', STR_PAD_RIGHT);

// Order date (convert to yyyymmdd)
$order_date = $row['order_date'] ?? '';
$odate = '';
if (!empty($order_date)) {
    $odate = date('Ymd', strtotime($order_date));
}
$stringData .= "," . $odate;

// Order reference (remove commas)
$order_ref = str_replace(',', ' ', $row['order_ref'] ?? '');
$stringData .= "," . str_pad($order_ref, 12, ' ', STR_PAD_RIGHT);

// Reconnect if needed
include 'Reconnect.php';

// Get branch code
$selected_branch = (int)($_SESSION['selected_branch'] ?? 0);
$query = "SELECT branch_code FROM branches WHERE branch_id = $selected_branch LIMIT 1;";
$result = $conn->query($query);
if (!$result) {
    terror('Error fetching branch code: ' . $conn->error, 'B2BWriteHeader.php');
}
$branch_row = $result->fetch_assoc();
$branch_code = $branch_row['branch_code'] ?? '';
$stringData .= "," . str_pad($branch_code, 8, ' ', STR_PAD_LEFT);

// Comments
$comments = [
    $_POST['comments1'] ?? '',
    $_POST['comments2'] ?? '',
    $_POST['comments3'] ?? '',
    $_POST['comments4'] ?? ''
];
foreach ($comments as $comment) {
    $cleanComment = str_replace(',', ' ', $comment); // clean commas
    $stringData .= "," . str_pad($cleanComment, 32, ' ', STR_PAD_RIGHT);
}

// User ID
$stringData .= "," . str_pad($uid, 15, ' ', STR_PAD_RIGHT);

// Write to file
fwrite($fh, $stringData);
fclose($fh);
?>
