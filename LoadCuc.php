#!/usr/bin/php
<?php

// Detect input source (browser or CLI)
if (isset($_GET['company'])) {
    $company_id = $_GET['company'];
} elseif (isset($_SERVER["argv"][1])) {
    $company_id = $_SERVER["argv"][1];
} else {
    die("Company ID not provided.\n");
}

// Includes
include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php'; // This must use mysqli now

// Directory for files
$dirname = "load/" . $company . "/";
if (!is_dir($dirname)) {
    die("Directory not found: $dirname\n");
}

$dirArray = [];

// Open and read directory
$myDirectory = opendir($dirname);
while (($entryName = readdir($myDirectory)) !== false) {
    if (strtoupper(substr($entryName, -4)) === ".CUC") {
        $dirArray[] = $entryName;
    }
}
closedir($myDirectory);

if (count($dirArray) === 0) {
    die("No .CUC files found in directory.\n");
}

// Sort files by name (can be adjusted to sort by mtime if needed)
sort($dirArray);

// Process the latest file
$latestFile = end($dirArray);
$filename = $dirname . $latestFile;
echo "Processing: $filename\n";

// Delete existing records
$conn->query("DELETE FROM customer_class");

// Open and read the CSV
$row = 1;
if (($handle = fopen($filename, "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if (strlen($data[2]) > 4) {
            $account_no    = $conn->real_escape_string($data[2]);
            $productgroup  = $conn->real_escape_string($data[3]);
            $display_flag  = $conn->real_escape_string($data[4]);

            $query = "
                INSERT INTO customer_class (Account_No, productgroup, display_flag)
                VALUES ('$account_no', '$productgroup', '$display_flag')
            ";

            if (!$conn->query($query)) {
                echo "Insert failed at row $row: " . $conn->error . "\n";
            }
        }
        $row++;
    }
    fclose($handle);
} else {
    die("Failed to open file: $filename\n");
}

// Commit transaction if needed (optional with autocommit)
$conn->commit();

// Done
echo "Import completed successfully.\n";

// Disconnect
include 'LDclosedb.php';
