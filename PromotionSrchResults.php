<?php //session_start();

// Javascript required for table sort headings

echo "<script type='text/javascript' src='scripts/common.js'></script>";
echo "<script type='text/javascript' src='scripts/css.js'></script>";
echo "<script type='text/javascript' src='scripts/standardista_table_sorting.js'></script>";

$companyid = $_SESSION['companyid'];

// company 5 - BA Bush
switch ($companyid)
               {
               case 5:
                $Special='http://www.bushtyresintranet.co.uk/bush.jpg';
                //$Special='images/bush.jpg';
		$SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
                break;

		case 11:
                $Special='http://www.bushtyresintranet.co.uk/endyke.jpg?dummy=48484848';
		$SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
                break;
				
		//STW
		case 4:
                $Special='graphics/stw-prom-hdr.jpg';
		$SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
                break;
                }

$vat = 20.0;


// Get price modifiers
$query = "CALL GetAccountDetails($cust);";
$srchresult = $conn->query($query);

if ($srchresult && $srchresult->num_rows > 0) {
    $row = $srchresult->fetch_assoc();

    $vatflag         = $row['IncVatFlag'] ?? null;
    $markupval       = $row['markupval'] ?? null;
    $markuppct       = $row['markuppc'] ?? null;
    $DefToSellFlag   = $row['DefToSellFlag'] ?? null;
    $Show_rrp        = $row['Show_rrp'] ?? null;
    $Show_rrp4       = $row['Show_rrp4'] ?? null;
    $Hide_rrp        = $row['hide_rrp'] ?? null;
    $Account_No      = $row['Account_No'] ?? null;
    $show_cust_spec  = $row['show_cust_specials'] ?? null;
}
//var_dump($Account_No);echo "<br/>\n";
	//reconnect
	include 'Reconnect.php';

