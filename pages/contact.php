<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact - Cas’E’Store</title>
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

  <!-- Contact Section -->
  <section class="section">
    <h2>Contact Us</h2>
    <p>If you have any questions, feel free to reach out to us using the form below.</p>

    <form class="contact-form">
      <input type="text" placeholder="Your Name" required><br>
      <input type="email" placeholder="Your Email" required><br>
      <textarea placeholder="Your Message" rows="5" required></textarea><br>
      <button type="submit" class="btn">Send Message</button>
       <button type="submit" class="btn">Send Message</button>
    </form>
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
</body>
</html>