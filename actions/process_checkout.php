<?php
session_start();
require_once "../config/db.php";

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;

// ✅ Fetch cart items
$sql = "SELECT product_id, quantity, price FROM cart_items WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$stmt->close();

// ✅ If cart is empty, stop checkout
if (empty($cart_items)) {
    echo "<h2>Checkout</h2><p>Your cart is empty.</p>";
    exit();
}

// ✅ Insert order into `orders`
$order_sql = "INSERT INTO orders (user_id, total_amount, created_at, status) 
              VALUES (?, ?, NOW(), 'Pending')";
$order_stmt = $conn->prepare($order_sql);
if (!$order_stmt) {
    die("Prepare failed: " . $conn->error);
}
$order_stmt->bind_param("id", $user_id, $total);
$order_stmt->execute();
$order_id = $order_stmt->insert_id; // last inserted order ID
$order_stmt->close();

// ✅ Insert each cart item into `order_items`
$oi_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
           VALUES (?, ?, ?, ?)";
$oi_stmt = $conn->prepare($oi_sql);
if (!$oi_stmt) {
    die("Prepare failed: " . $conn->error);
}

foreach ($cart_items as $item) {
    $oi_stmt->bind_param(
        "iiid",
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['price']
    );
    $oi_stmt->execute();
}
$oi_stmt->close();

// ✅ Clear cart after placing the order
$delete_stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
$delete_stmt->bind_param("i", $user_id);
$delete_stmt->execute();
$delete_stmt->close();

// ✅ Redirect to success page
header("Location: ../pages/order_success.php?id=" . $order_id);
exit();
?>
