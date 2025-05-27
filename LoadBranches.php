#!/usr/bin/php
<?php

// Determine company folder from CLI or browser input
$company_id = $_GET['company'] ?? ($_SERVER["argv"][1] ?? null);

if (!$company_id) {
    die("Company folder not provided.\n");
}

require 'tmanerror.inc';
require 'LDconfig.php';
require 'LDopendb.php'; // Must return $conn (a mysqli object)

$dirname = __DIR__ . "/load/" . $company;

if (!is_dir($dirname)) {
    die("Directory not found: $dirname\n");
}

$dirArray = [];
if ($dh = opendir($dirname)) {
    while (($entryName = readdir($dh)) !== false) {
        if (strtoupper(substr($entryName, -4)) === ".BRN") {
            $dirArray[] = $entryName;
        }
    }
    closedir($dh);
}

if (empty($dirArray)) {
    die("No .BRN files found.\n");
}

sort($dirArray);
$latestFile = end($dirArray);
$filename = "$dirname/$latestFile";
echo "Processing file: $filename\n";

if (($handle = fopen($filename, "r")) === false) {
    die("Failed to open $filename\n");
}

$row = 1;
while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    $branchCode = $conn->real_escape_string($data[1]);
    $description = $conn->real_escape_string($data[2]);
    $status = ($data[7] === 'Y') ? 'A' : 'D';

    $result = $conn->query("SELECT branch_id, address_id FROM branches WHERE branch_code = '$branchCode' LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $rowData = $result->fetch_assoc();
        $branchId = $rowData['branch_id'];
        $addressId = $rowData['address_id'];

        $update = "
            UPDATE branches 
            SET description = '$description', status = '$status', branch_code = '$branchCode' 
            WHERE branch_id = '$branchId'
        ";
        $conn->query($update);
    } else {
        $insert = "
            INSERT INTO branches (branch_code, description, status)
            VALUES ('$branchCode', '$description', '$status')
        ";
        $conn->query($insert);
        $branchId = $conn->insert_id;
        $addressId = '';
    }

    // Address fields
    $line1 = $conn->real_escape_string($data[3]);
    $line2 = $conn->real_escape_string($data[4]);
    $line3 = $conn->real_escape_string($data[5]);
    $postcode = $conn->real_escape_string($data[6]);
    $addressee = $conn->real_escape_string($description);

    if ($addressId) {
        $exists = $conn->query("SELECT 1 FROM addresses WHERE address_id = '$addressId'");
        if ($exists && $exists->num_rows > 0) {
            $update = "
                UPDATE addresses
                SET address_line1 = '$line1', address_line2 = '$line2',
                    address_line3 = '$line3', postcode = '$postcode', addressee = '$addressee'
                WHERE address_id = '$addressId'
            ";
            $conn->query($update);
        } else {
            $addressId = '';
        }
    }

    if (!$addressId) {
        $insert = "
            INSERT INTO addresses (customer_id, address_line1, address_line2, address_line3, postcode, addressee)
            VALUES (NULL, '$line1', '$line2', '$line3', '$postcode', '$addressee')
        ";
        $conn->query($insert);
        $addressId = $conn->insert_id;

        if (isset($branchId)) {
            $conn->query("UPDATE branches SET address_id = '$addressId' WHERE branch_id = '$branchId'");
        }
    }

    $row++;
}
fclose($handle);

// Optional commit
$conn->commit();
echo "Import completed successfully.\n";

// Optionally clean up files
foreach ($dirArray as $file) {
    if (substr($file, 0, 1) !== ".") {
        // Uncomment this to enable deletion:
        // unlink("$dirname/$file");
    }
}

require 'LDclosedb.php';
