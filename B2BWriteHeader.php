<?php
	
//call search proc
//set up query
$query="call GetPrefix($companyid);";  
//run query
$result=mysql_query($query);
$num=mysql_numrows($result);
$prefix = mysql_result($result,0,"prefix");
$uid = $_SESSION['uid'];

//reconnect
include 'Reconnect.php';

$query="call HeaderDetails($order_id);";  
//run query
$result=mysql_query($query);
$num=mysql_numrows($result);
//buildfilename
//$FilNam = str_pad($prefix,3,' ',STR_PAD_RIGHT).str_pad(mysql_result($result,0,"order_id"), 8, "0", STR_PAD_LEFT) . ".orh";
$FilNam = str_pad($prefix,3,' ',STR_PAD_RIGHT).str_pad($order_id,8,"0", STR_PAD_LEFT).".orh";
$myFile = "orders/" . $FilNam;


$fh = fopen($myFile, 'w');
//build string for file

$stringData = str_pad($prefix,3,' ',STR_PAD_RIGHT);
$stringData = $stringData . "," .  str_pad(mysql_result($result,0,"order_id"),8,'0',STR_PAD_LEFT);
$stringData = $stringData . "," . str_pad(mysql_result($result,0,"account_no"),8,' ',STR_PAD_RIGHT);


$stringData = $stringData . "," . str_pad(mysql_result($result,0,"Email"),50,' ',STR_PAD_RIGHT);
$odate = substr(mysql_result($result,0,"order_date"),0,10);
$odate = substr($odate,0,4) . substr($odate,5,2 ) . substr($odate,8,2 );
$stringData = $stringData . "," . $odate;
$order_ref = str_replace(","," ",mysql_result($result,0,"order_ref"));
$stringData = $stringData . "," .str_pad($order_ref,12,' ',STR_PAD_RIGHT);

//$stringData = $stringData . "," . str_pad($_SESSION['selected_branch'],8,' ',STR_PAD_LEFT);
//code added 11/03/09 to replace above line - replace branch_id with branch_code
//reconnect
include 'Reconnect.php';
$query = "SELECT * FROM branches WHERE branch_id = ".$_SESSION['selected_branch'];
$result = mysql_query($query);
$stringData = $stringData . "," . str_pad(mysql_result($result,0,"branch_code"),8,' ',STR_PAD_LEFT);
$stringData = $stringData . "," . str_pad($_POST['comments1'],32,' ',STR_PAD_RIGHT);
$stringData = $stringData . "," . str_pad($_POST['comments2'],32,' ',STR_PAD_RIGHT);
$stringData = $stringData . "," . str_pad($_POST['comments3'],32,' ',STR_PAD_RIGHT);
$stringData = $stringData . "," . str_pad($_POST['comments4'],32,' ',STR_PAD_RIGHT);
$stringData = $stringData . "," . str_pad($uid,15,' ',STR_PAD_RIGHT);


//write file
fwrite($fh, $stringData);
//close
fclose($fh);

?>
