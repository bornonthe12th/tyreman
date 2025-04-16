<?php

	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//include html headers
	include 'B2BHeader.inc';
	
	if (isset($_SESSION['customerid'])) {
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
?>

<div id="content">
<!-- blank_sidebar_SB  -->
<div id="sidebar">

<?php
include 'B2BSbarFtr.inc';
echo "<img src=";
echo GetResource('titlebarhdrimg');
echo ">"; 
?>
</div><!-- /sidebar -->
<div id="mainbody">

<form action=B2BProcessOrder.php method=POST>
<?php
	echo '<table id=BlueTable width="245" >';
	echo '<tr><td align="center" CLASS=maintitle>Your Comments / Instructions</td></tr>';
    echo '<tr><td align="center"><input type=text maxlength=32  size=32 name=comments1 /></td></tr>';
    echo '<tr><td align="center"><input type=text maxlength=32  size=32 name=comments2 /></td></tr>';
    echo '<tr><td align="center"><input type=text maxlength=32  size=32 name=comments3 /></td></tr>';
    echo '<tr><td align="center"><input type=text maxlength=32  size=32 name=comments4 /></td></tr>';
	echo '<tr><td align="center" CLASS=maintitle></td></tr>';
    echo '<tr><td align="center" CLASS=maintitle>Your Reference &nbsp;<input type=text name=ordref size="10" maxlength="10" /></td></tr>';
	echo '<tr><td align="center"><input type=submit name=process value="Complete Order" /> ';
	echo '<input type="button" value="Cancel" onClick="history.back()" class="button"></td></tr>';
	echo '<tr><td style="color:red;font-size:12px;">Note: Clicking the \'Complete Order\' button will place your order!</td></tr>';
	echo '</table>';
?>
</form>
</div><!-- /mainbody -->
</div><!-- /content -->

</BODY>

<?php
	//include closedb class
	include 'B2Bclosedb.php';	
?>
