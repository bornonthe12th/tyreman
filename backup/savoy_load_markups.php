<?php



// String EnCrypt + DeCrypt function
// Not totally secure encyrption, but enough for our purposes.

function convert($text, $key = '') {
    // return text unaltered if the key is blank
    if ($key == '') {
        return $text;
    }

    // remove the spaces in the key
    $key = str_replace(' ', '', $key);
    if (strlen($key) < 8) {
        exit('key error');
    }
    // set key length to be no more than 32 characters
    $key_len = strlen($key);
    if ($key_len > 32) {
        $key_len = 32;
    }

    // A wee bit of tidying in case the key was too long
    $key = substr($key, 0, $key_len);

    // We use this a couple of times or so
    $text_len = strlen($text);

    // fill key with the bitwise AND of the ith key character and 0x1F, padded to length of text.
    $lomask = str_repeat("\x1f", $text_len); // Probably better than str_pad
    $himask = str_repeat("\xe0", $text_len);
    $k = str_pad("", $text_len, $key); // this one _does_ need to be str_pad

    // {en|de}cryption algorithm
    $text = (($text ^ $k) & $lomask) | ($text & $himask);

    return $text;
}




	$account_number = $_SESSION['savoy_account_number'];
	
	
$n="http://www.savoytyres.co.uk/savoy/b2b/b2b_xfr.php";



$n.="?x=".md5($account_number);

$buffer=file_get_contents($n);

 //echo "<br /><br />Encrypted response below<br />$buffer";

$buffer=convert($buffer,"jkhgrkjbbsfrgb");



//echo "<br /><br />Decrypted response below<br />$buffer";
//echo "<br /><br /><br />";
//die();


if(strlen($buffer) > 5)
{
	$a=explode("^",$buffer); // Load up array a$[] with all the lines from the $buffer

	foreach($a as $b)
	if(strlen($b) > 4) // Ensure not a null length line e.g. from the end of the file
	{
		list($key,$value)=explode("|",$b);
			
		$value/=812;
			
		$_SESSION['savoy_db_'.$key]=$value;
	
	}
}




?>