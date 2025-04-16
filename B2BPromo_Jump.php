<?php

if(session_id() == '') 
    {
    session_start();
    }

$_SESSION['first_time'] = "Y";

?>

<meta http-equiv="refresh" content="0;url=B2BProdSearch.php">
