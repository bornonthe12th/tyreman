<?php
	//empty basket
	include 'B2BEmptyBasket.php';
	//unset session vars
	session_unset();
	//destroy session
	session_destroy();
	//goto login page
	

	
// Steve Cordingley 12th March 2008	
// The above does not seem to clear the session variables down properly, possibly because the cookie has not been cleared
// see www.php.net for session_unset(), session_destroy - read all the page for each function
// Particularly see http://uk2.php.net/manual/en/function.session-destroy.php with the example shown to destroy the session (used below)
	
if(isset($_SESSION['savoy']))		
{
	// Unset all of the session variables.
	$_SESSION = array();
	
	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (isset($_COOKIE[session_name()])) {
	    setcookie(session_name(), '', time()-42000, '/');
	}
	
	// Finally, destroy the session.
	session_destroy();	
	
}	
	
	
	
	
  	$URL="B2BLogin.php";
	header ("Location: $URL");
?>