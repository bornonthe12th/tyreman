<?php
include 'tmanerror.inc';
include 'B2Bconnect.php';
include 'B2BHeader.inc';

$cust = $_SESSION['customerid'] ?? null;
$order_id = $_SESSION['orderid'] ?? null;
$Company_desc = $_SESSION['description'] ?? '';
$companyid = $_SESSION['companyid'] ?? null;
$session = session_id();

$ord = $_GET['ord'] ?? '';
if (!$cust || !$order_id) {
    terror('Session expired or invalid', 'B2BOrderConfirm.php');
}
?>

<BODY>
<?php
include 'B2BMenu.php';
include 'B2BFunctions.php';
?>

<div id="content">
    <div id="sidebar">
        <?php
        echo "<img src='" . htmlspecialchars(GetResource('titlebarhdrimg')) . "'>";
        ?>
        <ul>
            <li><h2 align="center">Order Confirmation</h2></li>
            <li><input type="button" value="Start New Order" onclick="window.location.href='B2BProdSearch.php'"></li>
            <li><br></li>
            <li><input type="button" value="Logout" onclick="window.location.href='logout.php'"></li>
            <li><br></li>
        </ul>
        <?php include 'B2BSbarFtr.inc'; ?>
    </div><!-- /sidebar -->

    <div id="mainbody">

        <?php
        include 'Reconnect.php';

        // Fetch the order summary
        $query = "CALL ShowSummary('$session', $order_id);";
        $result = $conn->query($query);
        if (!$result) {
            terror('Failed to fetch order summary: ' . $conn->error, 'B2BOrderConfirm.php');
        }

        $neg_stk = "N";

        if ($result->num_rows > 0) {
            $i = 0;
            $OrdTtl = 0;
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            echo '<div style="font-size:12px;margin-top:30px;">';
            include 'Reconnect.php';

            // Fetch delivery address
            $sql = "SELECT * FROM customers LEFT JOIN addresses ON customers.delivery_add_id = addresses.Address_id WHERE customers.Customer_id = '$cust'";
            $address_result = $conn->query($sql);
            if ($address_row = $address_result->fetch_assoc()) {
                echo '<div style="margin-left:50px;float:left;width:70px;font-weight:bold;">To:</div>';
                echo '<div style="float:left;width:150px;">' . htmlspecialchars($address_row['Addressee']) . '<br>';
                for ($k = 1; $k <= 5; $k++) {
                    $field = "Address_line$k";
                    if (!empty($address_row[$field])) {
                        echo htmlspecialchars($address_row[$field]) . '<br>';
                    }
                }
                echo htmlspecialchars($address_row['PostCode']) . '<br>';
                echo '</div>';
            }

            // Fetch supplying depot
            $selected_branch = (int)($_SESSION['selected_branch'] ?? 0);
            $branch_result = $conn->query("SELECT Description FROM branches WHERE branch_id = $selected_branch");
            if ($branch_row = $branch_result->fetch_assoc()) {
                echo '<div style="margin-left:35px;float:left;width:150px;font-weight:bold;">Supplying Depot:</div>';
                echo '<div style="float:left;">' . htmlspecialchars($Company_desc) . '<br>' . htmlspecialchars($branch_row['Description']) . '</div>';
            }

            echo '<div style="clear:both;"></div>';
            echo '</div>';

            // Order details
            echo "<br><table id='BlueTable' align='center'>";
            echo "<tr><td class='maintitle'>Order Complete</td></tr><tr><td>";
            echo "<table align='center'>";
            echo "<tr><th width='150' class='titlemedium'>Stock Code</th><th width='300' class='titlemedium'>Description</th><th width='75' class='titlemedium'>Quantity</th><th width='75' class='titlemedium'>Unit Price</th></tr>";

            foreach ($rows as $row) {
                $tdclass = ($i % 2 === 0) ? 'even' : 'odd';
                $stocklevel = $row['stocklevel'] ?? 0;
                if ($stocklevel < 0) {
                    $neg_stk = "Y";
                }
                echo "<tr>";
                echo "<td class='$tdclass'>" . htmlspecialchars($row['stockcode']) . "&nbsp;</td>";
                echo "<td class='$tdclass'>" . htmlspecialchars($row['description']) . "&nbsp;</td>";
                echo "<td align='right' class='$tdclass'>" . htmlspecialchars($row['qty']) . "&nbsp;</td>";
                echo "<td align='right' class='$tdclass'>" . number_format((float)$row['price'], 2) . "&nbsp;</td>";
                echo "</tr>";

                $ord_ref = str_replace(",", " ", $row['order_ref'] ?? '');
                $OrdTtl += ($row['price'] ?? 0) * ($row['qty'] ?? 0);

                $i++;
            }

            echo "<tr><td colspan='2'></td><td align='right'>Total&nbsp;</td><td align='right'>" . number_format($OrdTtl, 2, '.', ',') . "&nbsp;</td></tr>";
            echo "</table>";
            echo "</td></tr>";
            echo "</table>";

            // Confirmation message
            echo "<br><div id='Message_Div' align='center'><table id='BlueTable'>";
            if ($companyid == 1 && $neg_stk == "Y") {
                echo "<tr bgcolor='red'><td>INSUFFICIENT STOCK AVAILABLE. PLEASE CALL YOUR DEPOT.</td></tr>";
            } else {
                echo "<tr><td class='titlemedium'>Thank you for placing your order with " . htmlspecialchars($Company_desc) . ".</td></tr>";
                echo "<tr><td class='titlemedium'>Please print this page for your reference.</td></tr>";
                echo "<tr><td class='titlemedium'>Our reference: " . str_pad($ord, 8, "0", STR_PAD_LEFT);
                if (!empty($ord_ref)) {
                    echo "<br>Your Reference: " . htmlspecialchars($ord_ref);
                }
                echo "</td></tr>";
            }
            echo "</table></div>";
        } else {
            echo "<div id='Message_Div'>No Order Details Found.</div>";
        }
        ?>

    </div><!-- /mainbody -->
</div><!-- /content -->

<?php
include 'B2Bclosedb.php';
?>

<script type="text/javascript">
    function ValidateForm() {
        return true;
    }
</script>
</BODY>
