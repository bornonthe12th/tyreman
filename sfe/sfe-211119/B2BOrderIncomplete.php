<HEAD>
<SCRIPT TYPE="text/javascript">
<!--
function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=400,height=600,scrollbars=yes');
return false;
}
//-->
</SCRIPT>
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
	//get post vars into local ones
	if (isset($_SESSION['customerid'])) {
		$cust = $_SESSION['customerid'];
	   } else {
		// Change to the URL you want to redirect to
		$URL="B2BLogin.php?error=S";
		session_write_close();
		header ("Location: $URL");		
	   }
	
	$cust = $_SESSION['customerid'];
	$tablesort = '';
	

	
	/* We need to store the selected branch as if it differs from the default branch then 
	   we need to load the prices for the default branch as well as the stock for the 
	   selected branch */
	if(isset($_POST['branch']))
		$_SESSION['selected_branch'] = $_POST['branch'];
	
	if(isset($_POST['sptype']))
		$_SESSION['sptype'] = $_POST['sptype'];
	
	if (isset($_POST['scode'])) {
		$scode = $_POST['scode'];
	   } else {
		$scode = '';	
	   }
	if (isset($_POST['size'])) {
		$size = $_POST['size'];
	   } else {
		$size = '';	
	   }
	if (isset($_POST['sdesc'])) {
		$sdesc = $_POST['sdesc'];
	   } else {
		$sdesc = '';	
	   }
	if (isset($_POST['spgroup'])) {
		$spgroup = $_POST['spgroup'];
	   } else {
		$spgroup = '';	
	   }
	if (isset($_POST['sman'])) {
		$sman = $_POST['sman'];
	   } else {
		$sman = '';	
	   }
	if (isset($_SESSION['sptype'])) {
		$sptype = $_SESSION['sptype'];
	   }  else {
		$sptype = '';	
	   }
	if (isset($_POST['sspecflag'])) {
		$sspecflag = $_POST['sspecflag'];
	   } else {
		$sspecflag = '';	
	   }
	if (isset($_POST['szstockflag'])) {
		$szstockflag = $_POST['szstockflag'];
	   } else {
		$szstockflag = '';	
	   }
	   
	if (isset($_POST['winterfilter'])) {
		$winterfilter = $_POST['winterfilter'];
	   } else {
		$winterfilter = '';	
	   }
	   
	   if (isset($_POST['xlfilter'])) {
		$xlfilter = $_POST['xlfilter'];
	   } else {
		$xlfilter = '';	
	   }
	   
	   if (isset($_POST['rffilter'])) {
		$rffilter = $_POST['rffilter'];
	   } else {
		$rffilter = '';	
	   }
	
	if (isset($_POST['sortprodlist'])) {
		$sortprodlist = $_POST['sortprodlist'];
	   } else {
		$sortprodlist = '';	
	   }
	
	if (isset($_POST['spdisp'])) {
		$spdisp = $_POST['spdisp'];
	   } else { //not set whats default for customer
		$DefToSellFlag = "";
		//get account details	
		$query="call GetAccountDetails($cust);";  	
		//run query
		$srchresult=mysql_query($query);
		$num=mysql_num_rows($srchresult);
		if ($num > 0){
			$DefToSellFlag = mysql_result($srchresult,0,"DefToSellFlag");
		             }
		if ($DefToSellFlag == 'Y') {
			$spdisp = 'S';	
		   } else {
			$spdisp = 'B';	
		   }

		//reconnect
		include 'Reconnect.php';
		
	}	
header("Cache-Control: no-cache");	
?>

<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Veranda; font-size:10">


<?php
// Disable Right click for company 5 - BA Bush
$companyid = $_SESSION['companyid'];
switch ($companyid)
               {
               case ($companyid=='5' || $companyid=='11'):
		?>
               	<script language=JavaScript>		//start script
		var message="Right Mouse Click Disabled!";
						
		function clickIE4(){
		if (event.button==2){
			alert(message);
			return false;
				   }
				}
						
		function clickNS4(e){
		if (document.layers||document.getElementById&&!document.all){
		   if (e.which==2||e.which==3){
		      alert(message);
		      return false;
					}
				}
			}
						
		if (document.layers){
		   document.captureEvents(Event.MOUSEDOWN);
	  	   document.onmousedown=clickNS4;
		     		    }
		   else if (document.all&&!document.getElementById){
			   document.onmousedown=clickIE4;
						}
						
		document.oncontextmenu=new Function("alert(message);return false")
						
		</script>				
                <?php 					// end script

break;
						
		}
?>
<script type="text/javascript" src="/scripts/wz_tooltip.js"></script> 
<?php

	//include menu class
	include 'B2BMenu.php';
	//include global functions class
	require 'B2BFunctions.php';
?>

<div id="content">
<!-- blank_sidebar_SB  -->
<div id="sidebar">

<?php
        if($spdisp == 'S')
        {
                echo "<img src=";
                echo GetResource('alt_image');
                echo ">";
        }
        else
        {
                echo "<img src=";
                echo GetResource('titlebarhdrimg');
                echo ">";
        }

	//reconnect
	include 'Reconnect.php';
	$query="SELECT * FROM customers WHERE Customer_id = '".$cust."'";  	
	//run query
	$customer_stop=mysql_query($query) or die(mysql_error());
	$num=mysql_numrows($customer_stop);
	if ($num > 0){
		if(mysql_result($customer_stop,0,"On_Stop_Flag") == 'Y') {
			echo '<br /><br /><p class="stop">** ON STOP **</p>';
				}
		     }
?>

<?php
require 'B2BSbarFtr.inc';
?>

</form>


</div><!-- /sidebar -->
<div id="mainbody">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><div align="center"><img src="images/ShoppingCartError.jpg"></a></div>
    </td>
  </tr>
  <tr>
    <td align="center"><a>There was a problem in transmitting your order.</a>
    </td>
  </tr>
  <tr>
    <td align="center"><a>Please try again.</a>
    </td>
  </tr>
  <tr>
    <td align="center"><a>If the problem persists please contact TYREMAN on 0845 402 7702.</a>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>

</div><!-- /mainbody -->
</div><!-- /content -->

</BODY>

<?php
	//include closedb class
	include 'B2Bclosedb.php';
?>
