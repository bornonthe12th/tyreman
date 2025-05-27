<?php

function getStockBand($BStockLvl) {
	if ($BStockLvl < 5) {
		return "<5";
	} elseif ($BStockLvl >= 5 && $BStockLvl <= 10) {
		return "5-10";
	} else {
		return ">10";
	}
}

function GetResource($description)
{
	include 'Reconnect.php';

	$description = $conn->real_escape_string($description);
	$query = "CALL GetResource('$description');";

	$result = $conn->query($query);
	if (!$result || $result->num_rows === 0) return null;

	$row = $result->fetch_assoc();
	return $row['url'] ?? null;
}

function GetCompanySetting($settingdesc)
{
	include 'Reconnect.php';

	$settingdesc = $conn->real_escape_string($settingdesc);
	$query = "CALL GetCompanySetting('$settingdesc');";

	$result = $conn->query($query);
	if (!$result || $result->num_rows === 0) return null;

	$row = $result->fetch_assoc();
	return $row['setting'] ?? null;
}

function GetDefaultBranch($cust)
{
	include 'Reconnect.php';

	$cust = $conn->real_escape_string($cust);
	$query = "CALL GetDefaultBranch('$cust');";

	$result = $conn->query($query);
	if (!$result || $result->num_rows === 0) {
		die("B2BFunctions.php : No value for default branch id<br /><br />$query");
	}

	$row = $result->fetch_assoc();
	return $row['default_branch_id'] ?? null;
}

function IsBasketEmpty($cust, $session)
{
	include 'Reconnect.php';

	$cust = (int)$cust;
	$session = $conn->real_escape_string($session);
	$query = "CALL ShowBasket($cust, '$session');";

	$result = $conn->query($query);
	if (!$result) return true;

	return $result->num_rows === 0;
}

function GetTotalStock($stckcde)
{
	include 'Reconnect.php';

	$stckcde = $conn->real_escape_string($stckcde);
	$query = "CALL CompanyWideStock('$stckcde');";

	$result = $conn->query($query);
	if (!$result || $result->num_rows === 0) return null;

	$row = $result->fetch_assoc();
	return $row['company_stock'] ?? null;
}

