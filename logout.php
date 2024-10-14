<?php
session_start();
session_unset();  // Unset all of the session variables
session_destroy();  // Destroy the session

// Redirect the user back to the login page after logging out
header("Location: login.php");
exit;
