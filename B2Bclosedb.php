<?php
// it does nothing but close
// a mysql database connection

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
