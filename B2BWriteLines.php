<?php
	
//set up query
//$query="call GetPrefix($companyid);";  
//run query
//$result=mysql_query($query);
//$num=mysql_numrows($result);
//$prefix = mysql_result($result,0,"prefix");

//reconnect
include 'Reconnect.php';

//call search proc
//set up query

$query="call LineDetails($order_id);";	
//run query
$result=mysql_query($query);
$num=mysql_numrows($result);
//buildfilename
//$myFile = "orders/" . str_pad($prefix,3,' ',STR_PAD_RIGHT).str_pad(mysql_result($result,0,"order_id"), 8, "0", STR_PAD_LEFT) . ".orl";
$FilNam = str_pad($prefix,3,' ',STR_PAD_RIGHT).str_pad($order_id,8,"0", STR_PAD_LEFT).".orl";
$myFile = "orders/" . $FilNam;

$OrderId = mysql_result($result,0,"order_id");
$stringData = "";
$fh = fopen($myFile, 'w');
$num=mysql_numrows($result);
//loop round results
if ($num>0) {
	//lines found
	$i=0;  	
	while ($i < $num) {
		//build string for file
		$stringData = $stringData .str_pad($prefix,3,' ',STR_PAD_RIGHT);
		$stringData = $stringData . str_pad(mysql_result($result,$i,"order_id"),8,'0',STR_PAD_LEFT);
		$stringData = $stringData . "," . str_pad(mysql_result($result,$i,"stockcode"),16,' ',STR_PAD_RIGHT);
		$stringData = $stringData . "," . str_pad(mysql_result($result,$i,"qty"),6,'0',STR_PAD_LEFT);
		$stringData = $stringData . "," . str_pad(mysql_result($result,$i,"price"),8,'0',STR_PAD_LEFT) . "\n";
	  	$i++;
	}  		
}

//write file
fwrite($fh, $stringData);
//close
fclose($fh);

  		
?>
