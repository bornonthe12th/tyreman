#!/usr/bin/php
<?php

$debug = true;

$company = isset($_GET['company']) ? $_GET['company'] : ($_SERVER["argv"][1] ?? null);

if (!$company) {
    die("Company ID not provided.\n");
}

include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php';

$cust = "";
$del_add = "";

$dirname = "load/" . $company . "/";
$dirArray = [];

if (!is_dir($dirname)) {
    die("Directory $dirname not found.\n");
}

$myDirectory = opendir($dirname);
while ($entryName = readdir($myDirectory)) {
    if (strtoupper(substr($entryName, -4)) == ".CLG") {
        $dirArray[] = $entryName;
    }
}
closedir($myDirectory);

if (empty($dirArray)) {
    die("No .CLG files found.\n");
}

sort($dirArray);
$filename = $dirname . end($dirArray);

if (substr(basename($filename), 0, 1) === '.') {
    die("Skipping hidden file.\n");
}

$row = 1;
$handle = fopen($filename, "r");

$conn->begin_transaction();

while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    $branch = null;
    $cust = null;
    $del_add = null;

    $branch_code = $conn->real_escape_string($data[20]);
    $result = $conn->query("SELECT branch_id FROM branches WHERE branch_code = '$branch_code'");
    if ($result && $result->num_rows > 0) {
        $branch = $result->fetch_assoc()['branch_id'];
    }

    $cust_admin = $conn->real_escape_string($data[1]);
    $result = $conn->query("SELECT customer_id FROM b2busers.users WHERE company_id = $company AND Cust_Admin = '$cust_admin' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $cust = $result->fetch_assoc()['customer_id'];
    }

    if ($cust) {
        $result = $conn->query("SELECT delivery_add_id FROM customers WHERE customer_id = '$cust' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $del_add = $result->fetch_assoc()['delivery_add_id'];
        }

        // More update logic would go here: update addresses, users, customers as needed...
        if ($debug) echo "Updating customer $cust...\n";
    } else {
        if ($debug) echo "Inserting new customer for $cust_admin...\n";
        // More insert logic would go here...
    }

    $row++;
}
fclose($handle);

$conn->commit();

include 'LDclosedb.php';
?>
