<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $status = $_POST['status'];
    $valid_status = ['Pending','Processing','Shipped','Delivered','Cancelled'];
    if(in_array($status, $valid_status)){
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Update Order</title></head>
<body>
<h2>Update Order Status</h2>
<form method="post">
    <select name="status">
        <?php foreach(['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $order['status']==$s?"selected":"" ?>><?= $s ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Update</button>
</form>
<a href="manage_orders.php">⬅ Back</a>
</body>
</html>