//var_dump($Hide_rrp); echo "<br/>\n";
//if we have some search criteria
$branch = $_SESSION['default_branch'];
if ($scode == '')  
	{
		//call search proc
		$query="call StockSearchPromotion('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";

        $srchresult = $conn->query($query);

        if (!$srchresult) {
            die("Query failed: " . $conn->error);
        }

        $num = $srchresult->num_rows;

			
	
	//loop round results
	if ($num>0)
		{
		
		//products found
		$i=0;
		
		//write table header
	  	echo "<table width='700' id=PromoTable align=center>";
       if ($show_cust_spec == 'Y')
                {
                echo "<tr><td><img src='$Special?dummy=48484848'></td></tr>";
                echo "<tr><td><marquee direction='left' onmouseover='this.stop()' onmouseout='this.start()' >$SpecialText</marquee></td></tr>";
                }
		// Added 2 new rows to display daily special pic/message ALS 02.06.15
		// made display conditional daily special pic/message ALS 25.06.15
		echo "<tr><td CLASS=maintitle>&nbsp;<img src='images/promotion-icon.gif' />&nbsp;&nbsp;On Promotion</td></tr>";
		echo "<tr><td>";
		echo "<table class=\"sortable\">";
		echo "<thead><tr>";
		echo "<th class=titlemedium>Description</th>";
		echo "<th class=titlemedium>Mfr.</th>";
		echo "<th class=titlemedium>Fuel</th>";
		echo "<th class=titlemedium>Wet</th>";
		echo "<th class=titlemedium>Noise</th>";
		echo "<th class=titlemedium>TL</th>";

        $srchresult->data_seek(0); // Move pointer to the first row
        $row = $srchresult->fetch_assoc();

        if (($row['show_stock_flag'] ?? '') === "Y") {
            echo "<th class='titlemedium'>Co. Stk</th>";
        }


		echo "<th class=titlemedium>Cost</th>";
		echo "<th class=titlemedium>Basket</th></tr></thead>";

		echo "<tbody>";	/* table body starting (required by the js that 
				   does the table sort headings) */
            $rows = $srchresult->fetch_all(MYSQLI_ASSOC); // get all rows as associative arrays
            foreach ($rows as $i => $row) {
                $tr_row_class = ($i % 2 === 0) ? '' : " class='odd'";
                $td_col_class = "class='promoeulabel'";
                $td_price_col_class = "class='promoprice'";
                $productid = $row['product_id'];

                echo "\n<tr $tr_row_class >";
                echo "<td class='promotext'><img src='images/promotion-icon.gif' />&nbsp;&nbsp;" . substr($row['description'], 0, 80) . "</td>";
                echo "<td class='promotext'>{$row['manufacturer']}</td>";

                echo "<td $td_col_class >{$row['fuel_efficiency']}</td>";
                echo "<td $td_col_class >{$row['wet_braking']}</td>";

                $noise = trim($row['decibels'], "\0 ");
                echo "<td $td_col_class >" . $noise . ($noise ? "db" : "") . "</td>";

                if (in_array($companyid, ['2', '3', '4', '5', '11', '16'])) {
                    echo "<td $td_col_class >";
                    $url = trim($row['url']);
                    if ($url) {
                        echo "<a href='$url' target='_blank'><img src='images/TyreLabelIcon.jpg' width='16' height='16' onmouseover=\"Tip('Tyre Label')\"></a>";
                    } elseif (
                        $row['fuel_efficiency'] !== '' &&
                        $row['wet_braking'] !== '' &&
                        $row['vehicle_class'] !== ''
                    ) {
                        $label_url = "http://www.tyreman.co.uk/eulabel.php?id={$row['fuel_efficiency']}&id2={$row['wet_braking']}&id3={$row['noise_rating']}&id4={$row['decibels']}&id5={$row['vehicle_class']}&id6={$row['stockcode']}";
                        echo "<a href='$label_url' target='_blank'><img src='images/TyreLabelIcon.jpg' width='16' height='16' onmouseover=\"Tip('Tyre Label')\"></a>";
                    }
                    echo "</td>";
                }

                if ($row['show_stock_flag'] === 'Y') {
                    $ttlstock = GetTotalStock($row['stockcode']);
                    echo "<td style='padding:0px 2px; text-align:right; font-size:14px'>{$ttlstock}</td>";
                } elseif ($row['show_stock_flag'] === 'B') {
                    echo "<td style='padding:0px 2px; text-align:right;'>" . getStockBand($row['stocklevel']) . "</td>";
                }

                // Customer price logic
                if ($_SESSION['selected_branch'] == $_SESSION['default_branch']) {
                    $custprice = $row['netprice'];
                } else {
                    include 'Reconnect.php';
                    $stockcode = $conn->real_escape_string($row['stockcode']);
                    $q = $conn->query("SELECT netprice FROM prices WHERE stockcode = '$stockcode' AND customer_id = $cust");
                    $custprice = ($q && $q->num_rows > 0) ? $q->fetch_assoc()['netprice'] : 0;
                }

                $buyprice = $custprice;
                echo "<td $td_price_col_class>" . number_format($custprice, 2) . "</td>";

                echo "<td style='text-align:center;'>";
                if ($custprice > 0) {
                    include 'Reconnect.php';
                    $q = $conn->query("SELECT * FROM customers WHERE Customer_id = '$cust'");
                    $enquiry_only = 'N';
                    if ($q && $q->num_rows > 0) {
                        $enquiry_only = $q->fetch_assoc()['Enquiry_Only'];
                    }

                    // assuming $customer_stop is another query result, you'll need to define it
                    if (isset($customer_stop) && $customer_stop->num_rows > 0) {
                        $stop_row = $customer_stop->fetch_assoc();
                        if (!is_null($stop_row) && $stop_row['On_Stop_Flag'] == 'Y') {
                            echo "<span style='color:red;'>On Stop</span>";
                        } elseif ($enquiry_only == 'Y') {
                            echo "n/a";
                        } else {
                            echo "<a href='addtobasket.php?productid=$productid&qty=1&price=$buyprice'>Add</a>";
                        }
                    } else {
                        echo "<a href='addtobasket.php?productid=$productid&qty=1&price=$buyprice'>Add</a>";
                    }
                } else {
                    echo "Call";
                }
                echo "</td></tr>";
            }
            echo "</tbody></table>";
		echo "</td></tr>";
		echo "</table>";
	}
	else
		echo "<div id=Message_Div>No Promotions Found.</div>";

  }

else
	echo "<div id=Message_Div>YEAH!! Running PROMOTION PAGE</div>";

?>
