<?php
session_start(); // Start a new session or resume the existing one
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: login.php"); // Redirect to the login page
exit();
?>