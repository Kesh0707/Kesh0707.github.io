<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: myaccount.php"); // Redirect to login page, or choose index.php
exit();
?>
