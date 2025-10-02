<?php
session_start();
include '../config/db.php';

if (!isset($_GET['model'])) {
    header("Location: ../index.php");
    exit();
}

$model = mysqli_real_escape_string($conn, $_GET['model']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products for <?php echo htmlspecialchars($model); ?> - Cas’E’Store</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <!-- Navbar (reuse your existing code if needed) -->
  <nav class="navbar">
    <div class="logo"><a href="../index.php">CAS’E’STORE</a></div>
    <div class="nav-links">
      <a href="../index.php">Home</a>
      <a href="../index.php#shop">Shop</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="orders_user.php">Orders</a>
        <a href="../actions/logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
      <a href="cart.php">🛒 Cart</a>
    </div>
  </nav>

  <section class="products">
    <h2>Products for "<?php echo htmlspecialchars($model); ?>"</h2>
    <div class="product-grid">
      <?php
      $sql = "SELECT * FROM products WHERE model LIKE '%$model%'";
      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <div class="product-card">
            <img src="../assets/image/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <div class="product-info">
              <h3><?php echo $row['name']; ?></h3>
              <p><strong>₹<?php echo number_format($row['price'], 2); ?></strong></p>
              <p><?php echo $row['brand'] . " - " . $row['model']; ?></p>
              <p><?php echo $row['stock'] > 0 ? "In Stock" : "Out of Stock"; ?></p>
              <a href="product.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
              <button type="button" class="btn" onclick="addToCart(<?php echo $row['id']; ?>)">Add to Cart</button>
            </div>
          </div>
          <?php
        }
      } else {
        echo "<p>No products found for this model.</p>";
      }
      ?>
    </div>
  </section>

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
