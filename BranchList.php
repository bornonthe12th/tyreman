<?php
//get default branch
$def_branch = $_SESSION['default_branch'];

$cust = $_SESSION['customerid']; 
//get session id
$session = session_id();

//use posted value if set
if (isset($_SESSION['selected_branch'])) {
		$branch = $_SESSION['selected_branch'];
} else {
		$branch = '';	
}

//get session id
$session = session_id();
$companyid = $_SESSION['companyid'];

//are they allowed to change branch
$chg_branch = GetCompanySetting('AllowBranchChange'); 
include 'Reconnect.php';

if ($chg_branch == 'Y')  {
	$basketEmpty = True;
	$query="call BranchList();";  	
	//run query
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	//loop round results
	if ($num>0) {
		echo "<li>Branch</li>";
		echo "<li><select name=branch";
		if ($companyid == '5') {
                        echo " onmouseover=\"Tip('Branch cannot be changed')\"";
                        $basketEmpty = False;
                }

		if (!IsBasketEmpty($cust,$session)) {
			echo " onmouseover=\"Tip('You may only change the branch</br>when your basket is empty.')\"";	 
			$basketEmpty = False;
		}
		echo ">";

		if ($basketEmpty){
		    
			$i=0;
			while ($i < $num) {
				echo "<option value=";
				echo mysql_result($result,$i,"branch_id");
				if ((($def_branch == mysql_result($result,$i,"branch_id"))AND ($branch=='')) OR ($branch ==mysql_result($result,$i,"branch_id"))){
					echo " selected ";	
				}	
				echo ">";
				echo mysql_result($result,$i,"description");
				//echo "</option>";
				$i++;
			}
		} else {
			//echo "<option value=''>";
			$i=0;
			while ($i < $num) {
				if ((($def_branch == mysql_result($result,$i,"branch_id"))AND ($branch=='')) OR ($branch == mysql_result($result,$i,"branch_id"))){
					echo "<option value=" . mysql_result($result,$i,"branch_id");
					echo " selected >" . mysql_result($result,$i,"description");	
				}	
				$i++;
			}
		}
		echo "</select></li>" . "\n";
	}
} else {
	//get branch 
	$query="call GetBranchName($def_branch);";  	
	//run query
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	//we got a result
	if ($num>0) {	
		echo "<li>".mysql_result($result,$i,"description")."</li>";
	}
}

?>
