<?php session_start();

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];

    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

    else if (!empty($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];

    else if (!empty($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];

    else if (!empty($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];

    else if (!empty($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];

    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

$remote_IP = get_client_ip();

//connect to users DB
$mdb_conn = mysqli_connect("localhost", $_SESSION['dbusername'] ,$_SESSION['dbpassword'], "b2busers")
                                or die('Error connecting to MySQL server - Audit update.');

//copy parms to local vars
$uid = $_SESSION['uid'];
$sql="insert into login_audit (UserName,Login_Date,Login_Time,Remote_IP) VALUES ('$uid', NOW(), NOW(), '$remote_IP');";

if (mysqli_query($mdb_conn, $sql)) {
    echo "login audited";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($mdb_conn);
}


//disconnect from usersdb
mysqli_close($mdb_conn);

$URL="B2BProdSearch.php";
header ("Location: $URL");

?>
