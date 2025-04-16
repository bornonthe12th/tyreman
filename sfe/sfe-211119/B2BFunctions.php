<?php

function getStockBand($BStockLvl) {
	//declare var
	if($BStockLvl < 5){
		$lBand = "<5";
	}elseif (($BStockLvl >= 5)AND($BStockLvl <= 10)) {
		$lBand = "5-10";
	}else {
		$lBand = ">10";
	}
	return $lBand;	
}

function GetResource($description)
{
	include 'Reconnect.php';
	$query="call GetResource('" . $description . "');";	
	//run query
	$result=mysql_query($query);
	$url = mysql_result($result,0,"url");
    return $url;
}

function GetCompanySetting($settingdesc)
{
	include 'Reconnect.php';
	$query="call GetCompanySetting('" . $settingdesc . "');";	
	//run query
	$result=mysql_query($query);
	$value = mysql_result($result,0,"setting");
    return $value;
}

function GetDefaultBranch($cust)
{

	include 'Reconnect.php';
	$query="call GetDefaultBranch('" . $cust . "');";	

	//run query
	$result=mysql_query($query);
	$value = mysql_result($result,0,"default_branch_id") 
	         OR die("B2BFunctions.php : No value for default branch id<br /><br />$query");

    return $value;

    
}

function IsBasketEmpty($cust,$session)
{
	include 'Reconnect.php';
	//reuse showbasket and just check if we get anything back
	$query="call ShowBasket($cust,'$session');";	
	//run query
	$srchresult=mysql_query($query);
	$num=mysql_numrows($srchresult);
	if ($num ==0){ return true;		
	} else  {return false;}
}

function GetTotalStock($stckcde)
{
	include 'Reconnect.php';
	//call stock total
	$query="call CompanyWideStock('$stckcde');";	
	//run query
	$result=mysql_query($query);
	$value = mysql_result($result,0,"Company_Stock");
	return $value;
}
?>
