<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect back to main.html in /public
header("Location: ../public/index.php");
exit();
?>