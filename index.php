<?php
session_start();
include 'config/db.php'; // DB connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cas’E’Store</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">CAS’E’STORE</div>
    <form action="pages/search.php" method="GET" style="text-align:center; margin:30px;">
      <input type="text" name="query" placeholder="Search for phone cases..." required
             style="padding:10px; width:300px; border:1px solid #ccc; border-radius:8px;">
      <button type="submit" style="padding:10px 20px; border:none; background:#2563eb; color:#fff; border-radius:8px;">
          Search
      </button>
    </form>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="index.php#shop">Shop</a>
      <a href="pages/about.php">About</a>
      <a href="pages/contact.php">Contact</a>
      <?php if (isset($_SESSION['user_id'])): ?>
  <a href="pages/my_orders.php">Orders</a>
  <a href="actions/logout.php">Logout</a>
  <a href="pages/profile.php">👤</a>
<?php else: ?>
  <a href="pages/login.php">Login</a>
<?php endif; ?>
<a href="pages/cart.php">
        <div class="cart">
          🛒Cart
          <span class="cart-count" id="cartCount">
            <?php
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
                $q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id=$uid");
                $row = mysqli_fetch_assoc($q);
                echo $row['total'] ? $row['total'] : 0;
            } else {
                echo 0;
            }
            ?>
          </span>
        </div>
      </a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <h1>MOBILE PHONE CASES</h1><br>
    <button class="btn" onclick="openModelList()">SELECT YOUR MODEL</button><br>
    <p id="selectedModel" class="selected-model"></p>
  </section>
  
  <!-- Phone Model Modal -->
  <div id="modelModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModelList()">&times;</span>
      <h2>Select Your Phone Model</h2>
      <ul class="model-list">
        <li onclick="selectModel('I Phone 15 Pro')">iPhone 15 Pro</li>
        <li onclick="selectModel('I phone 15')">iPhone 15</li>
        <li onclick="selectModel('Samsung Galaxy S24')">Samsung Galaxy S24</li>
        <li onclick="selectModel('Samsung Galaxy S24 Ultra')">Samsung Galaxy S24 Ultra</li>
        <li onclick="selectModel('OnePlus 12')">OnePlus 12</li>
        <li onclick="selectModel('OnePlus Nord 4')">OnePlus Nord 4</li>
        <li onclick="selectModel('Nothing Phone 2a')">Nothing Phone 2a</li>
        <li onclick="selectModel('Google Pixel 9 Pro')">Google Pixel 9 Pro</li>
        <li onclick="selectModel('Google Pixel 9')">Google Pixel 9</li>
        <li onclick="selectModel('Honor Magic V Flip')">Honor Magic V Flip</li>
        <li onclick="selectModel('iQOO NEO 10R')">iQOO NEO 10R</li>
        <li onclick="selectModel('Oneplus13R')">Oneplus13R</li>
        <li onclick="selectModel('Redmi NOTE 15 pro+')">Redmi NOTE 15 pro+</li>
        <li onclick="selectModel('IPhone 16')">iPhone 16</li>
        <li onclick="selectModel('Motorola Edge 50 Ultra')">Motorola Edge 50 Ultra</li>
      </ul>
    </div>
  </div>

  <!-- Products -->
  <section class="products" id="shop">
    <h2>Featured Products</h2>
    <div class="product-grid">
      <?php
      $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4"; 
      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <div class="product-card">
            <img src="assets/image/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <div class="product-info">
              <h3><?php echo $row['name']; ?></h3>
              <p><strong>₹<?php echo number_format($row['price'], 2); ?></strong></p>
              <p><?php echo $row['brand'] . " - " . $row['model']; ?></p>
              <p><?php echo $row['stock'] > 0 ? "In Stock" : "Out of Stock"; ?></p>
              <a href="pages/product.php?id=<?php echo $row['id']; ?>" class="btn">View</a>

              <!-- ✅ Correct Add to Cart -->
              <button type="button" class="btn" onclick="addToCart(<?php echo $row['id']; ?>)">Add to Cart</button>
            </div>
          </div>
          <?php
        }
      } else {
        echo "<p>No products found in the store.</p>";
      }
      ?>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Cas’E’Store | 
       <a href="pages/privacy.php">Privacy Policy</a> | 
       <a href="pages/terms.php">Terms</a> | 
       <a href="pages/signup.php">Signup</a> | 
       <a href="pages/login.php">Login</a> | 
       <a href="pages/contact.php">Contact</a> | 
       <a href="admin">Merchant</a>
    </p>
  </footer>
  <script>
function openModelList() {
    document.getElementById("modelModal").style.display = "block";
}

function closeModelList() {
    document.getElementById("modelModal").style.display = "none";
}

// When selecting a model → go to model_products.php with ?model=...
function selectModel(modelName) {
    window.location.href = "pages/model_products.php?model=" + encodeURIComponent(modelName);
}

function addToCart(productId) {
    fetch("actions/add_to_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "product_id=" + productId
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById("cartCount").textContent = data.cart_count;
            alert("✅ Item added to cart!");
        } else if (data.message === "login_required") {
            window.location.href = "pages/login.php";
        }
    })
    .catch(err => console.error(err));
}
</script>
<script src="assets/js/script.js"></script>
</body>
</html>
