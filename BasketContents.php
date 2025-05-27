<?php
$companyid = $_SESSION['companyid'];
$cust = $_SESSION['customerid'] ?? 0; // Add this if $cust is undefined
$session = session_id();


$query = "CALL ShowBasket($cust, '$session')";
$srchresult = $conn->query($query);


$total = 0;

if ($srchresult && $srchresult->num_rows > 0) {
    echo "<table id='BasketTable' align='center'>";
    echo "<tr><td class='maintitle'>Basket Contents</td></tr> ";
    echo "<tr><td>";
    echo "<table align='center'>";
    echo "<tr>
        <th width='190' class='titlemedium'>Stock Code</th>
        <th class='titlemedium'>Description</th>
        <th width='100' class='titlemedium'>Quantity Available</th>
        <th width='100' class='titlemedium'>Quantity Required</th>
        <th width='100' class='titlemedium'>Cost Price</th>
        <th width='100' class='titlemedium'>Line Total</th>
    </tr>";

    $i = 0;
    while ($row = $srchresult->fetch_assoc()) {
        $tdclass = ($i % 2 == 0) ? 'even' : 'odd';

        $stockcode = $row['stockcode'];
        $description = $row['description'];
        $stocklevel = $row['stocklevel'];
        $qty = $row['qty'];
        $price = $row['price'];
        $linettl = $row['linettl'];
        $productid = $row['product_id'];

        echo "<tr><td class='$tdclass'>$stockcode</td><td class='$tdclass'>$description</td><td align='right' class='$tdclass'>$stocklevel&nbsp;</td>";

        switch ($companyid) {
            case 1:
            case 3:
            case 15:
            case 16:
                echo "<td align='right' class='$tdclass'><select name='qty$i' style='width: 50px'>";
                for ($work = -1; $work <= $stocklevel; $work++) {
                    $selected = ($work == $qty) ? "SELECTED" : "";
                    echo "<option value=\"$work\" $selected>$work</option>";
                }
                echo "</select></td>";
                break;

            default:
                echo "<td align='center' class='$tdclass'><input type='text' size='5' maxlength='6' name='qty$i' value='$qty'></td>";
        }

        echo "<td align='right' class='$tdclass'>" . number_format($price, 2) . "&nbsp;</td>";
        echo "<td align='right' class='$tdclass'>" . number_format($linettl, 2) . "&nbsp;</td>";
        echo "<td align='right' class='$tdclass'><input type='hidden' name='prodid$i' value='$productid'></td></tr>";

        $total += $qty * $price;
        $i++;
    }

    echo "<tr><td colspan='4'></td><td align='right'>Total&nbsp</td><td align='right'><b>" . number_format($total, 2, '.', ',') . "&nbsp;</b></td></tr>";
    echo "<input type='hidden' name='linecount' value='$i'>";
    echo "</table></td></tr></table>";
    echo "<br><div id='Message_Div' align='center'><table id='BlueTable'><tr><td class='titlemedium'>To delete an item from your basket change the quantity to 0</td></tr>";
    echo "<tr><td class='titlemedium'>and press the 'Update Basket' button.</td></tr></table></div>";
} else {
    echo "<br><div id='Message_Div' align='center'><table id='BlueTable'>";
    echo "<tr><td class='titlemedium'>Your basket is currently empty..</td></tr>";
    echo "</table></div>";
}
?>
