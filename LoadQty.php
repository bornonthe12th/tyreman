#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    if (isset($_GET['company'])) {
        $company_id = $_GET['company'];
    }
} else {
    if (isset($argv[1])) {
        $company_id = $argv[1];
    }
}

include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php';

$dirname = "load/" . $company . "/";
$dirArray = [];

if (!is_dir($dirname)) {
    die("Directory not found: $dirname\n");
}

$myDirectory = opendir($dirname);
while (($entryName = readdir($myDirectory)) !== false) {
    if (strtoupper(substr($entryName, -4)) === ".QTY") {
        $dirArray[] = $entryName;
    }
}
closedir($myDirectory);

$indexCount = count($dirArray);
if ($indexCount === 0) {
    echo "No .QTY files found.\n";
    include 'LDclosedb.php';
    exit;
}

sort($dirArray);
$index = $indexCount - 1;
$filename = $dirname . $dirArray[$index];

if ($dirArray[$index][0] === '.') {
    echo "Skipping hidden file.\n";
    include 'LDclosedb.php';
    exit;
}

// Turn off autocommit
$conn->query("SET autocommit=0");

echo "Processing file: $filename\n";

$handle = fopen($filename, "r");
$row = 1;

while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[2]) > 4) {
    $branch_code = $conn->real_escape_string($data[1]);
    $stockcode = $conn->real_escape_string($data[2]);
    $stocklevel = (int)$data[7];
    $compstk = (int)$data[9];
    $regionstk = (int)$data[10];

    $query = "SELECT branch_id FROM branches WHERE branch_code = '$branch_code'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $branch = $result->fetch_assoc()['branch_id'];
    } else {
        echo "Branch not found for code: $branch_code (row $row)\n";
        $row++;
        continue;
    }

    $query = "SELECT COUNT(*) AS rowcount FROM stock WHERE stockcode = '$stockcode' AND branch_id = '$branch'";
    $result = $conn->query($query);
    $rowcount = $result ? (int)$result->fetch_assoc()['rowcount'] : 0;

    if ($rowcount > 0) {
        $update = "
            UPDATE stock SET 
                stocklevel = '$stocklevel', 
                compstk = '$compstk', 
                regionstk = '$regionstk' 
            WHERE stockcode = '$stockcode' AND branch_id = '$branch';
        ";
        $conn->query($update);
    } else {
        echo "Stock record not found for $stockcode in branch $branch (row $row)\n";
    }

    $row++;
}

fclose($handle);

// Commit
$conn->query("COMMIT");

// Optionally delete all .QTY files
for ($i = 0; $i < $indexCount; $i++) {
    if ($dirArray[$i][0] !== '.') {
        $fileToDelete = $dirname . $dirArray[$i];
        // Uncomment the line below to enable deletion:
        // unlink($fileToDelete);
    }
}

include 'LDclosedb.php';

?>
