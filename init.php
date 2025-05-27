<?php

ini_set('session.cache_limiter','nocache');
session_cache_limiter('nocache');
// init.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional cache protection
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>
