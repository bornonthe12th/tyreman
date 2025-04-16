<?php
$File = 'TYR00000284.orh';
$output = shell_exec("/usr/local/bin/TestOrderComplete.ksh $File 2>&1");
//echo "<pre>$output</pre>";

    if (trim($output) == 'FAIL') {
	echo "<meta http-equiv=\"refresh\" content=\"0;URL=B2BOrderIncomplete.php\">";
	} else {
 	echo "<meta http-equiv=\"refresh\" content=\"0;URL=B2BProdSearch.php\">";
	}
?>

