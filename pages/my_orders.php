<?php
session_start();
include '../config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $conn->prepare("
    SELECT id, total_amount, status, created_at 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        .order { background: #fff; padding: 15px; margin-bottom: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .order-header { display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .order-header h3 { margin: 0; }
        .order-status { font-weight: bold; padding: 5px 10px; border-radius: 4px; color: #fff; }
        .status-Pending { background: orange; }
        .status-Shipped { background: blue; }
        .status-Delivered { background: green; }
        .status-Cancelled { background: red; }
        .order-items { display: none; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007BFF; color: #fff; }
        .toggle-btn { background: none; border: none; font-size: 18px; cursor: pointer; }
    </style>
</head>
<body>

<h2>My Orders</h2>
<!-- Continue Shopping Button -->
<a href="../index.php" style="
    display: inline-block;
    padding: 10px 16px;
    background: #007BFF;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    margin-bottom: 20px;
">
    ⬅ Continue Shopping
</a>

<?php if ($orders_result->num_rows > 0): ?>
    <?php while ($order = $orders_result->fetch_assoc()): ?>
        <div class="order">
            <div class="order-header" onclick="toggleItems('items-<?php echo $order['id']; ?>')">
                <h3>Order #<?php echo $order['id']; ?> - ₹<?php echo number_format($order['total_amount'], 2); ?></h3>
                <span class="order-status status-<?php echo $order['status']; ?>">
                    <?php echo $order['status']; ?>
                </span>
                <button class="toggle-btn">&#9660;</button>
            </div>

            <div class="order-items" id="items-<?php echo $order['id']; ?>">
                <?php
                $order_id = $order['id'];

                $stmt_items = $conn->prepare("
                    SELECT p.name AS product_name, oi.quantity, oi.price 
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $items_result = $stmt_items->get_result();
                ?>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price (₹)</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                    <?php if ($items_result->num_rows > 0): ?>
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                            <?php $subtotal = $item['price'] * $item['quantity']; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                <td>₹<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No items found for this order.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>You have no orders yet.</p>
<?php endif; ?>

<script>
function toggleItems(id){
    const el = document.getElementById(id);
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
}
</script>
</body>
</html>