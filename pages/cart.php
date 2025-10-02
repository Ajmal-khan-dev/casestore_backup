<?php
session_start();
require_once "../config/db.php";

// ✅ Redirect if not logged in OR if admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch cart items
$query = "SELECT c.id AS cart_id, p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart - CAS’E’STORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .cart-container {
            width: 80%;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        td a {
            color: red;
            text-decoration: none;
        }
        .qty-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }
        .qty-btn {
            padding: 4px 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .qty-btn:hover {
            background: #0056b3;
        }
        .checkout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .checkout-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2>🛒 My Shopping Cart</h2>
            <a href="../actions/logout.php" style="padding:8px 12px; background:#6c757d; color:white; text-decoration:none; border-radius:6px;">Logout</a>
        </div>

        <table>
            <tr>
                <th>Product</th>
                <th>Price (₹)</th>
                <th>Qty</th>
                <th>Subtotal (₹)</th>
                <th>Action</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= number_format($row['price'], 2) ?></td>
                        <td>
                            <div class="qty-controls">
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="qty-btn">-</button>
                                </form>
                                <span><?= $row['quantity'] ?></span>
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="qty-btn">+</button>
                                </form>
                            </div>
                        </td>
                        <td><?= number_format($row['subtotal'], 2) ?></td>
                        <td>
                            <a href="../actions/remove_from_cart.php?id=<?= $row['cart_id'] ?>">❌ Remove</a>
                        </td>
                    </tr>
                    <?php $total += $row['subtotal']; ?>
                <?php endwhile; ?>

                <tr>
                    <td colspan="3" align="right"><strong>Total:</strong></td>
                    <td colspan="2">₹<?= number_format($total, 2) ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="5">Your cart is empty</td>
                </tr>
            <?php endif; ?>
        </table>

        <?php if ($total > 0): ?>
            <div class="cart-actions" style="margin-top:20px; text-align:center;">
                <a href="../index.php" class="checkout-btn" style="background:##007BFF;">← Continue Shopping</a>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a>
            </div>
        <?php else: ?>
            <div class="cart-actions" style="margin-top:20px; text-align:center;">
                <a href="../index.php" class="checkout-btn" style="background:#007BFF;">← Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>
