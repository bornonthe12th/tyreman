#!/usr/bin/php
<?php

if (isset($_GET['company'])) {
    $company = $_GET['company'];
} elseif (isset($_SERVER["argv"][1])) {
    $company = $_SERVER["argv"][1];
} else {
    exit("Missing company ID\n");
}

include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php'; // expects $conn to be a mysqli connection

$dirname = "load/" . $company . "/";
$filename = $dirname . "rrps.csv";

if (!file_exists($filename)) {
    die("File not found: $filename\n");
}

$row = 1;
$handle = fopen($filename, "r");

if (!$handle) {
    die("Failed to open $filename\n");
}

while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    $stockcode = trim($data[0]);
    $rrp = floatval($data[1]);
    $rrp4 = floatval($data[2]);

    // Check existence
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM stock WHERE stockcode = ?");
    $checkStmt->bind_param("s", $stockcode);
    $checkStmt->execute();
    $checkStmt->bind_result($rowcount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($rowcount > 0) {
        // Update query
        $updateStmt = $conn->prepare("UPDATE stock SET rrp = ?, rrp4 = ? WHERE stockcode = ?");
        $updateStmt->bind_param("dds", $rrp, $rrp4, $stockcode);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $row++;
}

fclose($handle);

// Commit (if needed)
$conn->query("COMMIT");

include 'LDclosedb.php';
?>
