<?php
// logout.php
// simple page to log the user out

session_start();
session_unset(); // clear session vars
session_destroy(); // destroy the session

// send back to home page
header("Location: index.php");
exit;
?>
