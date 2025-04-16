<?php

	//include error class
	include 'tmanerror.php';
	//include connect class
	include 'B2Bconnect.php';
	//include html headers
	include 'B2BHeader.php';
	
	$cust = $_SESSION['customerid'];
	$companyid = $_SESSION['companyid'];
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

<!--<img src=/images/graphics/smsavoy.jpg>--> 
<?php
echo "<img src=";
echo GetResource('titlebarhdrimg');
echo ">"; 
?>

<ul>
<li><h2>Account Details<h2></li>


</ul>

<?php
include 'B2BSbarFtr.php';
?>
</div><!-- /sidebar -->
<div id="mainbody">

<?php
include 'Reconnect.php';	

	$query="call GetAccountDetails($cust);";	
	//run query
	$srchresult=mysql_query($query);
	$num=mysql_numrows($srchresult);
	
	if ($num>0) {
		echo "<br><table id=BlueTable align=center>";
		echo "<tr><td CLASS=maintitle>Account Details</td></tr> ";
		echo "<tr><td>";
		echo "<FORM name=accform type=POST action=UpdateAccount.php onsubmit=ValidateForm();>";
		echo "<table align=center>";
		echo "<tr><th class=titlemedium colspan=2>" .ucfirst(mysql_result($srchresult,0,"title"));
		echo "&nbsp;" . ucfirst(mysql_result($srchresult,0,"first_name")) . "&nbsp;";
		echo ucfirst(mysql_result($srchresult,0,"surname"));
		echo "</th></tr>";
		//echo "<tr><td>Email:&nbsp;</td><td><input type=text size=35 value='" .mysql_result($srchresult,0,"email") ."' name=email> </input></td></tr>";	
//echo "session=" .$_SESSION;

		
		if(!$_SESSION['savoy'])
		{
			echo "<tr><td>&nbsp;&nbsp;Mark Up percent&nbsp;</td><td>&nbsp&nbsp&nbsp<input type=text value='" .mysql_result($srchresult,0,"markuppc" ) ."' name=markuppc> </input>&nbsp&nbsp</td></tr>"; 
			echo "<tr><td>&nbsp;&nbsp;Mark Up Value&nbsp;</td><td>&nbsp&nbsp&nbsp<input type=text value='" .mysql_result($srchresult,0,"markupval") ."' name=markupval></input>&nbsp&nbsp</td></tr>";	
		}

		echo "<tr><td>&nbsp;&nbsp;Include VAT&nbsp;</td><td align=center><input type=checkbox value=Y ";
		if (mysql_result($srchresult,0,"incVATFlag") == "Y") 
		{
		echo "checked";
		}
		echo " name=incVATFlag></input></td></tr>";	
		echo "<tr><td>&nbsp;&nbsp;Default To Sell out Price&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp;</td><td align=center><input type=checkbox value=Y ";
		if (mysql_result($srchresult,0,"DefToSellFlag") == "Y") 
		{
		echo "checked";	
		}

		echo " name=DefToSellFlag></input></td></tr>";	

		////////////show RRP & Show RRP4 prompts, Added 26/01/11 by GR ///////////////
                 switch ($companyid)
                        {
                        case 1:
                        //echo $companyid; 
                        break;

                        case 2:
                        //echo $companyid; 
                        break;

                        case 5: 
                        //echo $companyid; 
  
			echo "<tr><td>&nbsp;&nbsp;Show RRP&nbsp;</td><td align=center><input type=checkbox value=Y ";
			if (mysql_result($srchresult,0,"Show_rrp") == "Y") 
			{
				echo "checked";	
			}
			echo " name=Show_rrp></input></td></tr>";	
	
			echo "<tr><td>&nbsp;&nbsp;Show RRP4&nbsp;</td><td align=center><input type=checkbox value=Y ";
			if (mysql_result($srchresult,0,"Show_rrp4") == "Y") 
			{
			echo "checked";	
			}
	
			echo " name=Show_rrp4></input></td></tr>";	
                        break;

                        }

		/////////////////////////////////////////////////////////////////////////
		
		if($_SESSION['savoy'])
			include 'savoy_show_markups.php';
		
		echo "<tr><td></td><td align=center><input type=submit name=butsubmit Value=Update></td></tr>";
		echo "</table>";
		echo "</FORM>";
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
