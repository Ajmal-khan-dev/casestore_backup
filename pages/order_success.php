<?php
session_start();
require_once "../config/db.php";

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}
$order_id = intval($_GET['id']);

// ✅ Fetch order details
$order_sql = "SELECT o.id, o.total_amount, o.payment_method, o.created_at 
              FROM orders o 
              WHERE o.id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ✅ Fetch ordered items
$items_sql = "SELECT oi.quantity, oi.price, p.name 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success - CAS’E’STORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .order-box {
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            background: #f9f9f9;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background: #f0f0f0;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="order-box">
        <h2>✅ Thank you! Your order has been placed.</h2>
        <p>Order ID: <strong>#<?= $order['id'] ?></strong></p>
        <p>Total: <strong>₹<?= number_format($order['total_amount'], 2) ?></strong></p>
        <p>Payment Method: <strong><?= $order['payment_method'] ?? 'N/A' ?></strong></p>
        <p>Date: <strong><?= $order['created_at'] ?></strong></p>

        <h3>🛍️ Ordered Items</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price (₹)</th>
                <th>Subtotal (₹)</th>
            </tr>
            <?php while($item = $items_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="../index.php" class="btn">Continue Shopping</a>
    </div>
</body>
</html>
