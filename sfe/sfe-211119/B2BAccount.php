<?php

	//include error class
	include 'tmanerror.inc';
	//include connect class
	include 'B2Bconnect.php';
	//include html headers
	include 'B2BHeader.inc';
	
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

<!--<img src=/images/>--> 
<?php
echo "<img src=";
echo GetResource('titlebarhdrimg');
echo ">"; 
?>

<ul>
<li><h2 align=center>Account Details<h2></li>


</ul>

<?php
include 'B2BSbarFtr.inc';
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
	echo "<br><table id=AccountTable align=center>";
	echo "<tr><td CLASS=maintitle>Account Details</td></tr> ";
	echo "<tr><td>";
	echo "<FORM name=accform type=POST action=UpdateAccount.php onsubmit=ValidateForm();>";
	echo "<table>";
	echo "<tr><th class=titlemedium>" .ucfirst(mysql_result($srchresult,0,"title"));
	echo "&nbsp;" . ucfirst(mysql_result($srchresult,0,"first_name")) . "&nbsp;";
	echo ucfirst(mysql_result($srchresult,0,"surname"));
	echo "</th></tr>";
	echo "<tr><td>Mark Up percent</td><td><input type=text size=6 maxlength=6 value='" .
	mysql_result($srchresult,0,"markuppc" ) ."' name=markuppc> </input></td></tr>"; 
	echo "<tr><td>Mark Up Value</td><td><input type=text size=8 maxlength=8 value='" .
	mysql_result($srchresult,0,"markupval") ."' name=markupval></input></td></tr>";	

	echo "<tr><td>Include VAT</td>";
        echo "<td><input id='chkvat' type=checkbox name=incVATFlag value=Y ";
	if (mysql_result($srchresult,0,"incVATFlag") == "Y") 
	{
	  echo "checked";
	}
	echo " ><label for='chkvat'><span></span></label></td></tr>";	

	echo "<tr><td>Default To Sell out Price</td>";
        echo "<td><input id='chksell' type=checkbox name=DefToSellFlag value=Y ";
	if (mysql_result($srchresult,0,"DefToSellFlag") == "Y") 
	{
  	  echo "checked";	
	}
	echo " ><label for='chksell'><span></span></label></td></tr>";	

	//show RRP & Show RRP4 prompts, Added 26/01/11 by GR ///
	switch ($companyid)
        {
          case 1: break;
          case 2: break;
	  //Only Show RRP & RRP2 for BAB or Endyke
          case ($companyid=='5' || $companyid=='11') : 
            if (mysql_result($srchresult,0,"hide_rrp") !== "Y")
            {
              echo "<tr><td>Show RRP</td>";
              echo "<td><input id='chkrrp' type=checkbox name=Show_rrp value=Y ";
              if (mysql_result($srchresult,0,"Show_rrp") == "Y")
              {
                echo "checked";
              }
              echo " ><label for='chkrrp'><span></span></label></td></tr>";

              echo "<tr><td>Show RRP4</td>";
              echo "<td><input id='chkrrp4' type=checkbox name=Show_rrp4 value=Y ";
              if (mysql_result($srchresult,0,"Show_rrp4") == "Y")
              {
                echo "checked";
              }
              echo " ><label for='chkrrp4'><span></span></label></td></tr>";

	    }
              break;
          }
		
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
