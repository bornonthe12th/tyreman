#!/usr/bin/php
<?php

$debug = true;

$company_id = $_GET['company'] ?? ($_SERVER["argv"][1] ?? null);

if (!$company_id) {
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
    $branch_code = $conn->real_escape_string($data[20]);
    $branch = null;
    $result = $conn->query("SELECT branch_id FROM branches WHERE branch_code = '$branch_code'");
    if ($result && $result->num_rows > 0) {
        $branch = $result->fetch_assoc()['branch_id'];
    }

    $cust_admin = $conn->real_escape_string($data[1]);
    $result = $conn->query("SELECT customer_id FROM b2busers.users WHERE company_id = $company_id AND Cust_Admin = '$cust_admin' LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $cust = $result->fetch_assoc()['customer_id'];
    } else {
        $cust = null;
    }

    if ($cust) {
        $result = $conn->query("SELECT delivery_add_id FROM customers WHERE customer_id = '$cust' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $del_add = $result->fetch_assoc()['delivery_add_id'];
        } else {
            $del_add = null;
        }

        if ($del_add) {
            $query = "UPDATE addresses SET ".
                "address_line1='{$data[12]}', address_line2='{$data[13]}', address_line3='{$data[14]}', ".
                "address_line4='{$data[15]}', address_line5='{$data[16]}', postcode='{$data[17]}', ".
                "addressee='{$data[11]}' WHERE address_id = '$del_add'";
            $conn->query($query);
        } else {
            $query = "INSERT INTO addresses (customer_id, address_line1, address_line2, address_line3, address_line4, address_line5, postcode, addressee) ".
                "VALUES ('$cust', '{$data[12]}', '{$data[13]}', '{$data[14]}', '{$data[15]}', '{$data[16]}', '{$data[17]}', '{$data[11]}')";
            $conn->query($query);
            $del_add = $conn->insert_id;
        }

        $status = ($data[18] == 'Y') ? 'D' : 'A';
        $query = "UPDATE b2busers.users SET username='{$data[2]}', password='{$data[3]}', status='$status' ".
            "WHERE company_id = $company_id AND Cust_Admin = '$cust_admin'";
        $conn->query($query);

        $query = "UPDATE customers SET title='{$data[5]}', Account_no='{$data[4]}', first_name='{$data[6]}', surname='{$data[7]}', ".
            "phone='{$data[8]}', mobile='{$data[9]}', delivery_add_id='$del_add', fax='{$data[10]}', ".
            "on_stop_flag='{$data[19]}', credit_limit='{$data[21]}', trade_limit='{$data[22]}', ".
            "account_balance='{$data[23]}', enquiry_only='{$data[24]}', hide_rrp='{$data[25]}', ".
            "show_cust_specials='{$data[26]}', default_branch_id=" . ($branch ? "'$branch'" : "NULL") . " ".
            "WHERE customer_id = '$cust'";
        $conn->query($query);
    } else {
        // Customer insert logic would go here
        // User insert logic would go here
    }

    $row++;
}

fclose($handle);
$conn->commit();
include 'LDclosedb.php';
