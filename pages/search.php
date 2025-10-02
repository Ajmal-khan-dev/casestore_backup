<?php
include '../config/db.php'; // database connection

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($conn, $_GET['query']);

    // Search in multiple columns
    $sql = "SELECT * FROM products 
            WHERE name LIKE '%$search%' 
            OR brand LIKE '%$search%' 
            OR model LIKE '%$search%' 
            OR description LIKE '%$search%'";

    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>

  <div class="product-list">
    <?php if (isset($result) && mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="product-card">
          <img src="../assets/image/<?php echo $row['image']; ?>">
          <h3><?php echo $row['name']; ?></h3>
          <p><strong>Brand:</strong> <?php echo $row['brand']; ?> | <strong>Model:</strong> <?php echo $row['model']; ?></p>
          <p><strong>Price:</strong> ₹<?php echo number_format($row['price'], 2); ?></p>
          <p><strong>Stock:</strong> <?php echo $row['stock'] > 0 ? "In Stock" : "Out of Stock"; ?></p>
          <a href="product.php?id=<?php echo $row['id']; ?>" class="btn">View</a>

        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No products found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
