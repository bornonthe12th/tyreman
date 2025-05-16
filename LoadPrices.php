#!/usr/bin/php
<?php

if (isset($_GET['company'])) {
    $company_id = $_GET['company'];
} elseif (isset($_SERVER["argv"][1])) {
    $company_id = $_SERVER["argv"][1];
} else {
    exit("Missing company parameter.\n");
}

include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php'; // Assumes $conn (mysqli) is defined here

$cust = "";
$prod = "";

$dirname = "load/" . $company . "/";
$dirArray = [];

if (!is_dir($dirname)) {
    exit("Directory does not exist: $dirname\n");
}

$dh = opendir($dirname);
while (($entryName = readdir($dh)) !== false) {
    if (strtoupper(substr($entryName, -4)) == ".PRC") {
        $dirArray[] = $entryName;
    }
}
closedir($dh);

if (empty($dirArray)) {
    exit("No .PRC files found in $dirname\n");
}

sort($dirArray);
$latestFile = end($dirArray);
$filename = $dirname . $latestFile;

echo "Processing: $filename\n";

$handle = fopen($filename, "r");
if (!$handle) {
    exit("Failed to open $filename\n");
}

$row = 1;

while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    $stockcode = $conn->real_escape_string(trim($data[2]));
    $account_no = $conn->real_escape_string(trim($data[1]));

    // Get product_id
    $query = "SELECT product_id FROM stock WHERE stockcode = '$stockcode' LIMIT 1;";
    $result = $conn->query($query);
    $prod = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['product_id'] : '';

    // Get customer_id
    $query = "SELECT customer_id FROM customers WHERE account_no = '$account_no' LIMIT 1;";
    $result = $conn->query($query);
    $cust = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['customer_id'] : '';

    if ($cust && $prod) {
        $query = "SELECT COUNT(*) AS rowcount FROM prices WHERE customer_id = '$cust' AND product_id = '$prod';";
        $result = $conn->query($query);
        $rowcount = ($result) ? $result->fetch_assoc()['rowcount'] : 0;

        $costprice = (strtolower($company) == 'b2bsavoy') ? $conn->real_escape_string($data[3]) : 0;
        $stocklevel = $conn->real_escape_string($data[4]);
        $netprice = $conn->real_escape_string($data[6]);

        if ($rowcount > 0) {
            $update = "
                UPDATE prices
                SET netprice = '$netprice',
                    stocklevel = '$stocklevel',
                    " . (strtolower($company) == 'b2bsavoy' ? "costprice = '$costprice'," : "") . "
                    old = netprice
                WHERE customer_id = '$cust' AND product_id = '$prod';
            ";
            $conn->query($update);
        } else {
            $insert = "
                INSERT INTO prices (customer_id, product_id, costprice, stocklevel, old, netprice, stockcode)
                VALUES (
                    '$cust',
                    '$prod',
                    '$costprice',
                    '$stocklevel',
                    '0',
                    '$netprice',
                    '$stockcode'
                );
            ";
            $conn->query($insert);
        }
    }

    echo "\r$row processed...";
    $row++;
}

fclose($handle);
$conn->query("COMMIT");

echo "\nDone. Processed $row records.\n";

// Optional cleanup (uncomment if needed)
// foreach ($dirArray as $f) {
//     if ($f[0] !== '.') unlink($dirname . $f);
// }

include 'LDclosedb.php';
?>
