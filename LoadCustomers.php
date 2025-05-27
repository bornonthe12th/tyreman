#!/usr/bin/php
<?php

$debug = true;

$company_id = $_GET['company'] ?? ($_SERVER["argv"][1] ?? null);
if (!$company_id) {
    die("Company not provided.\n");
}

include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php'; // Assumes $conn (mysqli) is defined

$dirname = "load/" . $company . "/";
if (!is_dir($dirname)) {
    die("Directory $dirname not found.\n");
}

$dirArray = [];
$dh = opendir($dirname);
while (($entryName = readdir($dh)) !== false) {
    if (strtoupper(substr($entryName, -4)) === ".CLG") {
        $dirArray[] = $entryName;
    }
}
closedir($dh);

if (empty($dirArray)) {
    die("No .CLG files found in $dirname\n");
}

sort($dirArray);
$filename = $dirname . end($dirArray);
if (substr(basename($filename), 0, 1) === '.') {
    die("Skipping hidden file.\n");
}

echo "Processing: $filename\n";
$handle = fopen($filename, "r");
if (!$handle) {
    die("Failed to open $filename\n");
}

$conn->begin_transaction();
$row = 1;

while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    // Guard missing fields
    for ($i = 0; $i <= 26; $i++) {
        $data[$i] = $data[$i] ?? '';
    }

    $branch_code = $conn->real_escape_string($data[20]);
    $result = $conn->query("SELECT branch_id FROM branches WHERE branch_code = '$branch_code'");
    $branch = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['branch_id'] : null;

    $cust_admin = $conn->real_escape_string($data[1]);
    $result = $conn->query("SELECT customer_id FROM b2busers.users WHERE company_id = $company_id AND Cust_Admin = '$cust_admin' LIMIT 1");
    $cust = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['customer_id'] : null;

    if ($cust) {
        $result = $conn->query("SELECT delivery_add_id FROM customers WHERE customer_id = '$cust' LIMIT 1");
        $del_add = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['delivery_add_id'] : null;
    } else {
        $account_no = $conn->real_escape_string(trim($data[4]));
        $result = $conn->query("SELECT customer_id, delivery_add_id FROM customers WHERE account_no = '$account_no' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $rowData = $result->fetch_assoc();
            $cust = $rowData['customer_id'];
            $del_add = $rowData['delivery_add_id'];
        } else {
            $cust = null;
            $del_add = null;
        }
    }

    if (!$del_add) {
        $query = "INSERT INTO addresses (customer_id, address_line1, address_line2, address_line3, address_line4, address_line5, postcode, addressee) ".
            "VALUES (NULL, '{$data[12]}', '{$data[13]}', '{$data[14]}', '{$data[15]}', '{$data[16]}', '{$data[17]}', '{$data[11]}')";
        safeQuery($conn, $query, "Insert delivery address");
        $del_add = $conn->insert_id;
    } else {
        $query = "UPDATE addresses SET ".
            "address_line1='{$data[12]}', address_line2='{$data[13]}', address_line3='{$data[14]}', ".
            "address_line4='{$data[15]}', address_line5='{$data[16]}', postcode='{$data[17]}', ".
            "addressee='{$data[11]}' WHERE address_id = '$del_add'";
        safeQuery($conn, $query, "Update delivery address");
    }

    $credit_limit = (float) $data[21];
    $trade_limit = (float) $data[22];
    $account_balance = (float) $data[23];
    $branch_id = $branch ? (int)$branch : "NULL";
    $account_no = substr($conn->real_escape_string(trim($data[4])), 0, 8);

    if (!$cust) {
        $query = "INSERT INTO customers (
            title, first_name, surname, phone, mobile, fax, account_no,
            delivery_add_id, default_branch_id, credit_limit,
            trade_limit, account_balance, on_stop_flag,
            enquiry_only, hide_rrp, show_cust_specials
        ) VALUES (
            '{$data[5]}', '{$data[6]}', '{$data[7]}', '{$data[8]}', '{$data[9]}', '{$data[10]}',
            '$account_no', '$del_add', $branch_id, $credit_limit, $trade_limit, $account_balance,
            '{$data[19]}', '{$data[24]}', '{$data[25]}', '{$data[26]}'
        )";
        safeQuery($conn, $query, "Insert new customer");
        $cust = $conn->insert_id;
    } else {
        $query = "UPDATE customers SET title='{$data[5]}', first_name='{$data[6]}', surname='{$data[7]}', phone='{$data[8]}', ".
            "mobile='{$data[9]}', fax='{$data[10]}', delivery_add_id='$del_add', ".
            "credit_limit=$credit_limit, trade_limit=$trade_limit, account_balance=$account_balance, ".
            "on_stop_flag='{$data[19]}', enquiry_only='{$data[24]}', hide_rrp='{$data[25]}', ".
            "show_cust_specials='{$data[26]}', default_branch_id=$branch_id ".
            "WHERE customer_id = '$cust'";
        safeQuery($conn, $query, "Update existing customer");
    }

    // Insert or update user
    $status = ($data[18] === 'Y') ? 'D' : 'A';
    if (!$result || $result->num_rows === 0) {
        $query = "INSERT INTO b2busers.users (cust_admin, username, password, customer_id, company_id, status) ".
            "VALUES ('{$data[1]}', '{$data[2]}', '{$data[3]}', '$cust', '$company_id', '$status')";
        safeQuery($conn, $query, "Insert user");
    } else {
        $query = "UPDATE b2busers.users SET username='{$data[2]}', password='{$data[3]}', status='$status' ".
            "WHERE company_id = $company_id AND Cust_Admin = '$cust_admin'";
        safeQuery($conn, $query, "Update user");
    }

    echo "\r$row processed...";
    $row++;
}

fclose($handle);
$conn->commit();
echo "\nDone. Processed $row records.\n";

include 'LDclosedb.php';
