<?php
// Start session safely
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Include dependencies
include 'tmanerror.inc';
include 'B2Bconnect.php';
include 'B2BHeader.inc';

$cust = $_SESSION['customerid'] ?? null;
$companyid = $_SESSION['companyid'] ?? null;
$session = session_id();

if (!$cust || !$companyid || !$session) {
    terror('Missing required session data', 'B2BAccountDetails.php');
    exit();
}
?>

<BODY CLASS="slink" LEFTMARGIN=0 TOPMARGIN=0 STYLE="font-family:Verdana; font-size:10">

<?php
include 'B2BMenu.php';
include 'B2BFunctions.php';
?>

<div id="content">
    <div id="sidebar">
        <img src="<?= GetResource('titlebarhdrimg') ?>" alt="Header Image">
        <ul>
            <li><h2 align="center">Account Details</h2></li>
        </ul>
        <?php include 'B2BSbarFtr.inc'; ?>
    </div>

    <div id="mainbody">
        <?php
        include 'Reconnect.php';

        $query = "CALL GetAccountDetails($cust)";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0):
            $row = $result->fetch_assoc();
            ?>
            <br>
            <table id="AccountTable" align="center">
                <tr><td class="maintitle">Account Details</td></tr>
                <tr><td>
                        <form name="accform" method="POST" action="UpdateAccount.php" onsubmit="return ValidateForm();">
                            <table>
                                <tr>
                                    <th class="titlemedium">
                                        <?= ucfirst($row['title'] ?? '') . '&nbsp;' . ucfirst($row['first_name'] ?? '') . '&nbsp;' . ucfirst($row['surname'] ?? '') ?>
                                    </th>
                                </tr>

                                <tr>
                                    <td>Mark Up Percent</td>
                                    <td><input type="text" size="6" maxlength="6" name="markuppc" value="<?= htmlspecialchars($row['markuppc'] ?? '') ?>"></td>
                                </tr>

                                <tr>
                                    <td>Mark Up Value</td>
                                    <td><input type="text" size="8" maxlength="8" name="markupval" value="<?= htmlspecialchars($row['markupval'] ?? '') ?>"></td>
                                </tr>

                                <tr>
                                    <td>Include VAT</td>
                                    <td>
                                        <input type="checkbox" id="chkvat" name="incVATFlag" value="Y" <?= ($row['incVATFlag'] ?? '') === 'Y' ? 'checked' : '' ?>>
                                        <label for="chkvat"><span></span></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Default To Sell Out Price</td>
                                    <td>
                                        <input type="checkbox" id="chksell" name="DefToSellFlag" value="Y" <?= ($row['DefToSellFlag'] ?? '') === 'Y' ? 'checked' : '' ?>>
                                        <label for="chksell"><span></span></label>
                                    </td>
                                </tr>

                                <?php
                                if (in_array($companyid, [5, 11]) && ($row['hide_rrp'] ?? '') !== 'Y'):
                                    ?>
                                    <tr>
                                        <td>Show RRP</td>
                                        <td>
                                            <input type="checkbox" id="chkrrp" name="Show_rrp" value="Y" <?= ($row['Show_rrp'] ?? '') === 'Y' ? 'checked' : '' ?>>
                                            <label for="chkrrp"><span></span></label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Show RRP4</td>
                                        <td>
                                            <input type="checkbox" id="chkrrp4" name="Show_rrp4" value="Y" <?= ($row['Show_rrp4'] ?? '') === 'Y' ? 'checked' : '' ?>>
                                            <label for="chkrrp4"><span></span></label>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td></td>
                                    <td align="center">
                                        <input type="submit" name="butsubmit" value="Update">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td></tr>
            </table>
        <?php
        else:
            echo "<div id='Message_Div'>No Account Details Found.</div>";
        endif;
        ?>
    </div><!-- /mainbody -->
</div><!-- /content -->

<script type="text/javascript">
    function ValidateForm() {
        return true;
    }
</script>

</BODY>

<?php include 'B2Bclosedb.php'; ?>
