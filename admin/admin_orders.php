<?php
session_start();
include '../config/db.php';

// ✅ Check if logged in and admin
if (!isset($_SESSION['user_id']) || $_SESSION['admin_id'] != 1) {
    header("Location: ../pages/login.php");
    exit();
}

// ✅ Fetch all orders
$query = "SELECT o.id, o.fullname, o.email, o.address, o.city, o.zipcode, 
                 o.payment_method, o.total_amount, o.created_at, o.status, u.email AS user_email
          FROM orders o
          JOIN users u ON o.user_id = u.id
          ORDER BY o.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Orders | Cas’E’Store</title>
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
</head>
<body>
    <h2>📦 All Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User (Email)</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Payment</th>
            <th>Total</th>
            <th>Status</th>
            <th>Placed On</th>
            <th>Products</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['user_email']) ?></td>
                <td>
                    <?= htmlspecialchars($order['fullname']) ?><br>
                    <small><?= htmlspecialchars($order['email']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($order['address']) ?>, 
                    <?= htmlspecialchars($order['city']) ?> - 
                    <?= htmlspecialchars($order['zipcode']) ?>
                </td>
                <td><?= strtoupper($order['payment_method']) ?></td>
                <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                <td><?= ucfirst($order['status']) ?></td>
                <td><?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></td>
                <td>
                    <?php
                    // ✅ Fetch products for this order
                    $items = $conn->prepare("SELECT p.name, oi.quantity 
                                              FROM order_items oi
                                              JOIN products p ON oi.product_id = p.id
                                              WHERE oi.order_id=?");
                    $items->bind_param("i", $order['id']);
                    $items->execute();
                    $res_items = $items->get_result();
                    if ($res_items->num_rows > 0) {
                        while ($item = $res_items->fetch_assoc()) {
                            echo "- " . htmlspecialchars($item['name']) . " (x" . $item['quantity'] . ")<br>";
                        }
                    } else {
                        echo "<em>No items</em>";
                    }
                    ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
</body>
</html>
