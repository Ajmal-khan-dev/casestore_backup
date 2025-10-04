<?php
include '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Phone Cases - CAS’E’STORE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f6faff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            text-align: center;
        }
        h2 {
            margin-bottom: 30px;
            font-size: 28px;
            color: #22304a;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-align: left;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 18px rgba(0,0,0,0.12);
        }
        .product-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            margin-bottom: 15px;
            border-radius: 8px;
            background: #f9fafb;
        }
        .product-card h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #111827;
        }
        .product-card p {
            margin: 5px 0;
            color: #374151;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s ease;
            text-align: center;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        form button.btn {
            border: none;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>
<a href="../index.php" class="back-btn">← Back</a>
<div class="container">
    <h2>Our Phone Cases</h2>
    <div class="product-grid">
        <?php
        // Fetch all products from DB
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="product-card">
                    <img src="../assets/image/<?php echo htmlspecialchars($row['image']); ?>" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>" 
                         class="product-image">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Brand:</strong> <?php echo htmlspecialchars($row['brand']); ?></p>
                    <p><strong>Model:</strong> <?php echo htmlspecialchars($row['model']); ?></p>
                    <p><strong>Price:</strong> ₹<?php echo number_format($row['price'], 2); ?></p>
                    <p><strong>Stock:</strong> 
                        <?php echo $row['stock'] > 0 ? "✅ In Stock" : "❌ Out of Stock"; ?>
                    </p>
                    
                    <a href="product.php?id=<?php echo $row['id']; ?>" class="btn">View</a>

                    <form action="../actions/add_to_cart.php" method="POST" style="margin-top:8px;">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<p>No products available.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>
