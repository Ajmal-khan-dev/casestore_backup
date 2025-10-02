<?php
session_start();
include '../config/db.php';

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user orders (include total_amount column)
$sql = "SELECT id, fullname, email, address, city, zipcode, payment_method, total_amount, status, created_at
        FROM orders
        WHERE user_id = ?
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>My Orders</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <h3>
                        Order #<?php echo $row['id']; ?> - ₹
                        <?php 
                            $total = isset($row['total_amount']) ? (float)$row['total_amount'] : 0;
                            echo number_format($total, 2);
                        ?>
                    </h3>
                    <span class="status <?php echo strtolower($row['status']); ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>

                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // ✅ Fetch items for this order
                            $sql_items = "SELECT oi.quantity, p.name, p.price 
                                          FROM order_items oi
                                          JOIN products p ON oi.product_id = p.id
                                          WHERE oi.order_id = ?";
                            $stmt_items = $conn->prepare($sql_items);
                            $stmt_items->bind_param("i", $row['id']);
                            $stmt_items->execute();
                            $result_items = $stmt_items->get_result();

                            if ($result_items->num_rows > 0):
                                while ($item = $result_items->fetch_assoc()):
                                    $subtotal = $item['price'] * $item['quantity'];
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td>₹<?php echo number_format($subtotal, 2); ?></td>
                                    </tr>
                            <?php
                                endwhile;
                            else:
                                echo "<tr><td colspan='4'>No items found for this order.</td></tr>";
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
