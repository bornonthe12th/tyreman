<?php

// Javascript required for table sort headings
echo "<script type='text/javascript' src='scripts/common.js'></script>";
echo "<script type='text/javascript' src='scripts/css.js'></script>";
echo "<script type='text/javascript' src='scripts/standardista_table_sorting.js'></script>";

$companyid = $_SESSION['companyid'];

// company 5 - BA Bush
switch ($companyid) {
    case 5:
        $Special = 'http://www.bushtyresintranet.co.uk/bush.jpg';
        $SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
        break;
    case 11:
        $Special = 'http://www.bushtyresintranet.co.uk/endyke.jpg?dummy=48484848';
        $SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
        break;
    case 4:
        $Special = 'graphics/stw-prom-hdr.jpg';
        $SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
        break;
}

$vat = 20.0;

// Get price modifiers
$query = "CALL GetAccountDetails($cust);";
$srchresult = $conn->query($query);

if ($srchresult && $srchresult->num_rows > 0) {
    $accountDetails = $srchresult->fetch_assoc();
    $vatflag = $accountDetails['IncVatFlag'] ?? null;
    $markupval = $accountDetails['markupval'] ?? null;
    $markuppct = $accountDetails['markuppc'] ?? null;
    $DefToSellFlag = $accountDetails['DefToSellFlag'] ?? null;
    $Show_rrp = $accountDetails['Show_rrp'] ?? null;
    $Show_rrp4 = $accountDetails['Show_rrp4'] ?? null;
    $Hide_rrp = $accountDetails['hide_rrp'] ?? null;
    $Account_No = $accountDetails['Account_No'] ?? null;
    $show_cust_spec = $accountDetails['show_cust_specials'] ?? null;
}

include 'Reconnect.php';

$branch = $_SESSION['default_branch'];

if ($scode == '') {
    $query = "CALL StockSearchPromotion('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";
    $srchresult = $conn->query($query);

    if (!$srchresult) {
        die("Query failed: " . $conn->error);
    }

    $rows = $srchresult->fetch_all(MYSQLI_ASSOC);
    $num = count($rows);

    if ($num > 0) {
        echo "<table width='700' id=PromoTable align=center>";
        if ($show_cust_spec == 'Y') {
            echo "<tr><td><img src='$Special?dummy=48484848'></td></tr>";
            echo "<tr><td><marquee direction='left' onmouseover='this.stop()' onmouseout='this.start()'>$SpecialText</marquee></td></tr>";
        }

        echo "<tr><td CLASS=maintitle>&nbsp;<img src='images/promotion-icon.gif' />&nbsp;&nbsp;On Promotion</td></tr>";
        echo "<tr><td><table class=\"sortable\"><thead><tr>";
        echo "<th class=titlemedium>Description</th><th class=titlemedium>Mfr.</th><th class=titlemedium>Fuel</th><th class=titlemedium>Wet</th><th class=titlemedium>Noise</th><th class=titlemedium>TL</th>";

        if (($rows[0]['show_stock_flag'] ?? '') === "Y") {
            echo "<th class='titlemedium'>Co. Stk</th>";
        }

        echo "<th class=titlemedium>Cost</th><th class=titlemedium>Basket</th></tr></thead><tbody>";

        foreach ($rows as $i => $row) {
            $tr_row_class = ($i % 2 === 0) ? '' : " class='odd'";
            $td_col_class = "class='promoeulabel'";
            $td_price_col_class = "class='promoprice'";
            $productid = $row['product_id'];

            echo "<tr $tr_row_class>";
            echo "<td class='promotext'><img src='images/promotion-icon.gif' />&nbsp;&nbsp;" . substr($row['description'], 0, 80) . "</td>";
            echo "<td class='promotext'>{$row['manufacturer']}</td>";
            echo "<td $td_col_class >{$row['fuel_efficiency']}</td><td $td_col_class >{$row['wet_braking']}</td>";

            $noise = trim($row['decibels'], "\0 ");
            echo "<td $td_col_class >" . $noise . ($noise ? "db" : "") . "</td>";

            if (in_array($companyid, ['2', '3', '4', '5', '11', '16'])) {
                echo "<td $td_col_class >";
                $url = trim($row['url']);
                if ($url) {
                    echo "<a href='$url' target='_blank'><img src='images/TyreLabelIcon.jpg' width='16' height='16' onmouseover=\"Tip('Tyre Label')\"></a>";
                } elseif (trim($row['fuel_efficiency']) !== '' && trim($row['wet_braking']) !== '' && trim($row['vehicle_class']) !== '') {
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

            $custprice = ($_SESSION['selected_branch'] == $_SESSION['default_branch']) ? $row['netprice'] : 0;
            if (!$custprice) {
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
                $q = $conn->query("SELECT Enquiry_Only FROM customers WHERE Customer_id = '$cust'");
                $enquiry_only = ($q && $q->num_rows > 0) ? $q->fetch_assoc()['Enquiry_Only'] : 'N';

                if ($enquiry_only == 'Y') {
                    echo "n/a";
                } else {
                    echo "<a href='addtobasket.php?productid=$productid&qty=1&price=$buyprice'>Add</a>";
                }
            } else {
                echo "Call";
            }
            echo "</td></tr>";
        }

        echo "</tbody></table></td></tr></table>";
    } else {
        echo "<div id=Message_Div>No Promotions Found.</div>";
    }
} else {
    echo "<div id=Message_Div>YEAH!! Running PROMOTION PAGE</div>";
}
?>
