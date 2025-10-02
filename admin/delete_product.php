<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id=$id");
header("Location: manage_products.php");
exit();
?>
