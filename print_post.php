<?php 

  $myarray = array( $_POST);
  foreach ($myarray as $key => $value)
  {
    echo "<p>".$key."</p>";
    echo "<p>".$value."</p>";
    echo "<hr />";
  }

?>
