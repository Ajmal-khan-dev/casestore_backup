<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] == 1) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $zipcode = trim($_POST['zipcode']);
    $payment_method = trim($_POST['payment']); // ✅ fixed name
    $total_amount = $_POST['total_amount'];

    // ✅ Insert into orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, email, address, city, zipcode, payment_method, total_amount, status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("issssssd", $user_id, $fullname, $email, $address, $city, $zipcode, $payment_method, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // ✅ Fetch cart items (use `cart` since checkout.php uses `cart`)
    $cart_query = "SELECT c.product_id, c.quantity, p.price
                   FROM cart c
                   JOIN products p ON c.product_id = p.id
                   WHERE c.user_id = ?";
    $cart_stmt = $conn->prepare($cart_query);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    // ✅ Insert into order_items
    $oi_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, created_at) VALUES (?, ?, ?, ?, NOW())");
    while ($item = $cart_result->fetch_assoc()) {
        $oi_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $oi_stmt->execute();
    }

    // ✅ Clear cart after placing order
    $del_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $del_stmt->bind_param("i", $user_id);
    $del_stmt->execute();

    header("Location: my_orders.php?success=1");
    exit();
}
