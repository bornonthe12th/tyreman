<?php
// This is an example opendb.php
$conn = mysql_connect($dbhost, $dbuser, $dbpass,'false',65536) or die('Error connecting to mysql');

//use get value if set
if (isset($company_id)) {
		//select schema based on company_id
		$query = "select dbschema from b2busers.companies where company_id=$company_id;";
		$result=mysql_query($query);
		if (mysql_num_rows($result) > 0) {
			$company = mysql_result($result,0,"dbschema");
			$dbname = mysql_result($result,0,"dbschema");
//echo $company;
//echo $company_id;
		} else {
			exit("Invalid Company");
		}
} else {
		exit("Missing company parameter\n");
}
mysql_select_db($dbname);
?>
