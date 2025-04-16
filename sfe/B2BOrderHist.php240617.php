<?php
	
	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//include html headers
	include 'B2BHeader.inc';
	
	$cust = $_SESSION['customerid'];
	//get session id
	$session = session_id();
	
?>

<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Verdana; font-size:10">


<?php
	//include menu class
	include 'B2BMenu.php';
	//include global functions class
	include 'B2BFunctions.php'; 
?>

<div id="content">
<!-- blank_sidebar_SB  -->
<div id="sidebar">

<?php
echo "<img src=";
echo GetResource('titlebarhdrimg');
echo ">"; 
?>
<ul>
<li><h2 align=center>Order History<h2></li>


</ul>

<?php
include 'B2BSbarFtr.inc';
?>
</div><!-- /sidebar -->
<div id="mainbody">

<?php
include 'Reconnect.php';


	$query="call ShowOrderHistory($cust);";	


	
		//run query
	$srchresult=mysql_query($query);
	$num=mysql_numrows($srchresult);
	
	if ($num>0) {
		echo "<br><table id=BlueTable align=center>";
		echo "<tr><td CLASS=maintitle>Order History</td></tr> ";
		echo "<tr><td>";
		
		
		$i=0;
		$j=0;
		$order_id = "";
		//loop round results
		while ($i < $num) {
			if (mysql_result($srchresult,$i,"order_id")==$order_id) {
			} else {
					$j = 1;	
		}
			$j = $j + 1;
			//write one row of table
			if ($j/2 == round($j/2)) {
				$tdclass = 'even';
			} else {
				$tdclass = 'odd';
			} 
			
			if (mysql_result($srchresult,$i,"order_id")==$order_id) {
			} else {
				echo "<table align=center>";
				if ($i == 0) {
				} else {
					echo "<tr><th class=orderheader>&nbsp</th>";
					echo "<tr><th class=orderheader>&nbsp</th>";
			}
				echo "<tr><th class=titlemedium>Order&nbsp;ID</th>";
				echo "<th class=titlemedium>Order&nbsp;Date</th>";
				echo "<th class=titlemedium>Order&nbsp;Ref</th>";
				echo "<th class=titlemedium>Stock&nbsp;Code</th>";
				echo "<th class=titlemedium>Quantity</th>";
				echo "<th class=titlemedium>Price</th>";	
				echo "<tr><td align=right class=$tdclass > " .str_pad(mysql_result($srchresult,$i,"order_id"), 8, "0", STR_PAD_LEFT). "</td>";
  				echo "<td class=$tdclass nowrap=true>&nbsp;" . mysql_result($srchresult,$i,"order_date"). "&nbsp;</td>";
				echo "<td class=$tdclass >" .str_replace(","," ",mysql_result($srchresult,$i,"order_ref")). "</td>";
  				echo "<td width=200 class=$tdclass  >" .mysql_result($srchresult,$i,"stockcode"). "</td>";
				echo "<td align=right class=$tdclass >" .mysql_result($srchresult,$i,"qty"). "</td>";
				echo "<td width=75 align=right class=$tdclass >&nbsp;" .mysql_result($srchresult,$i,"price"). "&nbsp;</td>";
				echo "<td align=right class=ordheader></td></tr>" ;
				$j = 0;
			} 
				if (mysql_result($srchresult,$i,"order_id")==$order_id) {
					echo "<tr>";	
					echo "<td align=right class=$tdclass></td>";
					echo "<td align=right class=$tdclass></td>" ;
					echo "<td align=right class=$tdclass></td>" ;
					echo "<td width=200 class=$tdclass  >" .mysql_result($srchresult,$i,"stockcode"). "</td>";
					echo "<td align=right class=$tdclass >" .mysql_result($srchresult,$i,"qty"). "</td>";
					echo "<td width=75 align=right class=$tdclass >&nbsp;" .mysql_result($srchresult,$i,"price"). "&nbsp;</td>";
					echo "</tr>";
			} else {
				$order_id=(mysql_result($srchresult,$i,"order_id"));
			
			
			}		
			$i++;
		}  		
	echo "</table>";
	echo "</td></tr>";
	echo "</table>";
	}
?>

</div><!-- /mainbody -->
</div><!-- /content -->

<script type="text/javascript">
ValidateForm() {
	alert("dfdff");
	return true;	
}
</script>
</BODY>


<?php
	//include closedb class
	include 'B2Bclosedb.php';
	
?>
