<?php
include "init.php";

// Clear all session variables
$_SESSION = [];

// Unset session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Destroy the session on the server
session_destroy();

// Optional: clear browser-side cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirect to login
header("Location: B2BLogin.php");
exit;
