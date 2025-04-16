<?php
// #########################################################################################
// #
// #	Filename : 	login.php
// #	Version  :	2.0
// #	Author	 :	A.Steward
// #	Date	 :	21/11/19
// #	Amendment history :-
// #
// #########################################################################################

// #########################################################################################
// #	Function definition
// #########################################################################################

function pr($data)
{
    echo "<pre>";
    print_r($data); // or var_dump($data);
    echo "</pre>";
}

function tError($message,$source){
        //logfile
        $myFile = "logs/error.log";
        //open logfile
        $fh = fopen($myFile, 'a') or die("can't open file");
        //time and date
        $stringData = date("d/m/Y G:i:s");
        fwrite($fh,$stringData);
        fwrite($fh, " ");
        //message
        fwrite($fh, $message);
        fwrite($fh, " ");
        //source
        fwrite($fh, $source);
        //end line
        fwrite($fh, "\n");
        //close
        fclose($fh);
}

function GetDefaultBranch($cust)
{
//connect to customer database
$custdb_conn = mysqli_connect(dbserver,dbuser,dbpass,$_SESSION['dbschema']);
        $query="call GetDefaultBranch('" . $cust . "');";

        //run query
        $result=mysql_query($query);
        $value = mysql_result($result,0,"default_branch_id")
                 OR die("B2BFunctions.php : No value for default branch id<br /><br />$query");

    return $value;


}

// #########################################################################################
// #	Variable definition
// #########################################################################################

define('dbserver', 'localhost');
define('dbuser', 'root');
define('dbpass', 'syslib');
define('dbname', 'b2busers');

//copy parms to local vars
if( isset($_POST['username']) and isset($_POST['password']))
{
  $username = $_POST['username'];
  $password = $_POST['password'];
}

// #########################################################################################

// connect to MySQL
$db_conn = mysqli_connect(dbserver,dbuser,dbpass,dbname);

// set up query
$query="call B2BLogin('$username','$password');";

        //run query
        $result=mysqli_query($query);
        $num=mysqli_num_rows($result);



        if ($num==1)
           {
                //user found
                $i=0;

                session_start(); // start up your PHP session!

                while ($i < $num)
                      {
			$row = mysqli_fetch_array($result)
                        //set session vars
                        $_SESSION['db_conn'] = $db_conn;
                        $_SESSION['dbusername'] = $row["DBuserName"];
                        $_SESSION['dbpassword'] = $row["DBpassword"];
                        $_SESSION['dbschema'] = $row["DBschema"];
                        $_SESSION['customerid'] = $row["customer_id"];
                        $_SESSION['stylesheet'] = $row["stylesheet"];
                        $_SESSION['printstylesheet'] = $row["printstylesheet"];
                        $_SESSION['description'] =  $row["description"];
                        $_SESSION['companyid'] =  $row["company_id"];
                        $_SESSION['uid']=$username; 

                     $i++;

                     }


                //disconnect from b2busers db
                mysql_close($db_conn);

                $_SESSION['default_branch'] = GetDefaultBranch($_SESSION['customerid']);
                $_SESSION['selected_branch'] =  $_SESSION['default_branch'];

                // record login details 
                // go to function to record date/time of login
                tError("User logged on ",$username." - ".$password." login.php");
                $URL="B2BUpdateDate.php";
                session_write_close();
                header ("Location: $URL");

                } else {

                //user not found, write to log
		if ( !isset ($username))
		   {
		   $username="blank";
		   }
		if ( !isset ($password))
		   {
		   $password="blank";
		   }
                tError("User not found ",$username." - ".$password." login.php");
                //disconnect from usersdb
                mysql_close($db_conn);
                //go back to login
                $URL="B2BLogin.php?error=Y";
                session_write_close();
                header ("Location: $URL");
        }

?> 
