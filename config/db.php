<?php
$host = "localhost";
$user = "root";     // default WAMP user
$pass = "";         // default WAMP has no password
$dbname = "casestore";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
