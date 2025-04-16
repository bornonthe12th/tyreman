<?php

$companyid = $_SESSION['companyid'];
//get session id

//call search proc
//set up query

$query="call ShowBasket($cust,'$session');";	
//run query
$srchresult=mysql_query($query);
$num=mysql_numrows($srchresult);
$total = 0;
//loop round results
if ($num>0) {
	//products found
	$i=0;
  	//write table header
  	echo "<table id=BasketTable align=center>";
	echo "<tr><td CLASS=maintitle>Basket Contents</td></tr> ";
	echo "<tr><td>";
	echo "<table align=center>";
	echo "<tr><th width=190 class=titlemedium>Stock Code</th>";
	echo "<th class=titlemedium>Description</th>";
	echo "<th width=100 class=titlemedium>Quantity Available</th>";
	echo "<th width=100 class=titlemedium>Quantity Required</th>";
	echo "<th width=100 class=titlemedium>Cost Price</th>";
	echo "<th width=100 class=titlemedium>Line Total</th>";
	echo "<tr>";	
	while ($i < $num) {
		//write one row of table
			if ($i/2 == round($i/2)) {
				$tdclass = 'even';
			} else {
				$tdclass = 'odd';
			}
		//write one row of table
        	switch ($companyid)

                {
		default:
                echo "<tr><td class=$tdclass >";
                echo mysql_result($srchresult,$i,"stockcode");
                echo "</td><td class=$tdclass>";
                echo mysql_result($srchresult,$i,"description");
                echo "</td><td align=right class=$tdclass>";
                echo mysql_result($srchresult,$i,"stocklevel"). "&nbsp";
                echo "</td><td  align=center class=$tdclass><input type=text size=5 maxlength=6 name=qty" . $i . " value=";
                echo mysql_result($srchresult,$i,"qty");
                echo "></td><td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"price"),2);
                $total = $total + (mysql_result($srchresult,$i,"qty") * mysql_result($srchresult,$i,"price"));
                echo "&nbsp;</td><td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"linettl"),2);
                echo "&nbsp;</td><td align=right class=$tdclass>";
                $productid = mysql_result($srchresult,$i,"product_id");
                echo "<input type=hidden name=prodid" . $i . " value=$productid></input></tr>";

                $i++;
		break;

		case 1:
		echo "<tr><td class=$tdclass >";
  		echo mysql_result($srchresult,$i,"stockcode");
  		echo "</td><td class=$tdclass>";
  		echo mysql_result($srchresult,$i,"description");
  		echo "</td><td align=right class=$tdclass>";
		$brstk=mysql_result($srchresult,$i,"stocklevel");
  		echo "$brstk&nbsp</td>";

		$ordqty=mysql_result($srchresult,$i,"qty");
  		echo "<td  align=right  class=$tdclass><select name=qty" . $i . " width='50' STYLE='width: 50px' value="; 
		 for ($work=-1; $work<=$brstk; $work++) {
		     if ($work==$ordqty) {
			echo "<option value=\"$work\" SELECTED>$work</option>";
			   		 } else {
			echo "<option value=\"$work\">$work</option>"; 
						} 
							} 
  		echo "></select></td>"; 

  		echo "<td align=right class=$tdclass>";
  		echo number_format(mysql_result($srchresult,$i,"price"),2);
  		$total = $total + (mysql_result($srchresult,$i,"qty") * mysql_result($srchresult,$i,"price"));
  		echo "&nbsp;</td><td align=right class=$tdclass>";
  		echo number_format(mysql_result($srchresult,$i,"linettl"),2);
  		echo "&nbsp;</td><td align=right class=$tdclass>";
		$productid = mysql_result($srchresult,$i,"product_id");
		echo "<input type=hidden name=prodid" . $i . " value=$productid></input></tr>";
		
	  	$i++;
		break;

                case 3:
                echo "<tr><td class=$tdclass >";
                echo mysql_result($srchresult,$i,"stockcode");
                echo "</td><td class=$tdclass>";
                echo mysql_result($srchresult,$i,"description");
                echo "</td><td align=right class=$tdclass>";
                $brstk=mysql_result($srchresult,$i,"stocklevel");
                echo "$brstk&nbsp</td>";

                $ordqty=mysql_result($srchresult,$i,"qty");
                echo "<td  align=right  class=$tdclass><select name=qty" . $i . " width='50' STYLE='width: 50px' value=";
                 for ($work=-1; $work<=$brstk; $work++) {
                     if ($work==$ordqty) {
                        echo "<option value=\"$work\" SELECTED>$work</option>";
                                         } else {
                        echo "<option value=\"$work\">$work</option>";
                                                }
                                                        }
                echo "></select></td>";

                echo "<td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"price"),2);
                $total = $total + (mysql_result($srchresult,$i,"qty") * mysql_result($srchresult,$i,"price"));
                echo "&nbsp;</td><td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"linettl"),2);
                echo "&nbsp;</td><td align=right class=$tdclass>";
                $productid = mysql_result($srchresult,$i,"product_id");
                echo "<input type=hidden name=prodid" . $i . " value=$productid></input></tr>";

                $i++;
                break;

                case 15:
                echo "<tr><td class=$tdclass >";
                echo mysql_result($srchresult,$i,"stockcode");
                echo "</td><td class=$tdclass>";
                echo mysql_result($srchresult,$i,"description");
                echo "</td><td align=right class=$tdclass>";
                $brstk=mysql_result($srchresult,$i,"stocklevel");
                echo "$brstk&nbsp</td>";

                $ordqty=mysql_result($srchresult,$i,"qty");
                echo "<td  align=right  class=$tdclass><select name=qty" . $i . " width='50' STYLE='width: 50px' value=";
                 for ($work=-1; $work<=$brstk; $work++) {
                     if ($work==$ordqty) {
                        echo "<option value=\"$work\" SELECTED>$work</option>";
                                         } else {
                        echo "<option value=\"$work\">$work</option>";
                                                }
                                                        }
                echo "></select></td>";

                echo "<td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"price"),2);
                $total = $total + (mysql_result($srchresult,$i,"qty") * mysql_result($srchresult,$i,"price"));
                echo "&nbsp;</td><td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"linettl"),2);
                echo "&nbsp;</td><td align=right class=$tdclass>";
                $productid = mysql_result($srchresult,$i,"product_id");
                echo "<input type=hidden name=prodid" . $i . " value=$productid></input></tr>";

                $i++;
                break;

                case 16:
                echo "<tr><td class=$tdclass >";
                echo mysql_result($srchresult,$i,"stockcode");
                echo "</td><td class=$tdclass>";
                echo mysql_result($srchresult,$i,"description");
                echo "</td><td align=right class=$tdclass>";
                $brstk=mysql_result($srchresult,$i,"stocklevel");
                echo "$brstk&nbsp</td>";

                $ordqty=mysql_result($srchresult,$i,"qty");
                echo "<td  align=right  class=$tdclass><select name=qty" . $i . " width='50' STYLE='width: 50px' value=";
                 for ($work=-1; $work<=$brstk; $work++) {
                     if ($work==$ordqty) {
                        echo "<option value=\"$work\" SELECTED>$work</option>";
                                         } else {
                        echo "<option value=\"$work\">$work</option>";
                                                }
                                                        }
                echo "></select></td>";

                echo "<td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"price"),2);
                $total = $total + (mysql_result($srchresult,$i,"qty") * mysql_result($srchresult,$i,"price"));
                echo "&nbsp;</td><td align=right class=$tdclass>";
                echo number_format(mysql_result($srchresult,$i,"linettl"),2);
                echo "&nbsp;</td><td align=right class=$tdclass>";
                $productid = mysql_result($srchresult,$i,"product_id");
                echo "<input type=hidden name=prodid" . $i . " value=$productid></input></tr>";

                $i++;
                break;

		}
	}  
	echo "<tr><td colspan=4></td><td align=right>Total&nbsp</td><td align=right><b>" . number_format($total,2,'.',',') . "&nbsp;</b></td></tr>";
	echo "<input type=hidden name=linecount value=$i></input>";		
	echo "</table>";
	echo "</td></tr>";
	echo "</table>";
	echo "<br><div id=Message_Div align=center><table id=BlueTable><tr><td class=titlemedium>To delete an item from your basket change the quantity to 0</td></tr>";
	echo "<br><div id=Message_Div align=center><tr><td class=titlemedium>and press the 'Update Basket' button.";
	echo " </td></tr></table></div>";
} else {
	echo "<br><div id=Message_Div align=center><table id=BlueTable>";
	echo "<tr><td class=titlemedium>Your basket is currently empty..</td></tr>";
	echo "</table></div>";
}

?> 
