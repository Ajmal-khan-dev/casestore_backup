<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Casestore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-image: url('../assets/image/background.jpg'); /* Replace with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <h1>Welcome to Admin Dashboard</h1>
    <nav>
        <a href="manage_products.php">Manage Products</a> |
        <a href="manage_orders.php">Manage Orders</a> |
        <a href="manage_users.php">Manage Users</a> |
        <a href="logout.php">Logout</a>
    </nav>
</body>
</html>
