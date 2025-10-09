<?php
// Start the session if it's not already started
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the homepage
header("location: ../index.php");
exit;
?>