#!/usr/bin/php
<?php

$debug=FALSE;   // Change to TRUE in order to display all $query 's


if(isset($_GET['company']))                     // Is the script is being run through a browser
  $company_id = $_GET['company'];
else
  if(isset($_SERVER["argv"][1]))               // If the script is being run direct from Linux server
    $company_id = $_SERVER["argv"][1];


//include error class
include 'tmanerror.php';
include 'LDconfig.php';
include 'LDopendb.php';
$cust="";
$del_add="";



//read list of files
// open this directory 
//use get value if set



$dirname =  "load/".$company."/";
$myDirectory = opendir($dirname);
// get each entry
while($entryName = readdir($myDirectory)) {
	//only include .CLG files
	if (strtoupper(substr($entryName,strlen($entryName)-4))== ".CLG"){
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
	$handle = fopen($filename, "r");

	while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) and (strlen($data[0])>2))
        {
              foreach($data as $key => $value)
                            //echo "$key - $value <br />";
        
        
	    //get default branch
		$query = "select branch_id from branches where branch_code = '".$data[20]."';";
if($debug) echo $query . "<br />";
		$result = mysql_query($query);
		$num=mysql_num_rows($result);
		if ($num > 0) {
			$branch = mysql_result($result,0,"branch_id");
		} else {
			$branch = "";
		}
		
		//echo 'Branch - '.$branch.'<br />';
		
		$num = count($data);

	    //find customer_id
	    $query = "select customer_id from b2busers.users where company_id =" .$company_id. " and Cust_Admin ='" . $data[1] . "' limit 1;";
if($debug) echo $query . "<br />";
	    $result = mysql_query($query);
	    $num=mysql_num_rows($result);
	    $cust = "";
	    if ($num > 0) {
	    	$cust=mysql_result($result,0,"customer_id");
    	} else {
    		$cust = "";
		}
		//echo 'cust = '.$cust.'<br />';
    	//find delivery address_id
	    $query = "select delivery_add_id from customers where customer_id ='" . $cust . "' limit 1;";
if($debug) echo $query . "<br />";
	    $result = mysql_query($query);
	    $num=mysql_numrows($result);
	    if ($num > 0) {
	    	$del_add=mysql_result($result,0,"delivery_add_id");
    	} else {
	    	$del_add = "";	
    	}
    	
    	//if we have a cust update it else insert it
    	if ($cust){ // update
    		//echo "updating cust-" . $cust ."</br>";
    		//do we have a delivery address? if not insert one else update it
			if ($del_add) {//update del address
				$updstmnt="update addresses a ";	
		    	$whrstmnt = "where a.address_id ='" . $del_add . "';";
		    	$setstmnt = "set a.address_line1='" . $data[12] . "',";
		    	$setstmnt = $setstmnt ."a.address_line2='" . $data[13] . "',";
		    	$setstmnt = $setstmnt ."a.address_line3='" . $data[14] . "',";
		    	$setstmnt = $setstmnt ."a.address_line4='" . $data[15] . "',";
		    	$setstmnt = $setstmnt ."a.address_line5='" . $data[16] . "',";
		    	$setstmnt = $setstmnt ."a.postcode='" . $data[17] . "',";
		    	$setstmnt = $setstmnt ."a.addressee='" . $data[11] . "'";
		    	//build query
				$query = $updstmnt . $setstmnt . $whrstmnt;
if($debug) echo $query . "<br />";
				//update row
				$result=mysql_query($query);
			} else {//insert del address
				//set up query
				$query="insert into addresses (customer_id,address_line1,address_line2,address_line3,address_line4,address_line5,postcode,addressee) values (";	
				$query = $query . "'" . $cust . "',"; 
				$query = $query . "'" . $data[12] . "',"; 
				$query = $query . "'" . $data[13] . "',"; 
				$query = $query . "'" . $data[14] . "',"; 
				$query = $query . "'" . $data[15] . "',"; 
				$query = $query . "'" . $data[16] . "',";
				$query = $query . "'" . $data[16] . "',";  
				$query = $query . "'" . $data[11] . "');"; 			 
				//run query to insert customer rec
if($debug) echo $query . "<br />";
				$result=mysql_query($query);
				
				//find newly added address id
				$del_add = mysql_insert_id();
				
			}
	    	//update users table
    	    $updstmnt = "update b2busers.users u ";
    	    $whrstmnt = "where company_id =" . $company_id . " and Cust_Admin ='" . $data[1] . "';";
    	    $setstmnt = "set u.username='" . $data[2] . "',";
		    $setstmnt = $setstmnt ."u.password='" . $data[3] . "', ";
		    $setstmnt = $setstmnt ."u.status='";
		    if ($data[18] == 'Y') {
			    $setstmnt = $setstmnt . "D";
	    	} else {
		    	$setstmnt = $setstmnt . "A";
	    	}
			$setstmnt = $setstmnt . "' ";
		    //build query
			$query = $updstmnt . $setstmnt . $whrstmnt;
if($debug) echo $query . "<br />";
			//update row
			$result=mysql_query($query);
		    
    		//update customer
			$updstmnt="update customers c ";	
		    $whrstmnt = "where c.customer_id ='" . $cust . "';";
		    $setstmnt = "set c.title='" . $data[5] . "',";
		    $setstmnt = $setstmnt ."c.Account_no='" . rtrim($data[4]) . "',";
		    $setstmnt = $setstmnt ."c.first_name='" . $data[6] . "',";
		    $setstmnt = $setstmnt ."c.surname='" . $data[7] . "',";
		    $setstmnt = $setstmnt ."c.phone='" . $data[8] . "',";
		    $setstmnt = $setstmnt ."c.mobile='" . $data[9] . "',";
		    $setstmnt = $setstmnt ."c.delivery_add_id='" . $del_add . "',";
		    $setstmnt = $setstmnt ."c.fax='" . $data[10] . "', ";
		    $setstmnt = $setstmnt ."c.on_stop_flag='" . $data[19] . "', ";
		    $setstmnt = $setstmnt ."c.credit_limit='" . $data[21] . "', ";
		    $setstmnt = $setstmnt ."c.trade_limit='" . $data[22] . "', ";
		    $setstmnt = $setstmnt ."c.account_balance='" . $data[23] . "', ";
		    $setstmnt = $setstmnt ."c.enquiry_only='" . $data[24] . "', ";
		    $setstmnt = $setstmnt ."c.hide_rrp='" . $data[25] . "', ";
		    $setstmnt = $setstmnt ."c.default_branch_id=";
		    if ($branch){ 
			    $setstmnt = $setstmnt ."'".$branch . "' ";
			} else {
				$setstmnt = $setstmnt . "NULL " ;
			}
		    
			//build query
			$query = $updstmnt . $setstmnt . $whrstmnt;
if($debug) echo $query . "<br />";
			//update row
			$result=mysql_query($query);
			
    	} else {   //insert cust/user/deladd
			
			//we may have customer record already 
			//select from cust by accc_no get cust
			$query = "select customer_id from customers c where account_no ='".$data[4]."' limit 1;";
if($debug) echo $query . "<br />";
			$result=mysql_query($query);
			$num=mysql_numrows($result);
	    	if ($num > 0) {
	    		$cust=mysql_result($result,0,"customer_id");
    		} else {
	    		$cust = "";	
    		}
    		
    		//sort del address
    		
			//if customer record exists then update customer rec
			if ($cust) { 
				//try again to find delivery address_id
			    if (!$del_add) {
					$query = "select delivery_add_id from customers where customer_id ='" . $cust . "' limit 1;";
if($debug) echo $query . "<br />";
					$result = mysql_query($query);
				    $num=mysql_numrows($result);
				    if ($num > 0) {
				    	$del_add=mysql_result($result,0,"delivery_add_id");
			    	} else {
				    	$del_add = "";	
			    	}
				}
				if ($del_add){
					//update del add
					$updstmnt="update addresses a ";	
			    	$whrstmnt = "where a.address_id ='" . $del_add . "';";
			    	$setstmnt = "set a.address_line1='" . $data[12] . "',";
			    	$setstmnt = $setstmnt ."a.address_line2='" . $data[13] . "',";
			    	$setstmnt = $setstmnt ."a.address_line3='" . $data[14] . "',";
			    	$setstmnt = $setstmnt ."a.address_line4='" . $data[15] . "',";
			    	$setstmnt = $setstmnt ."a.address_line5='" . $data[16] . "',";
			    	$setstmnt = $setstmnt ."a.postcode='" . $data[17] . "',";
			    	$setstmnt = $setstmnt ."a.addressee='" . $data[11] . "'";
			    	//build query
					$query = $updstmnt . $setstmnt . $whrstmnt;
if($debug) echo $query . "<br />";
					//update row
					$result=mysql_query($query);
				} else {
					//insert del add
					$query="insert into addresses (customer_id,address_line1,address_line2,address_line3,address_line4,address_line5,postcode,addressee) values (";	
					$query = $query . "null,"; 
					$query = $query . "'" . $data[12] . "',"; 
					$query = $query . "'" . $data[13] . "',"; 
					$query = $query . "'" . $data[14] . "',"; 
					$query = $query . "'" . $data[15] . "',"; 
					$query = $query . "'" . $data[16] . "',";
					$query = $query . "'" . $data[16] . "',";  
					$query = $query . "'" . $data[11] . "');"; 			 
if($debug) echo $query . "<br />";
					//run query to insert customer rec
					$result=mysql_query($query);
					
					//find newly added address id
					$del_add = mysql_insert_id();
				}
				
				//update customer details
				$updstmnt="update customers c ";	
			    $whrstmnt = "where c.customer_id ='" . $cust . "';";
			    $setstmnt = "set c.title='" . $data[5] . "',";
			    $setstmnt = $setstmnt ."c.Account_no='" . rtrim($data[4]) . "',";
			    $setstmnt = $setstmnt ."c.first_name='" . $data[6] . "',";
			    $setstmnt = $setstmnt ."c.surname='" . $data[7] . "',";
			    $setstmnt = $setstmnt ."c.phone='" . $data[8] . "',";
			    $setstmnt = $setstmnt ."c.mobile='" . $data[9] . "',";
			    $setstmnt = $setstmnt ."c.delivery_add_id='" . $del_add . "',";
			    $setstmnt = $setstmnt ."c.fax='" . $data[10] . "', ";
			    $setstmnt = $setstmnt ."c.on_stop_flag='" . $data[19] . "' ";
			    $setstmnt = $setstmnt ."c.credit_limit='" . $data[21] . "' ";
			    $setstmnt = $setstmnt ."c.trade_limit='" . $data[22] . "' ";
			    $setstmnt = $setstmnt ."c.account_balance='" . $data[23] . "' ";
			    $setstmnt = $setstmnt ."c.enquiry_only='" . $data[24] . "' ";		    
			    $setstmnt = $setstmnt ."c.hide_rrp='" . $data[25] . "' ";		    
		    	$setstmnt = $setstmnt ."c.default_branch_id='" . $branch . "' ";
				//build query
				$query = $updstmnt . $setstmnt . $whrstmnt;
if($debug) echo $query . "<br />";
				//update row
				$result=mysql_query($query);
			} else {
				//run query to insert customer rec and address
				//insert address
				$query="insert into addresses (customer_id,address_line1,address_line2,address_line3,address_line4,address_line5,postcode,addressee) values (";	
				$query = $query . "null,"; 
				$query = $query . "'" . $data[12] . "',"; 
				$query = $query . "'" . $data[13] . "',"; 
				$query = $query . "'" . $data[14] . "',"; 
				$query = $query . "'" . $data[15] . "',"; 
				$query = $query . "'" . $data[16] . "',";
				$query = $query . "'" . $data[16] . "',";  
				$query = $query . "'" . $data[11] . "');"; 			 
if($debug) echo $query . "<br />";
				//run query to insert customer rec
				$result=mysql_query($query);
				
				//find newly added address id
				$del_add = mysql_insert_id();
				
				//insert customer
				//echo 'inserting customer<br />';
				$query="insert into customers (title,first_name,surname,phone,mobile,fax,account_no,delivery_add_id,default_branch_id,credit_limit,trade_limit,account_balance,on_stop_flag,enquiry_only,hide_rrp) values ";	
			    $query = $query."('" .$data[5] . "',";
			    $query = $query."'". $data[6] . "',";
			    $query = $query."'". $data[7] . "',";
			    $query = $query."'". $data[8] . "',";
			    $query = $query."'". $data[9] . "',";
			    $query = $query."'". $data[10] . "', ";
			    $query = $query."'". rtrim($data[4]) . "', ";
			    $query = $query."'". $del_add . "', ";
			    $query = $query."'". $branch . "', ";
			    $query = $query."'". $data[21] . "', ";
			    $query = $query."'". $data[22] . "', ";
			    $query = $query."'". $data[23] . "', ";
			    $query = $query."'". $data[19] . "', ";
			    $query = $query."'". $data[24] . "') ;"; 
				//insert row
if($debug) echo $query . "<br />";
				$result=mysql_query($query);
				
				//find newly added customer
				$cust = mysql_insert_id();
			}
			//insert user
			$query="insert into b2busers.users (cust_admin,username,password,customer_id,company_id,status) values (";	
			$query = $query . "'" . $data[1] . "',"; 
			$query = $query . "'" . $data[2] . "',"; 
			$query = $query . "'" . $data[3] . "',"; 
			$query = $query . "'" . $cust . "',"; 
			$query = $query . "'" . $company_id . "',"; 
			if ($data[18] == 'Y') {
			    $status = "D";
	    	} else {
		    	$status = "A";
	    	}
			$query = $query . "'" .$status."');"; 
if($debug) echo $query . "<br />";
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
	if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		//delete all files
		$filename = $dirname . $dirArray[$index];
		//unlink($filename);
	}
}


//disconnect
include 'LDclosedb.php';
?> 
