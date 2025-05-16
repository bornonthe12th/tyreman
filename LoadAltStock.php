#!/usr/bin/php
<?php

if (isset($_GET['company'])) {
    $company= $_GET['company'];
} elseif (isset($_SERVER["argv"][1])) {
    $company = $_SERVER["argv"][1];
} else {
    exit("Missing company ID\n");
}

include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php'; // should provide $conn (mysqli object)

$product = "";
$dirname = "load/" . $company . "/";

if (!is_dir($dirname)) {
    die("Directory $dirname does not exist.\n");
}

$dirArray = [];
$myDirectory = opendir($dirname);
while (($entryName = readdir($myDirectory)) !== false) {
    if (strtoupper(substr($entryName, -4)) == ".ALT") {
        $dirArray[] = $entryName;
    }
}
closedir($myDirectory);

$indexCount = count($dirArray);
sort($dirArray);
$index = $indexCount - 1;

if ($index < 0 || substr($dirArray[$index], 0, 1) === ".") {
    exit("No valid ALT file found.\n");
}

$row = 1;
$filename = $dirname . $dirArray[$index];
$handle = fopen($filename, "r");

if (!$handle) {
    die("Failed to open file: $filename\n");
}

while (($data = fgetcsv($handle, 1000, ",")) !== false && strlen($data[1]) > 4) {
    $altcode = trim($data[1]);
    $stockcode = trim($data[2]);

    $query = "SELECT a.product_id 
              FROM altstockcodes a 
              JOIN stock s ON a.product_id = s.product_id 
              WHERE a.stockcode = ? AND s.stockcode = ? 
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $altcode, $stockcode);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->free_result();
        $stmt->close();

        $query = "SELECT product_id FROM stock WHERE stockcode = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $stockcode);
        $stmt->execute();
        $stmt->bind_result($product_id);

        if ($stmt->fetch()) {
            $stmt->close();

            $query = "INSERT INTO altstockcodes (product_id, stockcode) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $product_id, $altcode);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt->close();
        }
    } else {
        $stmt->free_result();
        $stmt->close();
    }

    $row++;
}

fclose($handle);
$conn->query("COMMIT");

for ($i = 0; $i < $indexCount; $i++) {
    if (substr($dirArray[$i], 0, 1) !== ".") {
        $filepath = $dirname . $dirArray[$i];
        // unlink($filepath); // Uncomment to delete
    }
}

include 'LDclosedb.php';
?>
