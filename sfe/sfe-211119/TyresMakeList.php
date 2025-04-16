<?php
$query="call GetTyreMakes();";

//run query
$list=mysql_query($query);
//loop round results
?>
<?

while($row_list=mysql_fetch_assoc($list)){
                ?>
                    <option value="<? echo $row_list['manufacturer']; ?>"<? if($row_list['manufacturer']==$mktype){ echo "selected"; } ?>>
                                         <?echo $row_list['manufacturer'];?>
                    </option>
                <?
                }
                ?>
<?php
$_SESSION['mktype']=$mktype
?>
