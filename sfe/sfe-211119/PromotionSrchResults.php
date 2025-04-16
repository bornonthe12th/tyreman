<?php //session_start();

// Javascript required for table sort headings

echo "<script type='text/javascript' src='scripts/common.js'></script>";
echo "<script type='text/javascript' src='scripts/css.js'></script>";
echo "<script type='text/javascript' src='scripts/standardista_table_sorting.js'></script>";

$companyid = $_SESSION['companyid'];
// company 5 - BA Bush
switch ($companyid)
               {
               case ($companyid=='5'):
                //$Special='http://www.bushtyresintranet.co.uk/bush.jpg?dummy=48484848';
                $Special='images/bush.jpg';
		$SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
                break;

		case ($companyid=='11'):
                $Special='http://www.bushtyresintranet.co.uk/endyke.jpg?dummy=48484848';
		$SpecialText = shell_exec("/usr/local/bin/getMOTD.ksh $companyid");
                break;

                break;
                }

$vat = 20.0;

	//get price modifiers
	$query="call GetAccountDetails($cust);";
	//run query
	$srchresult=mysql_query($query);
	$num=mysql_numrows($srchresult);
	if ($num > 0){
		$vatflag = mysql_result($srchresult,0,"IncVatFlag");
		$markupval = mysql_result($srchresult,0,"markupval");
		$markuppct = mysql_result($srchresult,0,"markuppc");
		$DefToSellFlag = mysql_result($srchresult,0,"DefToSellFlag");
       	$Show_rrp = mysql_result($srchresult,0,"Show_rrp");
       	$Show_rrp4 = mysql_result($srchresult,0,"Show_rrp4");
       	$Hide_rrp = mysql_result($srchresult,0,"hide_rrp");
       	$Account_No = mysql_result($srchresult,0,"Account_No");
   	$show_cust_spec = mysql_result($srchresult,0,"show_cust_specials");
	    }
//var_dump($Account_No);echo "<br/>\n";
	//reconnect
	include 'Reconnect.php';

