<?php

// Javascript required for table sort headings
echo "<script type='text/javascript' src='scripts/common.js'></script>";
echo "<script type='text/javascript' src='scripts/css.js'></script>";
echo "<script type='text/javascript' src='scripts/standardista_table_sorting.js'></script>";

// Javascript for tool tip text pop up
echo "<script type='text/javascript' src='/scripts/wz_tooltip.js'></script>";

$companyid = $_SESSION['companyid'];
$vat = 20.0;

if ($spdisp != 'B' or $spdisp != "S") {
    $query = "call GetAccountDetails($cust);";
    $srchresult = mysqli_query($conn, $query);
    $num = mysqli_num_rows($srchresult);
    if ($num > 0) {
        mysqli_data_seek($srchresult, 0);
        $srchresult_row0 = mysqli_fetch_assoc($srchresult);
        $vatflag = $srchresult_row0["IncVatFlag"];
        $markupval = $srchresult_row0["markupval"];
        $markuppct = $srchresult_row0["markuppc"];
        $DefToSellFlag = $srchresult_row0["DefToSellFlag"];
        $Show_rrp = $srchresult_row0["Show_rrp"];
        $Show_rrp4 = $srchresult_row0["Show_rrp4"];
        if (!$srchresult_row0["hide_rrp"]) {
            $Hide_rrp = "n";
        } else {
            $Hide_rrp = $srchresult_row0["hide_rrp"];
        }
        $Account_No = $srchresult_row0["Account_No"];
    }

    include 'Reconnect.php';
}

