<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$q = mysqli_query($conn, "SELECT name, username, email, phone FROM users WHERE id=$uid");
$user = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile - Cas’E’Store</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .profile-container {
      max-width: 600px;
      margin: 60px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #2563eb;
      margin-bottom: 20px;
    }
    .profile-info {
      font-size: 16px;
      line-height: 1.8;
    }
    .profile-info p {
      margin: 8px 0;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #2563eb;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.2s;
    }
    .btn:hover {
      background: #1d4ed8;
    }
    .btn-secondary {
      background: #555;
    }
    .btn-secondary:hover {
      background: #333;
    }
    .actions {
      text-align: center;
      margin-top: 30px;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <h2>My Profile</h2>
    <div class="profile-info">
      <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
    </div>

    <div class="actions">
      <a href="edit_profile.php" class="btn">Edit Profile</a>
      <a href="change_password.php" class="btn">Change Password</a>
      <a href="../index.php" class="btn btn-secondary">Back to Home</a>
    </div>
  </div>

</body>
</html>
