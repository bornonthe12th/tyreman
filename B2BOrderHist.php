<?php
include 'tmanerror.inc';
include 'B2Bconnect.php';
include 'B2BHeader.inc';
include 'Reconnect.php';

$cust = $_SESSION['customerid'] ?? 0;
if (!$cust) {
    die("Customer not logged in.");
}

// Handle pagination and per-page options
$perPage = isset($_GET['perpage']) ? max((int)$_GET['perpage'], 1) : 100;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $perPage;

// Handle optional date filter
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$dateFilter = '';

if ($startDate && $endDate) {
    $startDateSafe = $conn->real_escape_string($startDate);
    $endDateSafe = $conn->real_escape_string($endDate);
    $dateFilter = " AND o.order_date BETWEEN '$startDateSafe' AND '$endDateSafe'";
}

// Count total distinct orders (with filter if any)
$countQuery = "SELECT COUNT(DISTINCT o.order_id) AS total 
               FROM orders o 
               WHERE o.customer_id = $cust $dateFilter";
$countResult = $conn->query($countQuery);
$total = $countResult ? ($countResult->fetch_assoc()['total'] ?? 0) : 0;
$totalPages = ceil($total / $perPage);

// Fetch orders with lines (with date filter)
$query = "
    SELECT 
        o.order_id,
        o.order_ref,
        DATE_FORMAT(o.order_date, '%d-%m-%Y') AS order_date,
        s.stockcode,
        ol.qty,
        ol.price
    FROM orders o
    JOIN order_lines ol ON o.order_id = ol.order_id
    LEFT JOIN stock s ON s.product_id = ol.product_id
    WHERE o.customer_id = $cust $dateFilter
    ORDER BY o.order_date DESC, o.order_id DESC
    LIMIT $perPage OFFSET $offset
";

$result = $conn->query($query);
?>

<BODY CLASS="slink" STYLE="font-family:Verdana; font-size:10">
<?php include 'B2BMenu.php'; ?>
<?php include 'B2BFunctions.php'; ?>

<div id="content">
    <div id="sidebar">
        <img src="<?= GetResource('titlebarhdrimg') ?>">
        <ul><li><h2 align="center">Order History</h2></li></ul>
        <?php include 'B2BSbarFtr.inc'; ?>
    </div>

    <div id="mainbody">
        <!-- Date Filter + PerPage Form -->
        <form method="GET" action="">
            <div style="margin-bottom: 20px;">
                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                <label>End Date:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">

                <label>Results per page:</label>
                <select name="perpage">
                    <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                    <option value="200" <?= $perPage == 200 ? 'selected' : '' ?>>200</option>
                </select>

                <button type="submit">Filter</button>
                <a href="B2BOrderHist.php" style="margin-left: 10px;">Clear</a>
            </div>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <table id="HistoryTable" border="0" cellspacing="0" cellpadding="4" style="width: 90%;">
                <tr><td class="maintitle">Order History</td></tr>
                <tr><td>
                        <table width="90%">
                            <?php
                            $current_order_id = null;
                            $j = 0;

                            while ($row = $result->fetch_assoc()):
                                $order_id   = $row['order_id'] ?? '';
                                $order_date = $row['order_date'] ?? '';
                                $order_ref  = $row['order_ref'] ?? '';
                                $stockcode  = $row['stockcode'] ?? 'N/A';
                                $qty        = $row['qty'] ?? '0';
                                $price      = $row['price'] ?? '0.00';

                                $tdclass = ($j % 2 === 0) ? 'even' : 'odd';

                                if ($order_id !== $current_order_id):
                                    if (!is_null($current_order_id)) {
                                        echo "<tr><th class='orderheader' colspan='6'>&nbsp;</th></tr>";
                                    }

                                    echo "<tr>
                                        <th width='120' class='titlemedium'>Order ID</th>
                                        <th width='120' class='titlemedium'>Order Date</th>
                                        <th width='150' class='titlemedium'>Order Ref</th>
                                        <th width='150' class='titlemedium'>Stock Code</th>
                                        <th width='100' class='titlemedium'>Quantity</th>
                                        <th width='120' class='titlemedium'>Price</th>
                                      </tr>";

                                    echo "<tr>
                                        <td class='$tdclass' align='center'>" . str_pad($order_id, 8, "0", STR_PAD_LEFT) . "</td>
                                        <td class='$tdclass' align='center' nowrap>&nbsp;$order_date&nbsp;</td>
                                        <td class='$tdclass'>" . htmlspecialchars($order_ref) . "</td>
                                        <td class='$tdclass'>" . htmlspecialchars($stockcode) . "</td>
                                        <td class='$tdclass' align='right'>$qty</td>
                                        <td class='$tdclass' align='right'>&nbsp;" . number_format((float)$price, 2) . "&nbsp;</td>
                                      </tr>";

                                    $j = 1;
                                    $current_order_id = $order_id;
                                else:
                                    echo "<tr>
                                        <td class='$tdclass'></td>
                                        <td class='$tdclass'></td>
                                        <td class='$tdclass'></td>
                                        <td class='$tdclass'>" . htmlspecialchars($stockcode) . "</td>
                                        <td class='$tdclass' align='right'>$qty</td>
                                        <td class='$tdclass' align='right'>&nbsp;" . number_format((float)$price, 2) . "&nbsp;</td>
                                      </tr>";
                                    $j++;
                                endif;
                            endwhile;
                            ?>
                        </table>
                    </td></tr>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php
                $range = 5;
                $start = max(1, $page - $range);
                $end = min($totalPages, $page + $range);

                $queryParams = "&start_date=" . urlencode($startDate) . "&end_date=" . urlencode($endDate) . "&perpage=$perPage";

                if ($page > 1) {
                    echo "<a href='?page=1$queryParams'>First</a>";
                    echo "<a href='?page=" . ($page - 1) . "$queryParams'>&laquo; Prev</a>";
                }

                for ($i = $start; $i <= $end; $i++) {
                    $class = ($i == $page) ? 'active' : '';
                    echo "<a href='?page=$i$queryParams' class='$class'>$i</a>";
                }

                if ($page < $totalPages) {
                    echo "<a href='?page=" . ($page + 1) . "$queryParams'>Next &raquo;</a>";
                    echo "<a href='?page=$totalPages$queryParams'>Last</a>";
                }
                ?>
            </div>

        <?php else: ?>
            <div id="Message_Div">No Orders Found.</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'B2Bclosedb.php'; ?>
</BODY>

<!-- Styles -->
<style>
    .maintitle {
        font-size: 16px;
        font-weight: bold;
        background-color: #eee;
        padding: 8px;
    }
    .titlemedium {
        font-weight: bold;
        font-size: 12px;
        background-color: #ddd;
    }
    .orderheader {
        background-color: #ccc;
        height: 10px;
    }
    .odd {
        background-color: #f9f9f9;
    }
    .even {
        background-color: #ffffff;
    }
    .pagination {
        margin: 20px 0;
        text-align: center;
    }
    .pagination a {
        color: #000;
        background: #FFA033;
        padding: 6px 10px;
        margin: 0 2px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 13px;
        border: 1px solid #cc6600;
    }
    .pagination a.active {
        background: #CC3300;
        color: #fff;
        font-weight: bold;
    }
    .pagination a:hover {
        background: #FF9900;
    }
</style>