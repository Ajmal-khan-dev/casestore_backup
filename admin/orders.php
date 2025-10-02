<?php
include '../config/db.php';

// Fetch all orders
$sql = "SELECT * FROM orders ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total (₹)</th>
            <th>Status</th>
            <th>Items</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['user_id']); ?></td>
                <td><?= number_format($row['total'], 2); ?></td>
                <td>
                    <form method="POST" action="update_order_status.php">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']); ?>">
                        <select name="status">
                            <option value="Pending"   <?= $row['status']=='Pending'   ? 'selected' : ''; ?>>Pending</option>
                            <option value="Shipped"   <?= $row['status']=='Shipped'   ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Delivered" <?= $row['status']=='Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?= $row['status']=='Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td>
                    <table border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price (₹)</th>
                            <th>Subtotal (₹)</th>
                        </tr>
                        <?php
                        // Fetch order items for this order
                        $stmt_items = $conn->prepare("
                            SELECT p.name AS product_name, oi.quantity, oi.price AS item_price
                            FROM order_items oi
                            JOIN products p ON oi.product_id = p.id
                            WHERE oi.order_id = ?
                        ");
                        $stmt_items->bind_param("i", $row['id']);
                        $stmt_items->execute();
                        $result_items = $stmt_items->get_result();

                        if ($result_items->num_rows > 0):
                            while ($item = $result_items->fetch_assoc()):
                                $subtotal = $item['item_price'] * $item['quantity'];
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                                    <td><?= $item['quantity']; ?></td>
                                    <td><?= number_format($item['item_price'], 2); ?></td>
                                    <td><?= number_format($subtotal, 2); ?></td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr><td colspan="4">No items found for this order.</td></tr>
                        <?php endif;

                        $stmt_items->close();
                        ?>
                    </table>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No orders found.</p>
<?php endif;

$conn->close();
?>

