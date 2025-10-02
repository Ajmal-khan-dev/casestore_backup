<?php
session_start();
session_destroy(); // destroys all session data
header("Location: ../pages/login.php"); // redirect to login page
exit();
?>
