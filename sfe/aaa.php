<html>
<head>
</head>
<body
<?php session_start();

if ($_SESSION['server_status'] == 'L')
        {
?>
<a><img src=/images/89.gif alt="Server" height="20" width="20" border="0"></a>
<?php
        } else {
?>
<a><img src=/images/90.gif alt='Server' height='20' width='20' border='0'></a>
<?php
               }
?>
</body>
</html>
