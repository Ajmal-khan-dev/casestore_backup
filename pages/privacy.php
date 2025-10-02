<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy - Cas’E’Store</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">CAS’E’STORE</div>
    <form action="search.php" method="GET" style="text-align:center; margin:20px;">
      <input type="text" name="query" placeholder="Search for phone cases..." required
             style="padding:10px; width:300px; border:1px solid #ccc; border-radius:8px;">
      <button type="submit" style="padding:10px 20px; border:none; background:#2563eb; color:#fff; border-radius:8px;">
          Search
      </button>
    </form>
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
<a href="cart.php">
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
  <!-- Privacy Section -->
  <section class="section legal">
    <h2>Privacy Policy</h2>
    <p>At Cas’E’Store, your privacy is very important to us. This Privacy Policy outlines how we collect, use, and safeguard your information.</p>

    <h3>Information We Collect</h3>
    <p>We may collect personal details such as your name, email address, shipping information, and payment details when you place an order.</p>

    <h3>How We Use Your Information</h3>
    <p>Your data is used to process orders, improve our services, and provide you with updates about our products and promotions.</p>

    <h3>Data Protection</h3>
    <p>We implement security measures to protect your personal data. However, no online system is 100% secure, and we cannot guarantee complete protection.</p>

    <h3>Third-Party Services</h3>
    <p>We may share information with trusted third-party services (e.g., payment gateways, delivery partners) strictly for business purposes.</p>

    <h3>Changes to This Policy</h3>
    <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page.</p>

    <p>If you have questions, please contact us at: <strong>support@casestore.com</strong></p>
  </section>

  <!-- Footer -->
  <footer>
  <p>&copy; 2025 Cas’E’Store | 
     <a href="privacy.php">Privacy Policy</a> | 
     <a href="terms.php">Terms</a> | 
     <a href="signup.php">Signup</a> | 
     <a href="login.php">Login</a>
  </p>
</footer>
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
            document.getElementById("cartCount").textContent = data.cart_count;
            alert("✅ Item added to cart!");
        } else if (data.message === "login_required") {
            window.location.href = "login.php";
        }
    })
    .catch(err => console.error(err));
}
</script>
  <script src="../assets/js/script.js"></script>
</body>
</html>