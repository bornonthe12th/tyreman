<?php
//include error class
include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php';


//read list of files
// open this directory 
$myDirectory = opendir("load/");
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .STK files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".STK"){
		$dirArray[] = $entryName;
	}
}
// close directory
closedir($myDirectory);
//	count elements in array
$indexCount	= count($dirArray);
// sort by date
sort($dirArray);

//only process latest file
$index = $indexCount-1;
if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
	$row = 1;
	$filename = "load/" . $dirArray[$index];
	$handle = fopen($filename, "r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    $num = count($data);
	    //do we insert or update?
	    $query = "select count(*) rowcount from LDB2BMAST where Stockcode ='" . $data[0] . "';";
	    $result = mysql_query($query);
	    if (mysql_result($result,0,"rowcount") > 0) {  //update
		    //set up query
			$updstmnt="update LDB2BMast l ";	
		     //first col is stock code
	        $whrstmnt = "where l.Stockcode ='" . $data[0] . "';";
	    	$setstmnt = "set l.desc='" . $data[1] . "',";
	    	$setstmnt = $setstmnt ."l.class='" . $data[2] . "',";
	    	$setstmnt = $setstmnt ."l.make='" . $data[2] . "',";
	    	$setstmnt = $setstmnt ."l.type='" . $data[2] . "',";
	    	$setstmnt = $setstmnt ."l.qty='" . $data[2] . "'";

		    //build query
		    $query = $updstmnt . $setstmnt . $whrstmnt;
		    //update row
			$result=mysql_query($query);
    	} else { //insert
		    //set up query
			$query="insert into LDB2BMast values (";	
		    //per column
		    for ($c=0; $c < $num; $c++) {
		        $query = $query . "'" . $data[$c] . "',";
		    }
		    //remove  final , and close statement
		    $query = rtrim($query,",") . ");";
		    //run query
		    //insert row
			$result=mysql_query($query);
		}
		
		$row++;
	//loop to next row
	}
	fclose($handle);
	//commit changes
	$query = "commit;";
	$result=mysql_query($query);
}
for($index=0; $index < $indexCount; $index++) {
	//delete all files
	$filename = "load/" . $dirArray[$index];
	//unlink($filename);
}


//disconnect
include 'LDclosedb.php';
?> 