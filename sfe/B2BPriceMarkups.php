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
<li><h2 align=center>Price Mark-up<h2></li>


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
	echo "<tr><td CLASS=maintitle>Retail Price Mark-up - Default</td></tr> ";
	echo "<tr><td>";
	echo "<FORM name=accform type=POST action=UpdateAccount.php onsubmit=ValidateForm();>";
	echo "<table>";
	echo "<tr><th class=titlemedium>" .ucfirst(mysql_result($srchresult,0,"title"));
	echo "&nbsp;" . ucfirst(mysql_result($srchresult,0,"first_name")) . "&nbsp;";
	echo ucfirst(mysql_result($srchresult,0,"surname"));
	echo "</th></tr>";
	echo "<tr><td>Default Mark Up percent</td><td><input type=text size=6 maxlength=6 value='" .
	mysql_result($srchresult,0,"markuppc" ) ."' name=markuppc> </input></td></tr>"; 
	echo "<tr><td>Default Mark Up Value</td><td><input type=text size=8 maxlength=8 value='" .
	mysql_result($srchresult,0,"markupval") ."' name=markupval></input></td></tr>";	
        
	
	echo "<tr><td></td><td align=center><input type=submit name=butsubmit Value=Update></td></tr>";
	echo "</table>";
	echo "</FORM>";
        echo "<tr><td CLASS=maintitle>Retail Price Mark-up - by Tyre Make and Size</td></tr> ";
        echo "<tr><td>";

        echo "<FORM name=mkselection type=POST action=MakeSizeResults.php>";
          echo "<table>";
            echo "</td></tr>";
            echo "<tr><td>Make</td><td>"; 
            echo "<select name=mktype id='mktype'>";
            include 'Reconnect.php';
            //include Make dropdown
	    include 'TyresMakeList.php';
            echo "<input type=submit name=selectmake Value='Select Make' >";
          echo "</table>";
        echo "</FORM>";

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
