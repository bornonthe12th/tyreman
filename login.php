<?php
include "init.php";
require 'tmanerror.inc';
require 'b2busersconfig.inc';
require 'B2BFunctions.php';

// Sanitize inputs
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Connect to database using mysqli
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Prepare and call the stored procedure safely
$stmt = $conn->prepare("CALL B2BLogin(?, ?)");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("ss", $username, $password);

if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

$result = $stmt->get_result();
$num = $result->num_rows;

if ($num === 1) {
    // 🛡 Regenerate session ID to avoid using a stale session
    session_regenerate_id(true);

    $row = $result->fetch_assoc();

    $_SESSION['dbusername']       = $row['dbusername'] ?? null;
    $_SESSION['dbpassword']       = $row['dbPASSWORD'] ?? null;
    $_SESSION['dbschema']         = $row['dbschema'] ?? null;
    $_SESSION['customerid']       = $row['customer_id'] ?? null;
    $_SESSION['stylesheet']       = $row['stylesheet'] ?? null;
    $_SESSION['printstylesheet']  = $row['printstylesheet'] ?? null;
    $_SESSION['description']      = $row['description'] ?? null;
    $_SESSION['companyid']        = $row['company_id'] ?? null;
    $_SESSION['uid']              = $username;

    // Get default branch
    $_SESSION['default_branch'] = GetDefaultBranch($_SESSION['customerid']);
    $_SESSION['selected_branch'] = $_SESSION['default_branch'];

    $conn->close();

    tError("User logged on", "$username - $password login.php");

    $URL = "B2BUpdateDate.php";
    session_write_close();
    header("Location: $URL");
    exit;
} else {
    tError("User not found", "$username - $password login.php");
    $conn->close();

    $URL = "B2BLogin.php?error=Y";
    session_write_close();
    header("Location: $URL");
    exit;
}