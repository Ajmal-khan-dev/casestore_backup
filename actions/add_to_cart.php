<?php
session_start();
require_once "../config/db.php";

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "login_required"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = 1;

// Check if product already exists in cart
$check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update quantity
    $row = $result->fetch_assoc();
    $newQty = $row['quantity'] + 1;
    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $newQty, $row['id']);
    $update->execute();
} else {
    // Insert new product
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
}

// Get updated cart count
$countQ = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
$countQ->bind_param("i", $user_id);
$countQ->execute();
$countRes = $countQ->get_result()->fetch_assoc();
$total = $countRes['total'] ?? 0;

echo json_encode(["status" => "success", "cart_count" => $total]);
exit();
?>
