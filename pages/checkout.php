<?php
session_start();
require_once "../config/db.php";

// Redirect if not logged in or admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items (using cart_items table)
$query = "SELECT c.id AS cart_id, p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['subtotal'];
}
$stmt->close();

// Redirect if cart is empty
if ($total <= 0) {
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - CAS’E’STORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .checkout-container { width: 80%; margin: 40px auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        h2 { margin-bottom: 15px; }
        form label { display: block; margin: 10px 0 5px; }
        form input, form select, form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .order-summary { border-left: 2px solid #eee; padding-left: 20px; }
        .place-order-btn { margin-top: 20px; display: inline-block; padding: 12px 20px; background: #007BFF; color: white; border-radius: 6px; border: none; cursor: pointer; }
        .place-order-btn:hover { background: #0056b3; }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align:center; }
        .logout-btn { padding:8px 12px; background:#6c757d; color:white; text-decoration:none; border-radius:6px; float:right; }
    </style>
</head>
<body>
    <div class="checkout-container">
        <!-- Billing Details -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2>Billing Details</h2>
                <a href="../pages/logout.php" class="logout-btn">Logout</a>
            </div>
            <form action="place_order.php" method="POST">
                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" required>

                <label for="email">Email</label>
                <input type="email" name="email" required>

                <label for="address">Address</label>
                <textarea name="address" required></textarea>

                <label for="city">City</label>
                <input type="text" name="city" required>

                <label for="zipcode">Zip Code</label>
                <input type="text" name="zipcode" required>

                <label for="payment">Payment Method</label>
                <select name="payment" required>
                    <option value="cod">Cash on Delivery</option>
                    <option value="card">Credit/Debit Card</option>
                    <option value="upi">UPI</option>
                </select>

                <input type="hidden" name="total_amount" value="<?= $total ?>">

                <button type="submit" class="place-order-btn">Place Order</button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2"><strong>Total:</strong></td>
                    <td><strong>₹<?= number_format($total, 2) ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
    <?php include "../includes/footer.php"; ?>
</body>
</html>
