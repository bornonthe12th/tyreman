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
<ul>

<!--Commented out Product search text to make room for wet/xl/rf filter-->
<!--<li>Product Search</li>-->



<form name="searchForm" onSubmit="return ValidateForm()" method=POST action=B2BProdSearch.php>
<li>Size</li> 
<li><input type=text maxlength=16 name=size placeholder="Select.." 
    onMouseOver="Tip('An entry of 1956515V would result</br>in a search for all 1956515 tyres which</br>have a V speed rating.</br></br>You can also remove the speed rating<br>which would show all 1956515 tyres</br>regardless of the tyre speed rating.</br></br>For a wider search an entry of 195 or </br>19565 is also allowed.<br></br>% will show ALL stock for the selected<br>product type</br>')" value="<?php echo $size; ?>"></input></li>


<li>Description</li>
<li><input type=text maxlength=20 name=sdesc placeholder="Select.." 
    onMouseOver="Tip('An entry of 195 would result in</br>a search for all stock with a</br>description starting with 195.</br></br>An entry of %565% would result<br>in a search for all stock with a 565</br>anywhere in the description.</br>')" value="<?php echo $sdesc; ?>"></input></li>

<!--<li>Product Group</li>

<li><input type=text maxlength=20 name=spgroup size=25 value="<?php echo $spgroup; ?>"></input></li>-->

<input type=hidden name=spgroup value="">

<li>Manufacturer</li>

<li><input type=text maxlength=20 name=sman placeholder="Select.." onMouseOver="Tip('An entry of MIC would result in a</br>search for stock with a </br>manufacturer starting with MIC,</br>for example MICHELIN.</br><br>An entry of M would result in a</br>search for stock with a</br>manufacturer starting with M,</br>for example MICHELIN & <br>MARANGONI.</br>')" value="<?php echo $sman; ?>"></input></li>

<li>Stock Code</li>
<li><input type=text maxlength=20 name=scode placeholder="Select.." value="<?php echo $scode; ?>"></input></li>

<li></li>
<li></li>
<li></li>
<li></li>
<li>Product Type</li>
<li>

<?php
//reconnect 
include 'Reconnect.php';

//include product type dropdown
include 'ProdTypeList.php';				
?>

</li>


<?php
//build branch drop down or button or show current branch accordingly
include 'BranchList.php';

echo "<li></li>";
echo "<li></li>";
echo "<li></li>";
echo "<li></li>";

echo "<li>";
  
echo "<input id=cost type=radio name=spdisp value=B ";
if ($spdisp =="B"){
	echo "checked=yes";	
	}
echo "><label for=cost>&nbsp;Cost&nbsp;&nbsp;&nbsp;&nbsp;</label>";

echo "<input id=sell type=radio name=spdisp value=S ";

if ($spdisp =="S"){
	echo "checked=yes";	
	}
echo "><label for=sell>&nbsp;Sell&nbsp;&nbsp;&nbsp;&nbsp;</label>";

echo "<input id=both type=radio name=spdisp value=X ";

if ($spdisp =="X"){
	echo "checked=yes";	
	}
echo "><label for=both>&nbsp;Both</label>";

echo "</li";
echo "<li></li>";
echo "<li></li>";
echo "<section>";
echo "<li></li>";

if ($companyid !== '5' and $companyid != '11' )
{
echo "<li>Include</li>";
}

echo "<input id='chk1' type='checkbox' name=szstockflag value=Y ";
if ($szstockflag =="Y" )
      {
        echo "checked";
      }
echo "><label for='chk1'>";
echo "<span></span>";
echo "Zero Stock Items	";
echo "<ins><i>Zero Stock Items</i></ins>";
echo "</label>";


if ($companyid == '2' or $companyid == '3' or $companyid == '5' or $companyid == '11' or $companyid == '16')
{



echo "<input id='chk2' type='checkbox' name=winterfilter value=Y ";
if ($winterfilter =="Y" )
      {
        echo "checked";
      }
echo "><label for='chk2'>";
echo "<span></span>";
echo "Winter Tyres";
echo "<ins><i>Winter Tyres</i></ins>";
echo "</label>";

echo "<input id='chk3' type='checkbox' name=xlfilter value=Y ";
if ($xlfilter =="Y" )
      {
        echo "checked";
      }
echo "><label for='chk3'>";
echo "<span></span>";
echo "Extra Load Tyres";
echo "<ins><i>Extra Load Tyres</i></ins>";
echo "</label>";

echo "<input id='chk4' type='checkbox' name=rffilter value=Y ";
if ($rffilter =="Y" )
	{
     	  echo "checked";
       	}
echo "><label for='chk4'>";
echo "<span></span>";
echo "Run Flat Tyres";
echo "<ins><i>Run Flat Tyres</i></ins>";
echo "</label>";
echo "</section>";
}

?>

<li></li>

<li><input type=submit value="Search" ></input></li>
<li></li>

</ul>


<?php
require 'B2BSbarFtr.inc';
?>

</form>


<?php
switch ($companyid)
               {
               case ($companyid=='5' || $companyid=='11'):
                ?>
<ul>
<li></li>
</ul>
<A HREF='key.html' onClick="return popup(this, 'KeyInformation')"><img src='images/keybutton.gif' height="18" width="92"></A>;
                <?php                                   // end script

break;

                }
?>

</div><!-- /sidebar -->
<div id="mainbody">

<?php
// Run normal search page unless no search made and customer BAB, in which case run stock promotions page.
include 'Reconnect.php';
if (($size == '') and ($scode == '') and ($sdesc == '') and ($sman == '') and ($companyid == '5' or $companyid == '11')) {
include 'PromotionSrchResults.php';
 } else {
include 'ProdSrchResults.php';
}
?>
</div><!-- /mainbody -->
</div><!-- /content -->

<script type="text/javascript">
function ValidateForm() {
	if ((document.searchForm.scode.value !="") 
		|| (document.searchForm.sdesc.value !="") 
		|| (document.searchForm.sman.value !="") 
		|| (document.searchForm.size.value !="")) {
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
