<?php

	//line added to stop IE and Firefox errors displaying when the user clicks 
	//<Back> button on browser from Basket - 06/06/08
	//ini_set('session.cache_limiter','private');


	//include error class
	include 'tmanerror.inc';

	//include connect class
	include 'B2Bconnect.php';

	//include html headers
	include 'B2BHeader.inc';
	

	if (isset($_SESSION['customerid'])) 
	   {
		$cust = $_SESSION['customerid'];
	
	   } else {
		// Change to the URL you want to redirect to
		$URL="B2BLogin.php?error=S";
		session_write_close();
		header ("Location: $URL");		
	   }

	//get session id
	$session = session_id();
		
?>



<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Verdana; font-size:10">

<?php

	//include menu class
	include 'B2BMenu.php';

	//include global functions class
	include 'B2BFunctions.php'; 

	if (isset($_SESSION['order_ref'])) 
	   {
		$order_ref = $_SESSION['order_ref'];
	   } else {
		$order_ref = ""; 
	   }
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
<li><h2 align=center>Shopping Basket<h2></li>


<?php
include 'basket.inc';
?>
<li><br></li>

<form name=frmBasket action=UpdateBasket.php method=POST>
<li><input type=submit name=prdsrch value="Search Again" ></input></li>

<li><input type=submit name=updbasket value="Update Basket" ></input></li>

<li><input type=button name=clrbasket value="Empty Basket" onClick="EmptyBtnPress();"></input></li>
<li><br></li>




<?php
  	//only display 'Confirm Basket' button if basket has items in it
	if (!IsBasketEmpty($cust,$session))
        {

		echo '<li><input type=submit name=process value="Confirm Basket" ></input></li>';
		echo '<li><br></li>';
	}
?>



</ul>

<?php
require 'B2BSbarFtr.inc';
?>

</div><!-- /sidebar -->



<div id="mainbody">

<?php
include 'Reconnect.php';
include 'BasketContents.php';
?>

</form>

</div><!-- /mainbody -->
</div><!-- /content -->

<script type="text/javascript">
function ProcessBtnPress() {
	window.location.href="B2BOrderRef.php";	
}

function EmptyBtnPress() {
var answer = confirm("Empty Basket?")
	if (answer){
		window.location.href="B2BEmptyBasket.php"	
	}
	else{
		null;
	}	
}

function ValidateForm() {
	return true;	
}
</script>


</BODY>


<?php
	//include closedb class
	include 'B2Bclosedb.php';
	
?>