//var_dump($Hide_rrp); echo "<br/>\n";
//if we have some search criteria
$branch = $_SESSION['default_branch'];
if ($scode == '')  
	{
		//call search proc
		$query="call StockSearchPromotion('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";	
	
	$srchresult=mysql_query($query) or die(mysql_error());
	$num=mysql_num_rows($srchresult);
			
	
	//loop round results
	if ($num>0)
		{
		
		//products found
		$i=0;
		
		//write table header
	  	echo "<table width='700' id=PromoTable align=center>";
       if ($show_cust_spec == 'Y')
                {
                echo "<tr><td><img src='$Special?dummy=48484848'></td></tr>";
                echo "<tr><td><marquee direction='left' onmouseover='this.stop()' onmouseout='this.start()' >$SpecialText</marquee></td></tr>";
                }
		// Added 2 new rows to display daily special pic/message ALS 02.06.15
		// made display conditional daily special pic/message ALS 25.06.15
		echo "<tr><td CLASS=maintitle>&nbsp;<img src='images/promotion-icon.gif' />&nbsp;&nbsp;On Promotion</td></tr>";
		echo "<tr><td>";
		echo "<table class=\"sortable\">";
		echo "<thead><tr>";
		echo "<th class=titlemedium>Description</th>";
		echo "<th class=titlemedium>Mfr.</th>";
		echo "<th class=titlemedium>Fuel</th>";
		echo "<th class=titlemedium>Wet</th>";
		echo "<th class=titlemedium>Noise</th>";
		echo "<th class=titlemedium>TL</th>";
		

		if (mysql_result($srchresult,0,"show_stock_flag")=="Y")
		   {
			echo "<th class=titlemedium>Co. Stk</th>";
		   }

		echo "<th class=titlemedium>Cost</th>";
		echo "<th class=titlemedium>Basket</th></tr></thead>";

		echo "<tbody>";	/* table body starting (required by the js that 
				   does the table sort headings) */

		while ($i < $num)
			{

			//write one row of table
			if ($i/2 == round($i/2))
			   {
				$tr_row_class = '';
			   } else {
				$tr_row_class = " class='odd'";
			   }

            		$td_col_class = "class='promoeulabel'";
			$td_price_col_class = "class='promoprice'";
			// highlight cost price for special items or show as normal if not
		  	//if (mysql_result($srchresult,$i,"highlight") == 'Y')
	  		 //  {
			//	$tr_row_class = " class='highlight'";
			//	$td_col_class = "class='highlight-eulabel'";
			//	$td_price_col_class = "class='highlight-price'";
			//   }

			$productid = mysql_result($srchresult,$i,"product_id");
			
			//echo "\n<tr $tr_row_class  onmouseover=\"this.style.backgroundColor='red'\"; onmouseout=\"this.style.backgroundColor=''\";>";
			echo "\n<tr $tr_row_class >";

			
		  	echo "<td class='promotext'>";
			echo "<img src='images/promotion-icon.gif' />&nbsp;&nbsp;";
		  	echo substr(mysql_result($srchresult,$i,"description"),0,80);
		  	echo "</td>";			
		  	echo "<td class='promotext'>";
		  	echo mysql_result($srchresult,$i,"manufacturer");
		  	echo "</td>";
			
			
		
                        // Additions EU Tyre labelling fields plus Winter Tyre, Extra Load and Run flat icons
                        echo "<td $td_col_class >";
                        echo mysql_result($srchresult,$i,"fuel_efficiency");
                        echo "<td $td_col_class >";
                        echo mysql_result($srchresult,$i,"wet_braking") ;
                        echo "</td>";
                        echo "<td $td_col_class >";
 			//echo mysql_result($srchresult,$i,"decibels");
                        //echo "!";
                        $scrp =  mysql_result($srchresult,$i,"decibels");
                        //echo $noise;
                        $noise = trim($scrp,"\0 ");
                        echo $noise;
                        if ($noise != "") {
                                echo "db";
                                }

                        echo "</td>";
                                        // Show tyre Options columns, currently BAB and Tyreman demo/dev regions only
                                        if ($companyid == '2' or $companyid == '3' or $companyid == '5' or $companyid == '11' or $companyid == '16')
                        {
                                    echo "<td $td_col_class >";
/*Only show label if all label variables have data in them*/
 if (trim(mysql_result($srchresult,$i,"fuel_efficiency")) != "" and
     trim(mysql_result($srchresult,$i,"wet_braking")) != ""  and
     trim(mysql_result($srchresult,$i,"vehicle_class")) != ""){
        echo "<a href='http://www.tyreman.co.uk/eulabel.php?id=" . mysql_result($srchresult,$i,"fuel_efficiency") . "&id2=" . mysql_result($srchresult,$i,"wet_braking") . "&id3=" . mysql_result($srchresult,$i,"noise_rating") . "&id4=" . mysql_result($srchresult,$i,"decibels") . "&id5=" . mysql_result($srchresult,$i,"vehicle_class") . "&id6=" . mysql_result($srchresult,$i,"stockcode") . "' target='_blank' ><img src='images/TyreLabelIcon.jpg' width='16' height='16'" ?> onMouseOver="Tip('Tyre Label')" <?php ">";
                                                        }

			}

                                    echo "</td>";
	
			
		  	//show stocklevel
		  	if (mysql_result($srchresult,$i,"show_stock_flag")=="Y")
		  	   {
		  		//get ttl stock for stock code
		  		$ttlstock = GetTotalStock(mysql_result($srchresult,$i,"stockcode"));
		  		echo "<td style=\"padding:0px 2px; text-align:right; font-size:14px\">$ttlstock</td>";
			   }
				//banded stocklevel
			   else if (mysql_result($srchresult,$i,"show_stock_flag")=="B")
			   {
				echo "<td style=\"padding:0px 2px; text-align:right;\">";
				echo getStockBand(mysql_result($srchresult,$i,"stocklevel"));
				echo "</td>";
			   }



	  	if($_SESSION['selected_branch'] == $_SESSION['default_branch'])
		  {
			$custprice = mysql_result($srchresult,$i,"netprice");
	 	  } else {
			//reconnect
			include 'Reconnect.php';
			$query = "SELECT netprice FROM prices WHERE stockcode = '" . mysql_result($srchresult,$i,"stockcode") . "' AND customer_id = $cust";
			$srchresult2 = mysql_query($query) or die(mysql_error());
			$num2 = mysql_num_rows($srchresult2).'<br />';
			if($num2 > 0) 
			  {
				$custprice = mysql_result($srchresult2,"netprice");
			  } else {
				$custprice = 0;
				 }
			  }
	
			$buyprice = $custprice;
			echo "<td $td_price_col_class;\">" .
			number_format($custprice,2) . "</td>";
	
		  	
	   	echo "<td style='text-align:center;'>";
		if ($custprice > 0)
		   {
		   //reconnect
		   include 'Reconnect.php';
		   $query="SELECT * FROM customers WHERE Customer_id = '".$cust."'";  	
		   //run query
		   $enquiry_result=mysql_query($query) or die(mysql_error());
		   $num_enq=mysql_numrows($enquiry_result);
		   $enquiry_only = 'N';
		   if ($num_enq > 0)
		      {
		      $enquiry_only = mysql_result($enquiry_result,0,"Enquiry_Only");
		      }
		   if(mysql_result($customer_stop,0,"On_Stop_Flag") == 'Y') 
  		     {
		  	echo '<span style="color:red;">On Stop</span>';
		     } else if($enquiry_only == 'Y')
			      {
				echo 'n/a';				 		
			      } else {
  					echo '<a href=addtobasket.php?productid=' . 
						$productid .'&qty=1&price=' . 
						$buyprice . '>Add</a></td></tr>';
			      }
	  	   } else {
	  			echo 'Call</td></tr>';
		   }

	  		
	  	$i++;
	  		
		}		// endof while ($i < $num)
		
		echo "</tbody></table>";
		echo "</td></tr>";
		echo "</table>";
	}
	else
		echo "<div id=Message_Div>No Promotions Found.</div>";

  }

else
	echo "<div id=Message_Div>YEAH!! Running PROMOTION PAGE</div>";

?>
