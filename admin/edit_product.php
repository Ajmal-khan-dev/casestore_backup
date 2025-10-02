<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name  = $_POST['name'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc  = $_POST['description'];

    $image = $product['image']; // keep old image if no new one uploaded

    // ✅ Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../assets/image/";
        $fileName  = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $image = $fileName;
        }
    }

    $stmt = $conn->prepare("UPDATE products 
                        SET name=?, brand=?, model=?, price=?, stock=?, description=?, image=? 
                        WHERE id=?");
$stmt->bind_param("sssdissi", $name, $brand, $model, $price, $stock, $desc, $image, $id);

    $stmt->execute();

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
         <link rel="stylesheet" href="../assets/css/style.css">
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        h2 { margin-bottom: 15px; }
        .products { margin-top: 5px; font-size: 14px; color: #555; }
        .back { display: inline-block; margin-top: 15px; padding: 8px 15px; background: #333; color: #fff; text-decoration: none; border-radius: 5px; }
        .back:hover { background: #555; }
        form { max-width: 400px; margin: auto; }
        input, textarea, button { display: block; width: 100%; margin: 10px 0; padding: 8px; }
        img { max-width: 120px; margin: 10px 0; border-radius: 6px; }
    </style>
</head>
<body>
<h2>Edit Product</h2>

<form method="post" enctype="multipart/form-data">
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
    <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" placeholder="Brand" required>
    <input type="text" name="model" value="<?= htmlspecialchars($product['model']) ?>" placeholder="Model" required>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>

    <!-- ✅ Show current image -->
    <?php if (!empty($product['image'])): ?>
        <p>Current Image:</p>
        <img src="../assets/image/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
    <?php endif; ?>

    <!-- ✅ Upload new image -->
    <input type="file" name="image">

    <button type="submit">Update</button>
</form>

<a href="manage_products.php">⬅ Back</a>
</body>
</html>
