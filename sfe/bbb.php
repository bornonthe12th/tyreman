<?php session_start();

// connect to MySQL
include 'B2BFunctions.php';

//get vars. from session
$def_branch = $_SESSION['default_branch'];
$cust = $_SESSION['customerid'];
$companyid = $_SESSION['companyid'];
$session = session_id();

//use posted value if set
if (isset($_SESSION['selected_branch']))
        {
                $branch = $_SESSION['selected_branch'];
        } else {
                $branch = '';
        }

//are they allowed to change branch
$chg_branch = GetCompanySetting('AllowBranchChange');
$chg_branch="N";
var_dump($chg_branch);
if ($chg_branch == 'Y')
        {
        $basketEmpty = True;
        $query="call BranchList();";

        //connect to customer DB.
        $cust_db_conn = mysqli_connect("localhost", $_SESSION['dbusername'] ,$_SESSION['dbpassword'], $_SESSION['dbschema'])
                        or die('unable to connect to b2b DB');

        //run query
        $result=mysqli_query($cust_db_conn,$query);
        $num=mysqli_num_rows($result);
        //$row = mysqli_fetch_array($result);

        //loop round results
        if ($num>0) 
		{
		while($row = mysqli_fetch_array($result)) 
			{
			echo $row['branch_id'];
			echo $row['description'];
			}

		}

       } else {

        //connect to customer DB.
        $cust_db_conn = mysqli_connect("localhost", $_SESSION['dbusername'] ,$_SESSION['dbpassword'], $_SESSION['dbschema'])
                        or die('unable to connect to b2b DB');

        //get branch
        $query="select description from branches where branch_id = $def_branch;";
        //run query
        $result=mysqli_query($cust_db_conn,$query) or die('failed');
        $num=mysqli_num_rows($result);
        $row = mysqli_fetch_array($result);

        //we got a result
        if ($num>0)
                {
                echo $row["description"];
                }
        }


?>
