<?php



	  	if($speed_rating == 's' || $speed_rating == 't') $speed_rating_group = 'st';
	  	else if($speed_rating == 'h' || $speed_rating == 'v') $speed_rating_group = 'hv';
	  	else if($speed_rating == 'w' || $speed_rating == 'y' || $speed_rating == 'z') $speed_rating_group = 'wyz';
	  	else $speed_rating_group = 'default';

	  	
		// Now get the brand name and format it so that the mark up array key can be constructed

		  $brand_name = mysql_result($srchresult,$i,"manufacturer");

		  $brand_name = str_replace(" ","",$brand_name);		// Remove spaces
		  $brand_name = str_replace("-","",$brand_name);		// Remove hyphens 
		  $brand_name = strtolower($brand_name);

		include 'Reconnect.php';

		 		if($_SESSION['selected_branch'] == $_SESSION['default_branch']) { 
			$savoy_cost_price = mysql_result($srchresult,$i,"costprice");
			//echo 'cost = '.$savoy_cost_price.'<br />';
			//else if(mysql_result($srchresult,$i,"stockcode") == mysql_result($srchresult2,$i,"stockcode")) // Check price is for same product (no reason why it shouldn't be, but worth checking)
			//$savoy_cost_price = mysql_result($srchresult2,$i,"costprice");
		}
		else
		{
			//die("An error has occurred, can't load prices for default branch");
			$query = "SELECT costprice FROM prices WHERE stockcode = '" . mysql_result($srchresult,$i,"stockcode") . "' AND customer_id = $cust";
            $srchresult2 = mysql_query($query) or die(mysql_error());
            
			$num2 = mysql_num_rows($srchresult2);

			if ($num2 > 0){
			  $savoy_cost_price = mysql_result($srchresult2,"costprice");
		 	} else {
			  $savoy_cost_price = 0;
			}
		}
		  // Now we have $speed_rating_group $brand_name and $brand_name
		  // So we can find out the markups, starting with Savoy's markup to their customer

			$array_key = "savoy_db_" . $brand_name . '_' . $speed_rating_group . '_gm_savoy';

			if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
				$savoy_markup_to_cust = $_SESSION[$array_key];

			else // there is not a markup set for this brand and speed rating ,  so try the default for brand (i.e. no speed rating group)
			{
				$array_key = "savoy_db_" . $brand_name . '_' . 'default' . '_gm_savoy';

				if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
					$savoy_markup_to_cust = $_SESSION[$array_key];

				else // There is not a markup for the default speed rating ,  so try default brand with normal speed rating
				{
					$array_key = "savoy_db_" . 'default' . '_' . $speed_rating_group . '_gm_savoy';
					

					if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
						$savoy_markup_to_cust = $_SESSION[$array_key];

					else // Need to use default brand with default speed rating
					{
						$array_key = "savoy_db_" . 'default' . '_' . 'default' . '_gm_savoy';

						if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
							$savoy_markup_to_cust = $_SESSION[$array_key];

						else // Can't determine what the markup should be from Savoy's database
							$savoy_markup_to_cust = 0;
					}
				}
			}

			// Okay, so now we should have the markup from Savoy to their customer
			// so we need to calculate the price

			if(isset($savoy_markup_to_cust) && ($savoy_markup_to_cust > .01) && ($savoy_cost_price > 0)) {
				$savoy_sell_price = $savoy_cost_price / (1 - ($savoy_markup_to_cust / 100));
		} else {
			  $savoy_sell_price = 99999;     // Default selling price if price can't be properly determined.
		  }

			// Now set Savoy customer's selling price

			$array_key = "savoy_db_" . $brand_name . '_' . $speed_rating_group . '_gm_customer';

			if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
				$customer_markup = $_SESSION[$array_key];

			else // there is not a markup set for this brand and speed rating ,  so try the default for brand (i.e. no speed rating group)
			{
				$array_key = "savoy_db_" . $brand_name . '_' . 'default' . '_gm_customer';

				if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
					$customer_markup = $_SESSION[$array_key];

				else // There is not a markup for the default speed rating ,  so try default brand with normal speed rating
				{
					$array_key = "savoy_db_" . 'default' . '_' . $speed_rating_group . '_gm_customer';


					if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
						$customer_markup = $_SESSION[$array_key];

					else // Need to use default brand with default speed rating
					{
						$array_key = "savoy_db_" . 'default' . '_' . 'default' . '_gm_customer';

						if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
							$customer_markup = $_SESSION[$array_key];

						else // Can't determine what the markup should be from Savoy's database
							$customer_markup = 0;
					}
				}
			}


			// Now we should have the customer's markup
			// so we need to calculate the customer's selling price 
			

			if(isset($customer_markup) && ($customer_markup > .01))
				$customer_sell_price = $savoy_sell_price / (1 - ($customer_markup / 100));
			else
			  $customer_sell_price = 0;


			// Finally, find out how much Savoy's customer charges for fitting
			
			$array_key = "savoy_db_" . $brand_name . '_fitted_rate';

			if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
				$customer_fitting_charge = $_SESSION[$array_key];

			else
			{
				$array_key = "savoy_db_" . 'default' . '_fitted_rate';

				if(isset($_SESSION[$array_key]) && ($_SESSION[$array_key] > .01))
					$customer_fitting_charge = $_SESSION[$array_key];
				else
				  $customer_fitting_charge = 0;
			}




			// Now we can display the prices and hyperlink to buy the product


	  		if ($spdisp =='B' || $spdisp =='X') // B = Cost      X = Both cost and sell       i.e. if cost price is required
	  		{
		  		echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format($savoy_sell_price,2) . "</td>";
	  		}


		  	if ($spdisp =='S' || $spdisp =='X') // S = Sell price     X = Both      i.e. if selling price is required
		  	{
					if ($vatflag == 'Y')
					{
						$customer_sell_price *= $vat;
            $customer_fitting_charge *= $vat;
					}

		  		echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format($customer_sell_price , 2) . "</td>";
		  		echo "<td style=\"padding:0px 10px; text-align:right;\">" . number_format(($customer_sell_price + $customer_fitting_charge) , 2) . "</td>";
				}


	  		echo "<td style=\"padding:0px 10px; text-align:center;\">";
	  		if (($savoy_sell_price > 0) && ($savoy_sell_price > $savoy_cost_price) && ($savoy_sell_price < 99999))
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
	  				echo '<a href=addtobasket.php?productid=' . $productid .'&qty=1&price=' . round($savoy_sell_price , 2) . '>Buy</a></td></tr>';
  				}
  			}

  			else
  			{
  				echo 'Phone</td></tr>';
	  		}



?>