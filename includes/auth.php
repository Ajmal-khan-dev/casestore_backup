<?php
// auth.php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: fetch user details if needed
require_once "config.php"; // DB connection file

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If no user found (session invalid), force logout
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
