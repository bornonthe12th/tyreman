<?php

// JavaScript for table sort headings and tooltips
echo "<script type='text/javascript' src='scripts/common.js'></script>";
echo "<script type='text/javascript' src='scripts/css.js'></script>";
echo "<script type='text/javascript' src='scripts/standardista_table_sorting.js'></script>";
echo "<script type='text/javascript' src='/scripts/wz_tooltip.js'></script>";

$companyid = $_SESSION['companyid'] ?? 0;
$cust = $_SESSION['customerid'] ?? 0;
$spdisp = $_GET['spdisp'] ?? '';
$vat = 20.0;

if (!in_array($spdisp, ['B', 'S'], true)) {
    $query = "CALL GetAccountDetails($cust);";
    $srchresult = mysqli_query($conn, $query);

    if ($srchresult && mysqli_num_rows($srchresult) > 0) {
        $row = mysqli_fetch_assoc($srchresult);
        $vatflag       = $row['IncVatFlag'];
        $markupval     = $row['markupval'];
        $markuppct     = $row['markuppc'];
        $DefToSellFlag = $row['DefToSellFlag'];
        $Show_rrp      = $row['Show_rrp'];
        $Show_rrp4     = $row['Show_rrp4'];
        $Hide_rrp      = $row['hide_rrp'] ?? 'n';
        $Account_No    = $row['Account_No'];
    }
    include 'Reconnect.php';
}

// Sanitize inputs
$scode = $_POST['scode'] ?? '';
$sdesc = $_POST['sdesc'] ?? '';
$sman = $_POST['sman'] ?? '';
$size = $_POST['size'] ?? '';
$branch = $_SESSION['selected_branch'] ?? '';
$default_branch = $_SESSION['default_branch'] ?? '';

if ($scode || $sdesc || $sman || $size) {
    if ($branch != $default_branch) {
        $query = "CALL StockSearchBranch('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";
    } else {
        $query = "CALL StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";
    }

    $srchresult = mysqli_query($conn, $query);

    if ($srchresult && mysqli_num_rows($srchresult) > 0) {
        echo "<table id='BlueTable'>";
        echo "<tr><td class='maintitle'>Search Results</td></tr>";
        echo "<tr><td><table class='sortable'><thead><tr>";
        echo "<th class='stcodetitle'>Stock Code</th><th class='titlemedium'>Description</th><th class='titlemedium'>Manufacturer</th>";
        echo "<th class='titlemedium'>Fuel</th><th class='titlemedium'>Wet</th><th class='titlemedium'>Noise</th><th class='titlemedium'>TL</th>";
        echo "<th class='titlemedium'>W</th><th class='titlemedium'>XL</th><th class='titlemedium'>RF</th><th class='titlemedium'>Basket</th>";
        echo "</tr></thead><tbody>";

        while ($row = mysqli_fetch_assoc($srchresult)) {
            echo "<tr>";
            echo "<td>{$row['stockcode']}</td><td>{$row['description']}</td><td>{$row['manufacturer']}</td>";
            echo "<td>{$row['fuel_efficiency']}</td><td>{$row['wet_braking']}</td><td>{$row['decibels']}db</td>";
            echo "<td><img src='images/TyreLabelIcon.jpg' width='16' height='16' alt='TL'></td>";
            echo "<td>{$row['winter']}</td><td>{$row['extraload']}</td><td>{$row['runflat']}</td>";
            echo "<td><a href='addtobasket.php?productid={$row['product_id']}&qty=1&price={$row['netprice']}'>Add</a></td>";
            echo "</tr>";
        }
        echo "</tbody></table></td></tr></table>";
    } else {
        echo "<div id='Message_Div'>No Matching Products Found.</div>";
    }
} else {
    echo "<div id='Message_Div'>Minimum Browser Version Requirements: IE 11, Edge 14, Firefox 50, Chrome 55, Safari 10, Opera 42, IOS Safari 10.2, Opera Mobile 37, Chrome Android 55</div>";
}
