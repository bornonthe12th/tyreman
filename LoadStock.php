#!/usr/bin/php
<?php

if (isset($_GET['company'])) {
    $company_id = $_GET['company'];
} elseif (isset($_SERVER["argv"][1])) {
    $company_id = $_SERVER["argv"][1];
} else {
    exit("Missing company parameter.\n");
}

require 'tmanerror.inc';
require 'LDconfig.php';
require 'LDopendb.php';

function getLines($file): int {
    $f = fopen($file, 'rb');
    if (!$f) {
        die('fopen failed');
    }

    $lines = 0;
    while (!feof($f)) {
        $lines += substr_count(fread($f, 8192), "\n");
    }
    fclose($f);
    return $lines;
}

$dirname = "load/" . $company . "/";
$rootDir = "/var/www/html/PHPB2B/$dirname";

echo "PHP update :\n";
echo "Root Directory = $rootDir\n";
echo "Directory = $dirname\n";

$dirArray = [];
if (!is_dir($dirname)) {
    die("Directory not found: $dirname\n");
}

$dh = opendir($dirname);
while (($entryName = readdir($dh)) !== false) {
    if (strtoupper(substr($entryName, -4)) === ".STK") {
        $dirArray[] = $entryName;
    }
}
closedir($dh);

if (empty($dirArray)) {
    die("No .STK files found.\n");
}

sort($dirArray);
$latestFile = end($dirArray);
$file = $rootDir . $latestFile;
echo "FileName = $file\n";
echo "Records in file = " . getLines($file) . "\n";
echo "Records processed :- \n";

// mark all existing stock as deleted
$conn->query("UPDATE stock SET status = 'X'");

$row = 1;
$handle = fopen($file, "r");
while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[0]) > 2) {
    $branch = '';
    $branchCode = $conn->real_escape_string($data[1]);
    $stockCode = $conn->real_escape_string($data[2]);

    $result = $conn->query("SELECT branch_id FROM branches WHERE branch_code = '$branchCode' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $branch = $result->fetch_assoc()['branch_id'];
    }

    if ($branch !== '') {
        $check = $conn->query("SELECT COUNT(*) AS rowcount FROM stock WHERE Stockcode = '$stockCode' AND branch_id = '$branch'");
        $exists = $check && ($check->fetch_assoc()['rowcount'] ?? 0) > 0;

        if ($exists) {
            $query = "
                UPDATE stock SET
                    description = '{$conn->real_escape_string($data[3])}',
                    productgroup = '{$conn->real_escape_string($data[4])}',
                    manufacturer = '{$conn->real_escape_string($data[5])}',
                    producttype = '{$conn->real_escape_string($data[6])}',
                    stocklevel = '{$conn->real_escape_string($data[7])}',
                    size = '{$conn->real_escape_string($data[8])}',
                    highlight = '{$conn->real_escape_string($data[9])}',
                    supplier_stock = '{$conn->real_escape_string($data[24])}',
                    image_name = '{$conn->real_escape_string($data[25])}',
                    status = 'A'
                WHERE Stockcode = '$stockCode' AND branch_id = '$branch'
            ";
            $conn->query($query);
        } else {
            $query = "
                INSERT INTO stock (
                    branch_id, stockcode, description, productgroup, manufacturer,
                    producttype, stocklevel, status, size, supplier_stock, image_name, highlight
                ) VALUES (
                    '$branch',
                    '$stockCode',
                    '{$conn->real_escape_string($data[3])}',
                    '{$conn->real_escape_string($data[4])}',
                    '{$conn->real_escape_string($data[5])}',
                    '{$conn->real_escape_string($data[6])}',
                    '{$conn->real_escape_string($data[7])}',
                    'A',
                    '{$conn->real_escape_string($data[8])}',
                    '{$conn->real_escape_string($data[24])}',
                    '{$conn->real_escape_string($data[25])}',
                    '{$conn->real_escape_string($data[9])}'
                )
            ";
            $conn->query($query);
        }
    }

    echo "\r$row";
    $row++;
}
fclose($handle);
$conn->query("COMMIT");

// remove all processed files
foreach ($dirArray as $filename) {
    if ($filename[0] !== '.') {
        unlink($dirname . $filename);
    }
}

require 'LDclosedb.php';

echo "\nDone.\n";
?>
