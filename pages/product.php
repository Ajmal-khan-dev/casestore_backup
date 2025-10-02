<?php
session_start();
include '../config/db.php'; // Database connection

// Check if product ID exists in URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // safer than raw GET

    $sql = "SELECT * FROM products WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("<h2>Product not found.</h2>");
    }
} else {
    die("<h2>Invalid Product ID.</h2>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['name']); ?> - Cas’e’store</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container">
    <div class="product-detail">
      <div class="product-image">
        <img src="../assets/image/<?php echo htmlspecialchars($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>">
      </div>
      
      <div class="product-info">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
        <p><strong>Model:</strong> <?php echo htmlspecialchars($product['model']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <p><strong>Price:</strong> ₹<?php echo number_format($product['price'], 2); ?></p>
        <p><strong>Stock:</strong> 
          <?php echo $product['stock'] > 0 ? "In Stock" : "Out of Stock"; ?>
        </p>

        <!-- ✅ AJAX Add to Cart -->
        <?php if ($product['stock'] > 0): ?>
          <button type="button" class="btn" onclick="addToCart(<?php echo $product['id']; ?>)">
            Add to Cart
          </button>
        <?php else: ?>
          <button class="btn" disabled>Out of Stock</button>
        <?php endif; ?>
        
        <!-- Back to Products -->
        <p style="margin-top:20px;">
          <a href="products.php" class="btn">← Back to Products</a>
        </p>
      </div>
    </div>

    <!-- ================= Related Products Section ================= -->
    <div class="related-products">
      <h3>Related Products</h3>
      <div class="product-grid">
        <?php
        $brand = $conn->real_escape_string($product['brand']);
        $related_sql = "
            SELECT * FROM products 
            WHERE brand = '$brand' AND id != $id 
            ORDER BY RAND() 
            LIMIT 4
        ";
        $related_result = $conn->query($related_sql);

        if ($related_result && $related_result->num_rows > 0) {
            while ($rel = $related_result->fetch_assoc()) {
                ?>
                <div class="product-card">
                    <img src="../assets/image/<?php echo htmlspecialchars($rel['image']); ?>" 
                         alt="<?php echo htmlspecialchars($rel['name']); ?>">
                    <h4><?php echo htmlspecialchars($rel['name']); ?></h4>
                    <p><strong>₹<?php echo number_format($rel['price'], 2); ?></strong></p>
                    <a href="product.php?id=<?php echo $rel['id']; ?>" class="btn">View</a>
                </div>
                <?php
            }
        } else {
            echo "<p>No related products found.</p>";
        }
        ?>
      </div>
    </div>
  </div>

  <!-- ✅ Add JS -->
  <script>
  function addToCart(productId) {
      fetch("../actions/add_to_cart.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "product_id=" + productId
      })
      .then(res => res.json())
      .then(data => {
          if (data.status === "success") {
              if (document.getElementById("cartCount")) {
                  document.getElementById("cartCount").textContent = data.cart_count;
              }
              alert("✅ Item added to cart!");
          } else if (data.message === "login_required") {
              window.location.href = "login.php";
          }
      })
      .catch(err => console.error(err));
  }
  </script>
</body>
</html>
