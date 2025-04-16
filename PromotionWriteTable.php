<?php

if(session_id() == '')
    {
    session_start();
    }


$vat = $_SESSION['vat_rate'];
$branch = $_SESSION['default_branch'];
$cust = "864";
$file = "test.txt";

if (!unlink($file)) {
  echo ("Error deleting $file");
} else {
  echo ("Deleted $file");
}

       //connect to customer DB.
       $cust_db_conn = mysqli_connect("localhost", $_SESSION['dbusername'] ,$_SESSION['dbpassword'], $_SESSION['dbschema']) or die('unable to connect to b2b DB');

	//get price modifiers
	$query="call GetAccountDetails($cust);";

	//run query
	$srchresult=mysqli_query($cust_db_conn,$query) or die('lookup error');
        $num=mysqli_num_rows($srchresult);
        $row = mysqli_fetch_array($srchresult);

	if ($num > 0)
		{
                $vatflag = $row["IncVATFlag"];
                $markupval = $row["markupval"];
                $markuppct = $row["markuppc"];
                $DefToSellFlag = $row["DefToSellFlag"];
                $Show_rrp = $row["show_rrp"];
                $Show_rrp4 = $row["show_rrp4"];
                $Hide_rrp = $row["hide_rrp"];
                $Account_No = $row["Account_No"];
                $show_cust_spec = $row["show_cust_specials"];
                $show_stock_flag = $row["show_stock_flag"];
	    	}
$Wstring = "$vatflag,$markupval";

$myfile = file_put_contents('test.txt', $Wstring.PHP_EOL , FILE_APPEND | LOCK_EX);

?>
