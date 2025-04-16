<?php


	foreach($_SESSION as $key => $value)
	if(stristr($key,"savoy_db_")) // we want data from savoy db (database)
	{
		$key = str_replace("savoy_db_","",$key);

		if(!stristr($key,"savoy")) // We don't want but not markups from savoy to savoy's customer
			echo "<tr><td>$key</td><td>$value</td></tr>";

	}

	echo "<tr><td colspan=2>If you would like to update your GM's please<br /> telephone Savoy Head Office or ask your Savoy rep <br />at the next visit.</td></tr>";




?>