if (($scode) OR ($sdesc) or ($sman) or ($size)) {
    if ($_SESSION['selected_branch'] != $_SESSION['default_branch']) {
        $query = "call StockSearchBranch('$scode',$cust,'$sdesc','$spgroup',
            '$sman','$sptype','$sspecflag','$szstockflag','$size',
            '$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";
    } else {
        $query = "call StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman',
          '$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";
    }

    $srchresult = mysqli_query($conn, $query) or die(mysqli_error($conn));
    $num = mysqli_num_rows($srchresult);

    if (($num > 0) && ($_SESSION['selected_branch'] != $_SESSION['default_branch'])) {
        include 'Reconnect.php';

        $query2 = "call StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman',
            '$sptype','$sspecflag','$szstockflag','$size','$sortprodlist',
            '$_SESSION[default_branch]','$winterfilter','$xlfilter','$rffilter');";
        $srchresult2 = mysqli_query($conn, $query2);
        $num2 = mysqli_num_rows($srchresult2);
    }

    if ($num > 0) {
        $i = 0;
        echo "<table id=BlueTable >";
        echo "<tr><td CLASS=maintitle>Search Results</td></tr>";
        echo "<tr><td>";
        echo "<table class=\"sortable\">";
        echo "<thead><tr>";
        echo "<th class=stcodetitle>Stock Code</th>";
        echo "<th class=titlemedium>Description</th>";
        echo "<th class=titlemedium>Manufacturer</th>";
        echo "<th class=titlemedium>Fuel</th>";
        echo "<th class=titlemedium>Wet</th>";
        echo "<th class=titlemedium>Noise</th>";
        echo "<th class=titlemedium>TL</th>";

        if ($companyid == '5') {
            echo "<th class=titlemedium>Image</th>";
        }

        echo "<th class=titlemedium>W</th>";
        echo "<th class=titlemedium>XL</th>";
        echo "<th class=titlemedium>RF</th>";
        if ($srchresult_row0["show_stock_flag"] == "Y") {
            echo "<th class=titlemedium>Br. Stk</th>";
            if ($companyid == '5' or $companyid == '11') {
                echo "<th class=titlemedium>Hub Stk</th>";
            }
            if ($companyid != '11') {
                echo "<th class=titlemedium>Co. Stk</th>";
            }
        }

        if ($companyid == '5') {
            echo "<th class=titlemedium>48hr Stk</th>";
        }

        if ($spdisp == "B" or $spdisp == "X") {
            echo "<th class=titlemedium>Cost Price</th>";
        }

        if ($spdisp == "S" or $spdisp == "X") {
            echo "<th class=titlemedium>Sell Price</th>";
            switch ($companyid) {
                case ($companyid == '5' || $companyid == '11'):
                    if (($Show_rrp == "Y") && ($Hide_rrp !== "Y")) {
                        echo "<th class=titlemedium>RRP</th>";
                    }
                    if (($Show_rrp4 == "Y") && ($Hide_rrp !== "Y")) {
                        echo "<th class=titlemedium>RRP4</th>";
                    }
                    break;
            }
        }

        echo "<th class=titlemedium>Basket</th></tr></thead><tbody>";

        while ($i < $num) {
            mysqli_data_seek($srchresult, $i);
            $row = mysqli_fetch_assoc($srchresult);

            $tr_row_class = ($i % 2 == 0) ? '' : " class='odd'";
            $td_col_class = "class='eulabel'";
            $td_price_col_class = "class='price'";

            if ($row["highlight"] == 'Y') {
                $tr_row_class = " class='highlight'";
                $td_col_class = "class='highlight-eulabel'";
                $td_price_col_class = "class='highlight-price'";
            }

            $productid = $row["product_id"];

            echo "\n<tr $tr_row_class >";
            echo "<td>{$row["stockcode"]}</td>";
            echo "<td>" . substr($row["description"], 0, 80) . "</td>";
            echo "<td>{$row["manufacturer"]}</td>";
            echo "<td $td_col_class>{$row["fuel_efficiency"]}</td>";
            echo "<td $td_col_class>{$row["wet_braking"]}</td>";

            $noise = trim($row["decibels"], "\0 ");
            echo "<td $td_col_class>$noise";
            if ($noise != "") {
                echo "db";
            }
            echo "</td>";
            echo "<td $td_col_class>";
            if (trim($row["url"]) != "") {
                echo "<a href='" . $row["url"] . "' target='_blank'><img src='images/TyreLabelIcon.jpg' width='16' height='16' onMouseOver=\"Tip('Tyre Label')\"></a>";
            } elseif (
                trim($row["fuel_efficiency"]) != "" &&
                trim($row["wet_braking"]) != "" &&
                trim($row["vehicle_class"]) != ""
            ) {
                echo "<a href='http://www.tyreman.co.uk/eulabel.php?id=" . $row["fuel_efficiency"] .
                    "&id2=" . $row["wet_braking"] .
                    "&id3=" . $row["noise_rating"] .
                    "&id4=" . $row["decibels"] .
                    "&id5=" . $row["vehicle_class"] .
                    "&id6=" . $row["stockcode"] . "' target='_blank'><img src='images/TyreLabelIcon.jpg' width='16' height='16' onMouseOver=\"Tip('Tyre Label')\"></a>";
            }
            echo "</td>";

            if ($companyid == '5') {
                echo "<td $td_col_class>";
                if ($row["image_name"] != "") {
                    echo "<a href='images/BAB/" . $row["image_name"] . "' target='_blank'><img src='images/camera-solid.svg' height='16px' width='16px' style='display: block; margin: auto;' onMouseOver=\"Tip('View item')\"></a>";
                }
                echo "</td>";
            }

            echo "<td $td_col_class>";
            if ($row["winter"] == "Y") {
                echo "<span style='VISIBILITY:hidden;display:none'>" . $row["winter"] . "</span>";
                echo "<img src='images/winter.gif' width='19' height='17' onMouseOver=\"Tip('Winter')\">";
            }
            echo "</td>";

            echo "<td $td_col_class>";
            if ($row["extraload"] == "Y") {
                echo "<span style='VISIBILITY:hidden;display:none'>" . $row["extraload"] . "</span>";
                echo "<img src='images/xl.gif' width='19' height='17' onMouseOver=\"Tip('Extra Load')\">";
            }
            echo "</td>";

            echo "<td $td_col_class>";
            if ($row["runflat"] == "Y") {
                echo "<span style='VISIBILITY:hidden;display:none'>" . $row["runflat"] . "</span>";
                echo "<img src='images/runflat.gif' width='19' height='17' onMouseOver=\"Tip('Run Flat')\">";
            }
            echo "</td>";
            if ($row["show_stock_flag"] == "Y") {
                echo "<td style=\"padding:0px 2px; text-align:right;\">" . $row["stocklevel"] . "</td>";
                if ($companyid == '5' or $companyid == '11') {
                    echo "<td style=\"padding:0px 2px; text-align:right;\">" . $row["regionstk"] . "</td>";
                }
                $ttlstock = GetTotalStock($row["stockcode"]);
                if ($companyid != '11') {
                    echo "<td style=\"padding:0px 2px; text-align:right;\">$ttlstock</td>";
                }
                if ($companyid == '5') {
                    echo "<td style=\"padding:0px 2px; text-align:right;\">" . $row["supplier_stock"] . "</td>";
                }
            } elseif ($row["show_stock_flag"] == "B") {
                echo "<td style=\"padding:0px 2px; text-align:right;\">" . getStockBand($row["stocklevel"]) . "</td>";
            }

            if ($_SESSION['selected_branch'] == $_SESSION['default_branch']) {
                $custprice = $row["netprice"];
            } else {
                include 'Reconnect.php';
                $query = "SELECT netprice FROM prices WHERE stockcode = '" . $row["stockcode"] . "' AND customer_id = $cust";
                $srchresult2 = mysqli_query($conn, $query) or die(mysqli_error($conn));
                $num2 = mysqli_num_rows($srchresult2);
                if ($num2 > 0) {
                    $row2 = mysqli_fetch_assoc($srchresult2);
                    $custprice = $row2["netprice"];
                } else {
                    $custprice = 0;
                }
            }

            if ($spdisp == 'B' or $spdisp == 'X') {
                $buyprice = $custprice;
                echo "<td $td_price_col_class>" . number_format($custprice, 2) . "</td>";
            }

            if ($spdisp == 'S' or $spdisp == 'X') {
                $buyprice = $custprice;
                $custprice += $markupval;
                $custprice *= (1 + ($markuppct / 100));
                if ($vatflag == 'Y') {
                    $custprice = ($custprice / 100) * $vat + $custprice;
                }
                $custprice = round($custprice, 2);
                echo "<td $td_price_col_class>" . number_format($custprice, 2) . "</td>";
            }
            switch ($companyid) {
                case 1:
                    break;
                case 2:
                    break;
                case ($companyid == '5' || $companyid == '11'):
                    if ($spdisp == "S" or $spdisp == "X") {
                        if (($Show_rrp == "Y") && ($Hide_rrp !== "Y")) {
                            $rrpval = $row["rrp"];
                            echo "<td style=\"padding:0px 2px; text-align:right;\">" . number_format($rrpval, 2) . "</td>";
                        }
                        if (($Show_rrp4 == "Y") && ($Hide_rrp !== "Y")) {
                            $rrp4val = $row["rrp4"];
                            echo "<td style=\"padding:0px 2px; text-align:right;\">" . number_format($rrp4val, 2) . "</td>";
                        }
                    }
                    break;
            }

            echo "<td style='text-align:center;'>";
            if ($custprice > 0) {
                include 'Reconnect.php';
                $query = "SELECT * FROM customers WHERE Customer_id = '$cust'";
                $enquiry_result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                $num_enq = mysqli_num_rows($enquiry_result);
                $enquiry_only = 'N';
                if ($num_enq > 0) {
                    $enquiry_row = mysqli_fetch_assoc($enquiry_result);
                    $enquiry_only = $enquiry_row["Enquiry_Only"];
                }
                mysqli_data_seek($customer_stop, 0);
                $stop_row = mysqli_fetch_assoc($customer_stop);
                if ($stop_row["On_Stop_Flag"] == 'Y') {
                    echo '<span style="color:red;">On Stop</span>';
                } elseif ($enquiry_only == 'Y') {
                    echo 'n/a';
                } else {
                    echo '<a title=Add&nbsp;to&nbsp;Basket href=addtobasket.php?productid=' .
                        $productid . '&qty=1&price=' . $buyprice . '>Add</a>';
                }
            } else {
                echo 'Call';
            }

            echo "</td></tr>";
            $i++;
        }

        echo "</tbody></table>";
        echo "</td></tr>";
        echo "</table>";
    } else {
        echo "<div id=Message_Div>No Matching Products Found.</div>";
    }

} else {
    echo "<div id=Message_Div>Minimum Browser Version Requirements: IE 11, Edge 14, Firefox 50, Chrome 55, Safari 10, Opera 42, IOS Safari 10.2, Opera Mobile 37, Chrome Android 55</div>";
}
?>
