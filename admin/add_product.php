<?php
session_start();
include_once("../config/db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name  = $_POST['name'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc  = $_POST['description'];

    $image = "";

    // ✅ Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../assets/image/";
        $fileName  = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $image = $fileName;
        } else {
            $error = "❌ Failed to upload image!";
        }
    }

    if (!$error) {
       $stmt = $conn->prepare("INSERT INTO products (name, brand, model, price, stock, description, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdiss", $name, $brand, $model, $price, $stock, $desc, $image);

        if ($stmt->execute()) {
            header("Location: manage_products.php");
            exit();
        } else {
            $error = "❌ Failed to add product!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
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
        form { max-width: 400px; margin: auto; }
        input, textarea, button { display: block; width: 100%; margin: 10px 0; padding: 8px; }
    </style>
</head>
<body>
<h2>Add Product</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="text" name="brand" placeholder="Brand" required>
    <input type="text" name="model" placeholder="Model" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="number" name="stock" placeholder="Stock" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="file" name="image" required>
    <button type="submit">Add Product</button>
</form>

<a href="manage_products.php">⬅ Back</a>
</body>
</html>
