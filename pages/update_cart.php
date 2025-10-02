<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['cart_id'], $_POST['action'])) {
    $cart_id = intval($_POST['cart_id']);
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    // Fetch current quantity
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if ($item) {
        $qty = $item['quantity'];

        if ($action === "increase") {
            $qty++;
        } elseif ($action === "decrease" && $qty > 1) {
            $qty--;
        }

        // Update quantity
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("iii", $qty, $cart_id, $user_id);
        $update->execute();
        $update->close();
    }
}

header("Location: cart.php");
exit();
