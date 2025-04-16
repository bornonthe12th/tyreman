<?php
	//line added to stop IE and Firefox errors displaying when the user clicks <Back> button on browser from Basket - 06/06/08
	ini_set('session.cache_limiter','private');
		
	//include error class
	include 'tmanerror.php';
	//include connect class
	include 'B2Bconnect.php';
	//include html headers
	include 'B2BHeader.php';
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
	

	
	// We need to store the selected branch as if it differs from the default branch then 
	// we need to load the prices for the default branch as well as the stock for the selected branch
	if(isset($_POST['branch']))
		$_SESSION['selected_branch'] = $_POST['branch'];
	//else
		//$_SESSION['selected_branch'] = '';
	
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
	if (isset($_POST['sptype'])) {
		$sptype = $_POST['sptype'];
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

		if($_SESSION['savoy']) $spdisp='X'; 	// SC 150208 savoy require this to be set to both if it can't be set in Tyreman system
				
		//reconnect
		include 'Reconnect.php';
		
	}	
	
?>

<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Verdana; font-size:10">


<?php
// Disable Right click for company 5 - BA Bush
$companyid = $_SESSION['companyid'];
switch ($companyid)
               {
                        case 5:
						?>
                        
                        <script language=JavaScript>
						var message="Right Mouse Click Disabled!";
						
						///////////////////////////////////
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
						
						// --> 
						</script>
                        <?php
                        break;
						
				}
?>
						
						






<script type="text/javascript" src="/scripts/wz_tooltip.js"></script> 

<?php
	//include menu class
	include 'B2BMenu.php';
	//include global functions class
	include 'B2BFunctions.php';
?>

<div id="content">
<!-- blank_sidebar_SB  -->
<div id="sidebar">

<!--<img src=/images/graphics/smsavoy.jpg>-->
<?php
	if($spdisp == 'S' && isset($_SESSION['savoy']))
	{
		echo '<img src="/images/graphics/smsavoy_blank.gif">';
	}
	else
	{
		echo "<img src=";
		echo GetResource('titlebarhdrimg');
		echo ">"; 
	}
	
	//if(isset($_SESSION['savoy']) && isset($_SESSION['savoy_account_number'])) // show account number for savoy customers
	//	echo $_SESSION['savoy_account_number'];
	
		
	//if customer on stop display 'On Stop' message	
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
<ul>


<li><h2>Product Search</h2></li>



<form name="searchForm" onSubmit="return ValidateForm()" method=POST action=B2BProdSearch.php>
<li>Size</li> 
<li><input type=text maxlength=16 name=size size=25 onMouseOver="Tip('An entry of 1956515V would result</br>in a search for all 1956515 tyres which</br>have a V speed rating.</br></br>You can also remove the speed rating<br>which would show all 1956515 tyres</br>regardless of the tyre speed rating.</br></br>For a wider search an entry of 195 or </br>19565 is also allowed.<br></br>% will show ALL stock for the selected<br>product type</br>')" value="<?php echo $size; ?>"></input></li>


<!-- savoy do not require description, therefore only display for other customers. SC 150208 -->
<?php if(!$_SESSION['savoy']) { ?>

<li>Description</li>
<li><input type=text maxlength=20 name=sdesc size=25 onMouseOver="Tip('An entry of 195 would result in</br>a search for all stock with a</br>description starting with 195.</br></br>An entry of %565% would result<br>in a search for all stock with a 565</br>anywhere in the description.</br>')" value="<?php echo $sdesc; ?>"></input></li>
<?php } ?>

<!--<li>Product Group</li>

<li><input type=text maxlength=20 name=spgroup size=25 value="<?php echo $spgroup; ?>"></input></li>-->

<input type=hidden name=spgroup value="">

<li>Manufacturer</li>

<li><input type=text maxlength=20 name=sman size=25 onMouseOver="Tip('An entry of MIC would result in a</br>search for stock with a </br>manufacturer starting with MIC,</br>for example MICHELIN.</br><br>An entry of M would result in a</br>search for stock with a</br>manufacturer starting with M,</br>for example MICHELIN & <br>MARANGONI.</br>')" value="<?php echo $sman; ?>"></input></li>

<li>Product Type</li>
<li><select name=sptype>

<?php

//reconnect 
include 'Reconnect.php';


//include product type dropdown



include 'ProdTypeList.php';				// NB SC mod in this script also

echo "</select></li>";

//echo "<li><input type=checkbox name="sspecflag" value=Y>Only Specials</input></li>-->";




?>

<!-- savoy do not require stockcode, therefore only display for other customers. SC 150208 -->
<?php if(!$_SESSION['savoy']) { ?>
<li>Stockcode</li>
<li><input type=text maxlength=20 name=scode size=25 value="<?php echo $scode; ?>"></input></li>
<?php } ?>

<?php
//build branch drop down or button or show current branch accordingly
include 'BranchList.php';



echo "<li>Show Price</li>";
  
echo "<li><input type=radio name=spdisp value=B ";
if ($spdisp =="B"){
	echo "checked=yes";	
}
echo " >Cost</input> ";

echo "<input type=radio name=spdisp value=S ";
if ($spdisp =="S"){
	echo "checked=yes";	
}
echo " >Sell</input>";

echo "<input type=radio name=spdisp value=X ";
if ($spdisp =="X"){
	echo "checked=yes";	
}
echo " >Both</input></li>";



echo "<li><input type=checkbox name=szstockflag value=Y " ;
if ($szstockflag =="Y" || ($_SESSION['savoy'] && !isset($_SESSION['savoy_sz_set']))){					// SC 150208 added || $_SESSION['savoy'] as savoy always want this to be checked by default.
	echo "checked";	
	$_SESSION['savoy_sz_set']=TRUE;
}
echo " >Show Zero Br.Stk </input></li>";



/* Sort headings not required SC 7 4 2008 as javascript sort headings take care of this

echo "<li>Sort Results by</li>";
echo "<li><input type=radio name=sortprodlist value=M ";
if ($sortprodlist =="M"){
	echo "checked=yes";	
}
echo " >Manufacturer</input>";
echo "<li><input type=radio name=sortprodlist value=P ";
if ($sortprodlist =="P"){
	echo "checked=yes";	
}
echo " >Price</input> ";



echo "<li><input type=radio name=sortprodlist value=T ";
if ($sortprodlist =="T"){
	echo "checked=yes";	
}
echo " >Branch Stock</input> ";

*/

?>
<li><input type=submit value="Search"></input></li>
<li><hr></hr></li>
<li></li>


</ul>

<?php
include 'B2BSbarFtr.php';
?>

</form>




</div><!-- /sidebar -->
<div id="mainbody">

<?php
include 'Reconnect.php';
include 'ProdSrchResults.php';
?>
</div><!-- /mainbody -->
</div><!-- /content -->

<script type="text/javascript">
function ValidateForm() {
	if ((document.searchForm.scode.value !="") || (document.searchForm.sdesc.value !="") || (document.searchForm.sman.value !="") || (document.searchForm.size.value !="")) {
		return true;
	} else {
		alert("You must enter some search criteria");
		return false;
	}	
}
</script> 
</BODY>


<?php
	//include closedb class
	include 'B2Bclosedb.php';
	
?>
