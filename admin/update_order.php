<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Update Order</title></head>
 <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        h2 { margin-bottom: 15px; }
        .products { margin-top: 5px; font-size: 14px; color: #555; }
        .back { display: inline-block; margin-top: 15px; padding: 8px 15px; background: #333; color: #fff; text-decoration: none; border-radius: 5px; }
        .back:hover { background: #555; }
    </style>
<body>
<h2>Update Order Status</h2>
<form method="post">
    <select name="status">
        <option value="Pending" <?= $order['status']=="Pending"?"selected":"" ?>>Pending</option>
        <option value="Processing" <?= $order['status']=="Processing"?"selected":"" ?>>Processing</option>
        <option value="Shipped" <?= $order['status']=="Shipped"?"selected":"" ?>>Shipped</option>
        <option value="Delivered" <?= $order['status']=="Delivered"?"selected":"" ?>>Delivered</option>
        <option value="Cancelled" <?= $order['status']=="Cancelled"?"selected":"" ?>>Cancelled</option>
    </select><br>
    <button type="submit">Update</button>
</form>
</body>
</html>
