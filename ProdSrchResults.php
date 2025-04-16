<?php

// Javascript required for table sort headings

echo "<script type='text/javascript' src='scripts/common.js'></script>";
echo "<script type='text/javascript' src='scripts/css.js'></script>";
echo "<script type='text/javascript' src='scripts/standardista_table_sorting.js'></script>";

// Javascript for tool tip text pop up
echo "<script type='text/javascript' src='/scripts/wz_tooltip.js'></script>"; 



$companyid = $_SESSION['companyid'];

$vat = 20.0;

if ($spdisp !='B' or $spdisp != "S")
{
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
		if (!mysql_result($srchresult,0,"hide_rrp")) {
		 $Hide_rrp = "n";
		} else $Hide_rrp = mysql_result($srchresult,0,"hide_rrp");
        	$Account_No = mysql_result($srchresult,0,"Account_No");
		    }
//var_dump($Account_No);echo "<br/>\n";
	//reconnect
	include 'Reconnect.php';

}
//var_dump($Hide_rrp); echo "<br/>\n";
//if we have some search criteria
if (($scode) OR ($sdesc) or ($sman) or ($size)) 
	{
		//call search proc
		if($_SESSION['selected_branch'] != $_SESSION['default_branch'])
		  {
		  $query="call StockSearchBranch('$scode',$cust,'$sdesc','$spgroup',
			'$sman','$sptype','$sspecflag','$szstockflag','$size',
			'$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";
		  } else {
		  $query="call StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman',
		  '$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch','$Account_No','$winterfilter','$xlfilter','$rffilter');";	
		  }
	
	$srchresult=mysql_query($query) or die(mysql_error());
	$num=mysql_num_rows($srchresult);
			
	if(($num > 0) && ($_SESSION['selected_branch'] != $_SESSION['default_branch'])) 
			// We need to load the prices that the default branch uses
		{
		include 'Reconnect.php';
		
		$query2="call StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman',
			'$sptype','$sspecflag','$szstockflag','$size','$sortprodlist',
			'$_SESSION[default_branch]','$winterfilter','$xlfilter','$rffilter');";
		$srchresult2=mysql_query($query2);
		$num2=mysql_num_rows($srchresult2);
		}

	//loop round results
	if ($num>0)
		{
		
		//products found
		$i=0;
		
		//write table header
	  	echo "<table id=BlueTable >";
		echo "<tr><td CLASS=maintitle>Search Results</td></tr>";
		echo "<tr><td>";
		echo "<table class=\"sortable\">";
		echo "<thead><tr>";
		echo "<th class=stcodetitle>Stock Code</th>";
		echo "<th class=titlemedium>Description</th>";
		echo "<th class=titlemedium>Manufacturer</th>";
		echo "<th class=titlemedium>Fuel</th>";
		echo "<th class=titlemedium>Wet</th>";
		echo "<th class=titlemedium>Noise</th>";
		// Show tyre Options columns, currently BAB and Tyreman demo/dev regions only
		//if ($companyid == '2' or $companyid == '3' or $companyid == '5' or $companyid == '11' or $companyid == '16')
			//{
   				echo "<th class=titlemedium>TL</th>";
		//BAB show icon in heading for picture of product
		if ($companyid == '5')
		  {
		    	echo "<th class=titlemedium>Image</th>";
    			//echo "<th class=titlemedium> <img src='images/camera-solid.svg' height='16px' width='16px' /> </th> ";

		  }


				echo "<th class=titlemedium>W</th>";
				echo "<th class=titlemedium>XL</th>";
				echo "<th class=titlemedium>RF</th>";
			//}
		

		if (mysql_result($srchresult,0,"show_stock_flag")=="Y")
		   {
			echo "<th class=titlemedium>Br. Stk</th>";
//Show hub stock for BABush
                        if ($companyid == '5' or $companyid == '11')
                        {
                        echo "<th class=titlemedium>Hub Stk</th>";
                        }
                         if ($companyid != '11' )
                            {
			    echo "<th class=titlemedium>Co. Stk</th>";
			    }

		   }

		//Show 48hr stock column for BAB
		if ($companyid == '5')
		  {
		    	echo "<th class=titlemedium>48hr Stk</th>";
		  }

		if ($spdisp == "B" or $spdisp == "X")
		   {
			echo "<th class=titlemedium>Cost Price</th>";
		   }
		// If Sell or Both Prices selected
		if ($spdisp == "S" or $spdisp == "X")
		   {
			echo "<th class=titlemedium>Sell Price</th>";
			switch ($companyid)
               		{

                        case ($companyid=='5' || $companyid=='11'):
			// BABush additional RRP & RRP2 Columns
			if (($Show_rrp == "Y") && ($Hide_rrp !== "Y"))
				{
					echo "<th class=titlemedium>RRP</th>";
				}
                        if (($Show_rrp4 == "Y") && ($Hide_rrp !== "Y"))
				{
					echo "<th class=titlemedium>RRP4</th>";
				}
                        break;

                	}
		   }

		echo "<th class=titlemedium>Basket</th></tr></th>";

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

            $td_col_class = "class='eulabel'";
			$td_price_col_class = "class='price'";
			// highlight cost price for special items or show as normal if not
		  	if (mysql_result($srchresult,$i,"highlight") == 'Y')
	  		   {
				$tr_row_class = " class='highlight'";
				$td_col_class = "class='highlight-eulabel'";
				$td_price_col_class = "class='highlight-price'";
			   }

			$productid = mysql_result($srchresult,$i,"product_id");
			
			echo "\n<tr $tr_row_class >";

			echo "<td>";
		  	echo mysql_result($srchresult,$i,"stockcode");
		  	echo "</td>";
		  	echo "<td>";
		  	echo substr(mysql_result($srchresult,$i,"description"),0,80);
		  	echo "</td>";			
		  	echo "<td>";
		  	echo mysql_result($srchresult,$i,"manufacturer");
		  	echo "</td>";
			
			
			// Additions EU Tyre labelling fields plus Winter Tyre, Extra Load and Run flat icons
			echo "<td $td_col_class >";
		  	echo mysql_result($srchresult,$i,"fuel_efficiency");
		  	echo "<td $td_col_class >";
		  	echo mysql_result($srchresult,$i,"wet_braking") ;
		  	echo "</td>";
			echo "<td $td_col_class >";

                        $scrp =  mysql_result($srchresult,$i,"decibels");
                        //echo $noise;
                        $noise = trim($scrp,"\0 ");
                        echo $noise;
                        if ($noise != "") {
                                echo "db";
                                }

                        echo "</td>";
                                        // Show tyre Options columns, currently BAB and Tyreman demo/dev regions only
                                        //if ($companyid == '2' or $companyid == '3' or $companyid == '5' or $companyid == '11' or $companyid == '16')
                       // {
                                    echo "<td $td_col_class >";
/*Only show label if all label variables have data in them*/
//show 2020 Tyre label URL if found
if (trim(mysql_result($srchresult,$i,"url")) != "") {
echo "<a href='" . mysql_result($srchresult,$i,"url") . "' target='_blank' ><img src='images/TyreLabelIcon.jpg' width='16' height='16'" ?> onMouseOver="Tip('Tyre Label')" <?php ">";
}
//Show old style Tyre label
if (trim(mysql_result($srchresult,$i,"url")) == "" and trim(mysql_result($srchresult,$i,"fuel_efficiency")) != "" and
     trim(mysql_result($srchresult,$i,"wet_braking")) != ""  and
     trim(mysql_result($srchresult,$i,"vehicle_class")) != ""){
	echo "<a href='http://www.tyreman.co.uk/eulabel.php?id=" . mysql_result($srchresult,$i,"fuel_efficiency") . "&id2=" . mysql_result($srchresult,$i,"wet_braking") . "&id3=" . mysql_result($srchresult,$i,"noise_rating") . "&id4=" . mysql_result($srchresult,$i,"decibels") . "&id5=" . mysql_result($srchresult,$i,"vehicle_class") . "&id6=" . mysql_result($srchresult,$i,"stockcode") . "' target='_blank' ><img src='images/TyreLabelIcon.jpg' width='16' height='16'" ?> onMouseOver="Tip('Tyre Label')" <?php ">";
}
			echo "</td>";
			//Show camera icon if there is an image associated to the stock code
			if ($companyid == '5')
				{
					echo "<td $td_col_class >";
					if (mysql_result($srchresult,$i,"image_name") !="") 
					{
						echo "<a href='images/BAB/" . mysql_result($srchresult,$i,"image_name") . "' target='_blank' ><img src='images/camera-solid.svg' height='16px' width='16px' style='display: block; margin: auto;'" ?> onMouseOver="Tip('View item')" <?php ">";
					}
					echo "</td>";
				}
            echo "<td $td_col_class >";
			if (mysql_result($srchresult,$i,"winter")=="Y") 
				{
				echo "<span style='VISIBILITY:hidden;display:none'>" . mysql_result($srchresult,$i,"winter") . "</span>";
				echo "<img src='images/winter.gif' width='19' height='17' " ?> onMouseOver="Tip('Winter')" <?php ">";
				}
			echo "</td>";
			echo "<td $td_col_class >";
			if (mysql_result($srchresult,$i,"extraload")=="Y") {
				echo "<span style='VISIBILITY:hidden;display:none'>" . mysql_result($srchresult,$i,"extraload") . "</span>";
				echo "<img src='images/xl.gif' width='19' height='17' " ?> onMouseOver="Tip('Extra Load')" <?php ">";
				}
			echo "</td>";
			echo "<td $td_col_class >";
			if (mysql_result($srchresult,$i,"runflat")=="Y") {
				echo "<span style='VISIBILITY:hidden;display:none'>" . mysql_result($srchresult,$i,"runflat") . "</span>";
				echo "<img src='images/runflat.gif' width='19' height='17' " ?> onMouseOver="Tip('Run Flat')" <?php ">";
				}
			echo "</td>";
			
		  	//show stocklevel
		  	if (mysql_result($srchresult,$i,"show_stock_flag")=="Y")
		  	   {
		  		echo "<td style=\"padding:0px 2px; text-align:right;\">" . mysql_result($srchresult,$i,"stocklevel") . "</td>";
				//Show hub stock if BAB branch
				if ($companyid == '5' or $companyid == '11')
                    {
						echo "<td style=\"padding:0px 2px; text-align:right;\">" . mysql_result($srchresult,$i,"regionstk") . "</td>";	
                    }
		  		//get ttl stock for stock code
		  		$ttlstock = GetTotalStock(mysql_result($srchresult,$i,"stockcode"));
				//Don't show company stock for endyke
                if ($companyid != '11')
					{
					echo "<td style=\"padding:0px 2px; text-align:right;\">$ttlstock</td>";
                    }
				//Show BAB 48hr stock qty
				if ($companyid == '5')
					{
						echo "<td style=\"padding:0px 2px; text-align:right;\">" . mysql_result($srchresult,$i,"supplier_stock") . "</td>";
					}
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
	
			if ($spdisp =='B' or $spdisp =='X') 
			   // B = Cost      X = Both cost and sell       i.e. if cost price is required
		  	   {
			  	$buyprice = $custprice;
			  	echo "<td $td_price_col_class;\">" .
					 number_format($custprice,2) . "</td>";
		  	   }
	
		  	if ($spdisp =='S' or $spdisp =='X') 
			   // S = Sell price     X = Both      i.e. if selling price is required
			   {
				$buyprice = $custprice;
				$custprice = $custprice + $markupval;
				$custprice = $custprice * (1+($markuppct/100));

				if ($vatflag == 'Y') 
				   $custprice = ($custprice / 100) * ($vat) + $custprice;
	
				//round to 2 dec places
				$custprice = round($custprice,2);
			  	//add vat + markup as needed for cust
		  		echo "<td $td_price_col_class;\">" . 
					number_format($custprice,2) . "</td>";
			   }
				
			// Show RRP AND/OR RRP4
			switch ($companyid)
				{
				case 1:
				//echo $companyid;
				break;

			 	case 2:
				//echo $companyid;
				break;

				case ($companyid=='5' || $companyid=='11'):
				// BABush additional RRP & RRP2 Columns
				if ($spdisp == "S" or $spdisp == "X")
				   {
					if (($Show_rrp == "Y") && ($Hide_rrp !== "Y"))
				  	   {
					   //RRP
				  	   $rrpval = mysql_result($srchresult,$i,"rrp");
					   echo "<td style=\"padding:0px 2px; text-align:right;\">" . 
						number_format($rrpval,2) . "</td>";
					   }
					if (($Show_rrp4 == "Y") && ($Hide_rrp !== "Y"))
					   {
					   //RRP4
					   $rrp4val = mysql_result($srchresult,$i,"rrp4");
					   echo "<td style=\"padding:0px 2px; text-align:right;\">" . 
						number_format($rrp4val,2) . "</td>";
					   }
				break;
				} // End switch statement

		}	// End default branch query

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
  					echo '<a title=Add&nbsp;to&nbsp;Basket href=addtobasket.php?productid=' . 
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
		echo "<div id=Message_Div>No Matching Products Found.</div>";

  }

else
	echo "<div id=Message_Div>Minimum Browser Version Requirements: IE 11, Edge 14, Firefox 50, Chrome 55, Safari 10, Opera 42, IOS Safari 10.2, Opera Mobile 37, Chrome Android 55</div>";

?>
