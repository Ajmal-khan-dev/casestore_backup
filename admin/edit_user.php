<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit User</title></head>
 <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        h2 { margin-bottom: 15px; }
        .products { margin-top: 5px; font-size: 14px; color: #555; }
        .back { display: inline-block; margin-top: 15px; padding: 8px 15px; background: #333; color: #fff; text-decoration: none; border-radius: 5px; }
        .back:hover { background: #555; }
    </style>
<body>
<h2>Edit User</h2>
<form method="post">
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    <select name="role">
        <option value="customer" <?= $user['role']=="customer"?"selected":"" ?>>Customer</option>
        <option value="admin" <?= $user['role']=="admin"?"selected":"" ?>>Admin</option>
    </select><br>
    <button type="submit">Update</button>
</form>
<a href="manage_users.php">⬅ Back</a>
</body>
</html>
