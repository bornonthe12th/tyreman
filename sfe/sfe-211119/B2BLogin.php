<style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {color: #0000FF}
INPUT:focus {background: #FFFFC0;}
-->
</style>
<?php
	//include html headers
	require 'B2BHeader.inc';
?>

<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Verdana; font-size:10">

<div id="content">

 <!-- blank_sidebar_SB  -->
 <div id="sidebar">

 <a href="http://www.tyreman.co.uk/b2b.htm" target="_blank">
 <img src=/images/etyreman.gif border="0" 
 alt="Developed by Tyreman Software - Click image to visit our web site."></a>

 <?php
	require 'B2BSbarFtr.inc';	// get from library
 ?>

 </div><!-- /sidebar -->


 <div id="mainbody">
 <br>
 <form name="loginForm" onSubmit="return ValidateForm();" method=POST action=login.php>
 <table id="BlueTable" align=center>
 <tr><td CLASS="maintitle">B2B Login</td></tr>
 <tr><td>
  <table align=center>
  <?php
	//copy parms to local vars
  	if (isset($_GET['error'])) {
  		//copy parms to local vars
		$error = $_GET['error'];
  		if ($error =='Y') {
			echo "<tr><th class=titlemedium colspan=2>
				Invalid Username or Password<br>Please try again.</td><tr>";
		} elseif ($error =='S') {
			echo "<tr><th class=titlemedium colspan=2>
				There was a Session Problem<br>Please try again.</td><tr>";
		}
	}
  ?>

  <TR>
  <td>User Name</td>
  <td align=right><input type=text name=username style="width:125px;"></input></td>
  </tr>
  <tr>
  <td>Password</td><td align=right><input type=password name=password style="width:125px;"></input></td>
  </tr>
  <tr><td colspan=2 align=right><input type=submit value=Login></input></td>
  </tr>
  </table>
 </td></tr>
 </table>

 </form>

 <script language="javascript">
 function ValidateForm(){
     if((document.loginForm.username.value=="") || (document.loginForm.password.value==""))
	{
       alert("You MUST Enter a User Name and a Password.");
       return false;
       }
       return true;
   			}

 </SCRIPT>

</div><!-- /mainbody -->
</div><!-- /content -->
</BODY>
