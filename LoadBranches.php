#!/usr/bin/php
<?php

if(isset($_GET['company']))                     // Is the script is being run through a browser
  $company_id = $_GET['company'];
else
  if(isset($_SERVER["argv"][1]))               // If the script is being run direct from Linux server
    $company_id = $_SERVER["argv"][1];


//include error class
include 'tmanerror.inc';
include 'LDconfig.php';
include 'LDopendb.php';

$baddress = "";
$branch="";


//read list of files

$dirname =  "load/".$company."/";
$myDirectory = opendir($dirname) OR die("Can't open $dirname");
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .BRN files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".BRN"){
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
	$filename = $dirname . $dirArray[$index];
	echo $filename;
	$handle = fopen($filename, "r");
	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2)) {
	    $num = count($data);
	    
	    //do we insert or update?
	    $query = "select branch_id,address_id from branches where branch_code ='" . $data[1] . "' limit 1;";
	    //echo $query . "<br />";
	    $result = mysql_query($query);
	    if (mysql_numrows($result) > 0) {  //update
	    	//set up query
		    $branch = mysql_result($result,0,"branch_id");
		    $baddress = mysql_result($result,0,"address_id");
			$updstmnt="update branches b ";	
	        $whrstmnt = "where b.branch_id ='" . $branch . "';";
	    	$setstmnt = "set b.description='" . $data[2] . "',";
	    	//echo $data[7] . "</br>";
	    	if ($data[7] == 'Y'){
		    	$status = 'A';
	    	} else {
		    	$status = 'D';
	    	}
	    	$setstmnt = $setstmnt ."b.status='" . $status . "',";
	    	$setstmnt = $setstmnt ."b.branch_code='" . $data[1] . "'";
		    //build query
		    $query = $updstmnt . $setstmnt . $whrstmnt;
//echo $query . "<br />";
		    //update row
			$result=mysql_query($query);
    	} else { //insert
		    //set up query
			$query="insert into branches (branch_code,description,status) values (";	
			$query = $query . "'" . $data[1] . "',"; 
		    $query = $query . "'" . $data[2] . "','";
		    if ($data[7] == 'Y'){
		    	$status = 'A';
	    	} else {
		    	$status = 'D';
	    	}
		    $query = $query . $status ."');"; 
		    //run query to insert row
		    $result=mysql_query($query);
		}
		
		//does address exist?
		if ($baddress != ""){
			$query = "select count(*) rowcount from addresses where address_id ='" . $baddress . "';";
//echo $query . "<br />";
	    	$selresult = mysql_query($query);
	    	if (mysql_numrows($selresult) > 0) {  //update
    			//yes? update details
    			$updstmnt="update addresses a ";	
			    $whrstmnt = "where a.address_id ='" . $baddress . "';";
			    $setstmnt = "set a.address_line1='" . $data[3] . "',";
			    $setstmnt = $setstmnt ."a.address_line2='" . $data[4] . "',";
			    $setstmnt = $setstmnt ."a.address_line3='" . $data[5] . "',";
			    $setstmnt = $setstmnt ."a.postcode='" . $data[6] . "',";
			    $setstmnt = $setstmnt ."a.addressee='" . $data[2] . "'";
			    //build query
				$query = $updstmnt . $setstmnt . $whrstmnt;
				//echo $query . "</br>";
				//update row
				$result=mysql_query($query);	
	    	}
    	}
    	if (($baddress == "") OR (mysql_numrows($selresult)== 0)) {
			//no insert it
			//insert del add
			$query="insert into addresses (customer_id,address_line1,address_line2,address_line3,postcode,addressee) values (";	
			$query = $query . "null,"; 
			$query = $query . "'" . $data[3] . "',"; 
			$query = $query . "'" . $data[4] . "',"; 
			$query = $query . "'" . $data[5] . "',"; 
			$query = $query . "'" . $data[6] . "',";
			$query = $query . "'" . $data[2] . "');"; 			 

//echo $query . "<br />";
			//run query to insert customer rec
			$result=mysql_query($query);
					
			//find newly added address id
			$baddress = mysql_insert_id();
			// then update address_id on branches table
			$updstmnt="update branches b ";	
	        $whrstmnt = "where b.branch_id ='" . $branch . "';";
	    	$setstmnt = "set b.address_id='" . $baddress . "'";
		    //build query
		    $query = $updstmnt . $setstmnt . $whrstmnt;
		    //echo $query . "</br>";
		    //update row
			$result=mysql_query($query);
//echo $query . "<br />";		
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
	if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		//delete all files
		$filename = $dirname . $dirArray[$index];
		//unlink($filename);
	}
}


//disconnect
include 'LDclosedb.php';
?> 
