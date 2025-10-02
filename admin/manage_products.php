<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
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
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #f4f4f4; }
        img { max-width: 60px; max-height: 60px; border-radius: 5px; }
        .actions a { margin: 0 5px; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Manage Products</h2>
    <a href="dashboard.php">⬅ Back to Dashboard</a> | 
    <a href="add_product.php">➕ Add Product</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <img src="../assets/image/<?= htmlspecialchars($row['image']) ?>" alt="Product">
                <?php else: ?>
                    <span style="color:#888;">No Image</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['brand']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td>₹<?= number_format($row['price'],2) ?></td>
            <td><?= $row['stock'] ?></td>
            <td class="actions">
                <a href="edit_product.php?id=<?= $row['id'] ?>">✏ Edit</a> | 
                <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
