<?php
	
	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//include html headers
	include 'B2BHeader.inc';
	
	$cust = $_SESSION['customerid'];
	$order_id = $_SESSION['orderid'];
	$Company_desc = $_SESSION['description'];
	$companyid = $_SESSION['companyid'];
	//get session id
	$session = session_id();
	
	if (isset($_GET['ord'])) {
		$ord = $_GET['ord'];
	} else {
		$ord = '';	
	}
?>

<BODY>

<?php
	//include menu class
	include 'B2BMenu.php';
	//include global functions class
	include 'B2BFunctions.php'; 
?>

<div id="content">
<!-- blank_sidebar_SB  -->
<div id="sidebar">

<!--<img src=/images/--> 

<?php
echo "<img src=";
echo GetResource('titlebarhdrimg');
echo ">"; 
?>
<ul>
<li><h2 align=center>Order Confirmation</h2></li>
<form name=frmConfirm action=B2BProdSearch.php method=POST>
<li><input type=button name=prdsrch value="Start New order" onClick=window.location.href="B2BProdSearch.php"></input></li>
<li><br></li>
<li><input type=button name=logout value="Logout" onClick=window.location.href="logout.php"></input></li>
<li><br></li>

</ul>

<?php
include 'B2BSbarFtr.inc';
?>
</div><!-- /sidebar -->
<div id="mainbody">

<?php
include 'Reconnect.php';

//set up query
$query="call ShowSummary('$session',$order_id);";
$neg_stk = "N";

//run query
$result=mysql_query($query);

$num=mysql_numrows($result);

if ($num>0) {
	$i=0;
	$OrdTtl=0;
	
	//display order details
	//reconnect
	echo '<div style="font-size:12px;margin-top:30px;">';
	include 'Reconnect.php';
	$sql = "SELECT * FROM customers LEFT JOIN addresses ON customers.delivery_add_id = addresses.Address_id WHERE customers.Customer_id = '$cust'";
	$result2 = mysql_query($sql) or die(mysql_error());
	$num_rows=mysql_numrows($result2);
	if ($num_rows > 0){
		echo '<div style="margin-left:50px;float:left;width:70px;font-weight:bold;">To:</div>';
		echo '<div style="float:left;width:150px;">'.mysql_result($result2,0,"Addressee").'<br />';
		echo mysql_result($result2,0,"Address_line1").'<br />';
		echo mysql_result($result2,0,"Address_line2").'<br />';
		echo mysql_result($result2,0,"Address_line3").'<br />';
		echo mysql_result($result2,0,"Address_line4").'<br />';
		echo mysql_result($result2,0,"Address_line5").'<br />';
		echo mysql_result($result2,0,"PostCode").'<br />';	
		echo '</div>';
	}
	$sql_supplier = "SELECT Description FROM branches WHERE branch_id = '$_SESSION[selected_branch]'";
	$result3 = mysql_query($sql_supplier) or die(mysql_error());
	$num_rows=mysql_numrows($result3);
	if ($num_rows > 0){
		echo '<div style="margin-left:35px;float:left;width:150px;font-weight:bold;">Supplying Depot:</div>';
		echo '<div style="float:left;">';
		echo $Company_desc.'<br />'.mysql_result($result3,0,"Description");
		echo '</div>';		
	}	
	//clear floats
	echo '<div style="clear:both;"></div>';	
	echo '</div>';
	
	echo "<br><table id=BlueTable align=center>";
	echo "<tr><td CLASS=maintitle>Order Complete</td></tr> ";
	echo "<tr><td>";
	echo "<table align=center>";
	echo "<tr><th width=150 class=titlemedium>Stock Code</th>";
	echo "<th width=300 class=titlemedium>Description&nbsp;</th>";
	echo "<th width=75 class=titlemedium>&nbsp;Quantity&nbsp;</th>";
	echo "<th width=75 class=titlemedium>&nbsp;Unit Price&nbsp;</th>";
	echo "</tr>";
	while ($i < $num) { 
		if ($i/2 == round($i/2)) {
			$tdclass = 'even';
		} else {
			$tdclass = 'odd';
		} 
		echo "<tr><td class=$tdclass>" . mysql_result($result,$i,"stockcode") ."&nbsp;</td>"; 
		echo "<td class=$tdclass>" . mysql_result($result,$i,"description") ."&nbsp;</td>"; 
		echo "<td align=right class=$tdclass>" . mysql_result($result,$i,"qty") . "&nbsp;</td>"; 
//		echo "<td align=right class=$tdclass>" . mysql_result($result,$i,"stocklevel") . "&nbsp;</td>"; 
	
		if (mysql_result($result,$i,"stocklevel") < 0 )
			{ $neg_stk = "Y" ;
			}
//		echo "<td align=right class=$tdclass>" . $neg_stk . "&nbsp;</td>"; 
		echo "<td class=$tdclass align=right>" . mysql_result($result,$i,"price") ."&nbsp;</td>"; 
		echo "</tr>";
		$ord_ref = str_replace(","," ",mysql_result($result,$i,"order_ref"));
		$OrdTtl = $OrdTtl + (mysql_result($result,$i,"price")*mysql_result($result,$i,"qty"));
		$i++;
	}	
	echo "<tr><td colspan=2></td><td align=right>Total&nbsp</td><td align=right>" . number_format($OrdTtl,2,'.',',') . "&nbsp;</td></tr>";	
	echo "</table>";
	echo "</td></tr>";
	echo "</table>"."\n";
	//$neg_stk = "N";
	switch ($companyid)
	
                        {
                        // Hawleys show message if stock gone negative
						case 1: 
						if ($neg_stk == "Y")
						{
							echo "<br><div id=Message_Div align=center><table><tr bgcolor=red><td>INSUFFICIENT STOCK AVAILABLE AT BRIDGE STREET.";
							echo " </td></tr><tr bgcolor=red><td>PLEASE CALL 0114 272 1096 TO PLACE YOUR ORDER.</td></tr>";
							echo "</td></tr></table></div>";
						} else
							{
							// Default message to show Order number and confirmation message
							echo "<br><div id=Message_Div align=center><table id=BlueTable><tr><td class=titlemedium>Thank you for placing your order with ";
							echo $Company_desc;
							echo " </td></tr><tr><td class=titlemedium>Please print this page for your reference.</td></tr>";
							echo "<tr><td class=titlemedium>Our reference: " .str_pad($ord, 8, "0", STR_PAD_LEFT);
							if ($ord_ref){
								echo "<br>Your Reference: " .$ord_ref. " "; 
							}
							echo "</td></tr></table></div>";
						}
                        break;
						default:
						// Default message to show Order number and confirmation message
						echo "<br><div id=Message_Div align=center><table id=BlueTable><tr><td class=titlemedium>Thank you for placing your order with ";
	echo $Company_desc;
	echo " </td></tr><tr><td class=titlemedium>Please print this page for your reference.</td></tr>";
	echo "<tr><td class=titlemedium>Our reference: " .str_pad($ord, 8, "0", STR_PAD_LEFT);
	if ($ord_ref){
		echo "<br>Your Reference: " .$ord_ref. " "; 
	}
	echo "</td></tr></table></div>";
						
						
						
						break;
						}
	
}
include 'Reconnect.php';
?>
</form>

</div><!-- /mainbody -->
</div><!-- /content -->

<script type="text/javascript">
function ValidateForm() {
	return true;	
}
</script>
</BODY>


<?php
	//include closedb class
	include 'B2Bclosedb.php';
	
?>
