<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head><title>Manage Users</title></head>
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
<h2>Manage Users</h2>
<a href="dashboard.php">⬅ Back to Dashboard</a>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= $row['role'] ?></td>
    <td>
        <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a> | 
        <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
