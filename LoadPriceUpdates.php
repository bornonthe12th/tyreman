#!/usr/bin/php
<?php

// Determine how the script was invoked
if (isset($_GET['company'])) {
    $company = $_GET['company'];
} elseif (isset($_SERVER['argv'][1])) {
    $company = $_SERVER['argv'][1];
} else {
    die("Missing company parameter\n");
}

// Include config and connect
include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php'; // This should set up $conn (mysqli)

$cust = '';
$prod = '';

// Lookup dbschema if not already selected (moved into LDopendb.php)
$dirname = "load/" . $company . "/";
if (!is_dir($dirname)) {
    die("Directory $dirname not found.\n");
}

$dirArray = array_filter(scandir($dirname), function ($file) {
    return strtoupper(substr($file, -4)) === '.QTP';
});

if (empty($dirArray)) {
    die("No QTP files found in $dirname\n");
}

sort($dirArray);
$filename = $dirname . end($dirArray);
echo "Processing: $filename\n";

if (($handle = fopen($filename, "r")) === false) {
    die("Failed to open file: $filename\n");
}

$row = 0;
while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    $row++;

    $stockcode = trim($data[2]);
    $accountNo = trim($data[1]);
    $netprice  = $data[6] ?? null;
    $stocklevel = $data[4] ?? null;

    // Get product_id
    $stmt = $conn->prepare("SELECT product_id FROM stock WHERE stockcode = ? LIMIT 1");
    $stmt->bind_param("s", $stockcode);
    $stmt->execute();
    $res = $stmt->get_result();
    $prod = $res->num_rows > 0 ? $res->fetch_assoc()['product_id'] : null;
    $stmt->close();

    // Get customer_id
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE account_no = ? LIMIT 1");
    $stmt->bind_param("s", $accountNo);
    $stmt->execute();
    $res = $stmt->get_result();
    $cust = $res->num_rows > 0 ? $res->fetch_assoc()['customer_id'] : null;
    $stmt->close();

    if ($cust && $prod) {
        // Check if record exists
        $stmt = $conn->prepare("SELECT COUNT(*) AS rowcount FROM prices WHERE customer_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $cust, $prod);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res->fetch_assoc()['rowcount'] > 0;
        $stmt->close();

        if ($exists) {
            // Update
            $stmt = $conn->prepare("UPDATE prices SET netprice = ?, stocklevel = ?, old = netprice WHERE product_id = ? AND customer_id = ?");
            $stmt->bind_param("diii", $netprice, $stocklevel, $prod, $cust);
            $stmt->execute();
            $stmt->close();
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO prices (customer_id, product_id, netprice, stocklevel) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iidi", $cust, $prod, $netprice, $stocklevel);
            $stmt->execute();
            $stmt->close();
        }
    }
}
fclose($handle);

// Commit changes
$conn->commit();

// Delete all QTP files
foreach ($dirArray as $file) {
    if (substr($file, 0, 1) !== '.') {
        unlink($dirname . $file);
    }
}

include 'LDclosedb.php';
?>
