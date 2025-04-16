<?php

// Javascript required for table sort headings

echo "<script type='text/javascript' src='common.js'></script>";
echo "<script type='text/javascript' src='css.js'></script>";
echo "<script type='text/javascript' src='standardista_table_sorting.js'></script>";

$companyid = $_SESSION['companyid'];
// b2bheader.php altered to refer to css in current directory as it wouldn't work when referring to other directories


$vat = 20.0;

/* for test purposes only - steve cordingley

foreach($_SESSION as $q1 => $q2)
echo "$q1 $q2<br />";
die();

*******************/




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
        // GR 02-Feb-11 Get customer show RRP flags
        $Show_rrp = mysql_result($srchresult,0,"Show_rrp");
        $Show_rrp4 = mysql_result($srchresult,0,"Show_rrp4");
	}
	//reconnect
	include 'Reconnect.php';

}


//if we have some search criteria
if (($scode) OR ($sdesc) or ($sman) or ($size)) 
{
	//call search proc
	//set up query
		if($_SESSION['selected_branch'] != $_SESSION['default_branch'])
	{
		$query="call StockSearchBranch('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch');";
	}
	else
	{
		$query="call StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$branch');";	
	}
	
	$srchresult=mysql_query($query) or die(mysql_error());
	$num=mysql_num_rows($srchresult);
	//echo 'num = '.$num.'<br />';
	//echo 'cust = '.$cust.'<br />';
	

			
	if(($num > 0) && ($_SESSION['selected_branch'] != $_SESSION['default_branch'])) // We need to load the prices that the default branch uses
	{
		include 'Reconnect.php';
		
				
		$query2="call StockSearch('$scode',$cust,'$sdesc','$spgroup','$sman','$sptype','$sspecflag','$szstockflag','$size','$sortprodlist','$_SESSION[default_branch]');";
		$srchresult2=mysql_query($query2);
		$num2=mysql_num_rows($srchresult2);
	}

	//loop round results
	if ($num>0)
	{
		
		//products found
		$i=0;
		
		//write table header
	  	echo "<table width='770' id=BlueTable align=center>";

		echo "<tr><td CLASS=maintitle>Search Results</td></tr>";
		echo "<tr><td>";
		echo "<table class=\"sortable\">";
		echo "<thead><tr>";
		echo "<th class=stcodetitle>Stock Code</th>";
		echo "<th class=titlemedium>Description</th>";

  		if(isset($_SESSION['savoy']) && $_SESSION['savoy'])				// Savoy need the rating shown as selling prices depend on it.
			echo "<th class=\"titlemedium\" style=\"padding:0px 10px;\">Rating</th>";


		echo "<th class=titlemedium>Mfr.</th>";




		if (mysql_result($srchresult,0,"show_stock_flag")=="Y")
		{
			echo "<th class=titlemedium>Br. Stk</th>";
			echo "<th class=titlemedium>Co. Stk</th>";
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
                        case 1:
                        break;

                        case 2:
                        break;

                        case 5:
						// BABush additional RRP & RRP2 Columns
				  		if ($Show_rrp == "Y")
						{
								echo "<th class=titlemedium>RRP</th>";
						}
                        if ($Show_rrp4 == "Y")
						{
								echo "<th class=titlemedium>RRP4</th>";
						}
                        break;
                }
		}

		echo "<th class=titlemedium>Buy</th></tr></thead>";

		echo "<tbody>";	// table body starting (required by the js that does the table sort headings)

		while ($i < $num)
		{

			//write one row of table
			if ($i/2 == round($i/2))
			{
					$tr_row_class = '';
			}

			else
			{
				$tr_row_class = " class='odd'";
			}


			// highlight cost price for special items or show as normal if not
		  	if (mysql_result($srchresult,$i,"highlight") == 'Y')
	  		{
				$tr_row_class = " class='highlight'";
			}

			$productid = mysql_result($srchresult,$i,"product_id");
			
			echo "\n<tr $tr_row_class  onmouseover=\"this.style.backgroundColor='red'\"; onmouseout=\"this.style.backgroundColor=''\";>";

			if(!$_SESSION['savoy'])			// Savoy don't require the stock code
			{
				echo "<td>";
			  	echo mysql_result($srchresult,$i,"stockcode");
			  	echo "</td>";
			}
		
		  	echo "<td >";
		  	echo substr(mysql_result($srchresult,$i,"description"),0,80);
		  	echo "</td>";

		  	
		  if(isset($_SESSION['savoy']) && $_SESSION['savoy'])
		  {
			// Get the speed rating letter
	
			$size = mysql_result($srchresult,$i,"size");

		  	if(strlen($size))
			  	$speed_rating = strtolower(substr($size,-1));
		  	else
		  		$speed_rating = '';		  	
		  	
		 	echo "<td align=\"center\" style=\"padding:0px 10px;\">" . strtoupper($speed_rating) . "</td>";
	 	 }

		  	echo "<td style=\"padding:0px 10px;\">";
		  	echo mysql_result($srchresult,$i,"manufacturer");
		  	echo "</td>";


		  	//show stocklevel
		  	if (mysql_result($srchresult,$i,"show_stock_flag")=="Y")
		  	{
		  		echo "<td style=\"padding:0px 10px; text-align:right;\">" . mysql_result($srchresult,$i,"stocklevel") . "</td>";
	
		  		//get ttl stock for stock code
		  		$ttlstock = GetTotalStock(mysql_result($srchresult,$i,"stockcode"));
		  		echo "<td style=\"padding:0px 10px; text-align:right;\">$ttlstock</td>";
				}
				//banded stocklevel
				else if (mysql_result($srchresult,$i,"show_stock_flag")=="B")
				{
					echo "<td style=\"padding:0px 10px; text-align:right;\">";
					echo getStockBand(mysql_result($srchresult,$i,"stocklevel"));
					echo "</td>";
				}



		  // Savoy require special handling of selling out prices based on their cost marked up
		  // by a gm percentage based on manufacturer and speed rating.
		  // The markups are obtained by login.php from Savoy's own database at www.savoytyres.co.uk
	
		  if(isset($_SESSION['savoy']) && $_SESSION['savoy'])
		  {
			include 'savoy_calc_and_disp_prices.php';
	  	  }
		  else
		  {
			  	//echo 'searchresult scode = '.mysql_result($srchresult,$i,"stockcode").'<br />';
				//echo 'searchresult2 scode = '.mysql_result($srchresult2,$i,"stockcode").'<br />';
				
	  			if($_SESSION['selected_branch'] == $_SESSION['default_branch']) {
					$custprice = mysql_result($srchresult,$i,"netprice");
				//else if(mysql_result($srchresult,$i,"stockcode") == mysql_result($srchresult2,$i,"stockcode")) // Check price is for same product (no reason why it shouldn't be, but worth checking)
					//$custprice = mysql_result($srchresult2,$i,"netprice");
				} else {
					
					//die("An error has occurred, can't load prices for default branch");
					//reconnect
					include 'Reconnect.php';
					$query = "SELECT netprice FROM prices WHERE stockcode = '" . mysql_result($srchresult,$i,"stockcode") . "' AND customer_id = $cust";
					$srchresult2 = mysql_query($query) or die(mysql_error());
					$num2 = mysql_num_rows($srchresult2).'<br />';
					if($num2 > 0) {
						$custprice = mysql_result($srchresult2,"netprice");
					}
					else
					{
						$custprice = 0;
					}
				}
	
				if ($spdisp =='B' or $spdisp =='X') // B = Cost      X = Both cost and sell       i.e. if cost price is required
		  		{
			  		
			  		$buyprice = $custprice;
			  		echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format($custprice,2) . "</td>";
		  		}
	
	
			  	if ($spdisp =='S' or $spdisp =='X') // S = Sell price     X = Both      i.e. if selling price is required
			  	{
				  	$buyprice = $custprice;
					$custprice = $custprice + $markupval;
					$custprice = $custprice * (1+($markuppct/100));

					if ($vatflag == 'Y') 
						$custprice = ($custprice / 100) * ($vat) + $custprice;

	
					//round to 2 dec places
					$custprice = round($custprice,2);
				  	//add vat + markup as needed for cust
			  		echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format($custprice,2) . "</td>";
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
					case 5:
					// BABush additional RRP & RRP2 Columns
					if ($spdisp == "S" or $spdisp == "X")
					{
						if ($Show_rrp == "Y")
						{
							//RRP
							$rrpval = mysql_result($srchresult,$i,"rrp");
							echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format($rrpval,2) . "</td>";
						}
						if ($Show_rrp4 == "Y")
						{
							//RRP4
							$rrp4val = mysql_result($srchresult,$i,"rrp4");
							echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format($rrp4val,2) . "</td>";
						}
						break;
					}
				}
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
					if ($num_enq > 0){
						$enquiry_only = mysql_result($enquiry_result,0,"Enquiry_Only");
					}
						  		
			  		if(mysql_result($customer_stop,0,"On_Stop_Flag") == 'Y') {
			  	  		echo '<span style="color:red;">On Stop</span>';
			 		}
			 		else if($enquiry_only == 'Y')
			 		{
						echo 'n/a';				 		
			 		} else {
  						echo '<a href=addtobasket.php?productid=' . $productid .'&qty=1&price=' . $buyprice . '>Buy</a></td></tr>';
					}
	  			}
	
	  			else
	  			{
	  				echo 'Call</td></tr>';
		  		}

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
	echo "<div id=Message_Div>Please Enter Search Criteria</div>";

?>
