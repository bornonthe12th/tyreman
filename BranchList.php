<?php
$def_branch = $_SESSION['default_branch'];
$cust       = $_SESSION['customerid'];
$session    = session_id();
$companyid  = $_SESSION['companyid'];
$branch     = $_SESSION['selected_branch'] ?? '';

// Are they allowed to change branch?
$chg_branch = GetCompanySetting('AllowBranchChange');

include 'Reconnect.php';

if ($chg_branch === 'Y') {
	$basketEmpty = true;
	$query = "CALL BranchList();";
	$result = $conn->query($query);

	if ($result && $result->num_rows > 0) {
		echo "<li>Branch</li>";
		echo "<li><select name='branch'";

		if ($companyid == '5') {
			echo " onmouseover=\"Tip('Branch cannot be changed')\"";
			$basketEmpty = false;
		}

		if (!IsBasketEmpty($cust, $session)) {
			echo " onmouseover=\"Tip('You may only change the branch</br>when your basket is empty.')\"";
			$basketEmpty = false;
		}

		echo ">";

		$branches = [];
		while ($row = $result->fetch_assoc()) {
			$branches[] = $row;
		}

		foreach ($branches as $row) {
			$selected = '';
			if ((($def_branch == $row['branch_id'] && $branch == '') || $branch == $row['branch_id'])) {
				$selected = ' selected';
			}

			if ($basketEmpty) {
				echo "<option value='{$row['branch_id']}'{$selected}>{$row['description']}</option>";
			} else {
				// Only show the selected branch when basket is not empty
				if ($selected !== '') {
					echo "<option value='{$row['branch_id']}'{$selected}>{$row['description']}</option>";
				}
			}
		}

		echo "</select></li>\n";
	}

} else {
	// Display current branch name only
	$query = "CALL GetBranchName($def_branch);";
	$result = $conn->query($query);

	if ($result && $row = $result->fetch_assoc()) {
		echo "<li>{$row['description']}</li>";
	}
}
