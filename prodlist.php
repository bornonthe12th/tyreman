<HEAD>
<SCRIPT TYPE="text/javascript">
<!--
//-->
</SCRIPT>
<meta Http-Equiv="Cache-Control" Content="no-cache">
<meta Http-Equiv="Pragma" Content="no-cache">
<meta Http-Equiv="Expires" Content="0">
<meta Http-Equiv="Pragma-directive: no-cache">
<meta Http-Equiv="Cache-directive: no-cache">
</HEAD>
<?php
	/*line added to stop IE and Firefox errors displaying when the user clicks 
	<Back> button on browser from Basket - 06/06/08 */
	ini_set('session.cache_limiter','private');
		
	//include error class
	require 'tmanerror.inc';
	//include connect class
	require 'B2Bconnect.php';
	//include html headers
	require 'B2BHeader.inc';
header("Cache-Control: no-cache");	


         // start up your PHP session!
        if(session_id() == '')
           {
           session_start();
           }

        if (isset($_SESSION['sptype'])) {
                $sptype = $_SESSION['sptype'];
           }  else {
                $sptype = '';
           }
?>

<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Veranda; font-size:10">


<li></li>
<li></li>
<li></li>
<li></li>
<li>Product Type</li>
<li>


<?php
//include product type dropdown


$query="call ProdTypeList();";
$username = $_SESSION['dbusername'];
$password = $_SESSION['dbpassword'];
$dbname = $_SESSION['dbschema'];
$host = "localhost";
@ $db = new mysqli($host, $username, $password, $dbname);

if(mysqli_connect_errno())
{
    die("Connection could not be established");
}


$result=$db->query($query);

$total_num_rows = $result->num_rows;
echo "Total $total_num_rows <br>";
echo "sptype = $sptype <br>";

$dropdown = "<select name=$sptype>";

while ( $row = $result->fetch_array()) 
	{
          $dropdown .= "\r\n<option value='{$row['producttype']}'>{$row['producttype']}</option>";
        }
        $dropdown .= "\r\n</select>";
        echo $dropdown;

$_SESSION['sptype']=$sptype
?>

</li>

<?php
	//include closedb class
	include 'B2Bclosedb.php';
?>
</body>
</head>
</html>
