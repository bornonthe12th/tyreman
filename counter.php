#!/usr/bin/php
<?php

$counter="1";

while($counter < 10){
    echo "plus one to $counter\r";
	sleep (1);
    $counter++;
}

echo("Counter is $counter!\n");  // Outputs: Counter is 10!

?>
