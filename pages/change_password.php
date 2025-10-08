<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$message = "";

// Handle password update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "<p class='error'>All fields are required.</p>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<p class='error'>New passwords do not match.</p>";
    } else {
        // Fetch current password from DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Verify current password
        if (password_verify($current_password, $hashed_password)) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $update->bind_param("si", $new_hashed, $uid);
            if ($update->execute()) {
                $message = "<p class='success'>✅ Password changed successfully!</p>";
            } else {
                $message = "<p class='error'>❌ Error updating password.</p>";
            }
            $update->close();
        } else {
            $message = "<p class='error'>Incorrect current password.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - Cas’E’Store</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .change-password-box {
      max-width: 500px;
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
    form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    form input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-top: 5px;
      font-size: 15px;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #2563eb;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn:hover {
      background: #1d4ed8;
    }
    .btn-secondary {
      background: #555;
      text-decoration: none;
      margin-left: 10px;
    }
    .btn-secondary:hover {
      background: #333;
    }
    .error {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
    .success {
      color: green;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <div class="change-password-box">
    <h2>Change Password</h2>

    <?php echo $message; ?>

    <form method="POST">
      <label for="current_password">Current Password:</label>
      <input type="password" id="current_password" name="current_password" required>

      <label for="new_password">New Password:</label>
      <input type="password" id="new_password" name="new_password" required minlength="6">

      <label for="confirm_password">Confirm New Password:</label>
      <input type="password" id="confirm_password" name="confirm_password" required minlength="6">

      <button type="submit" class="btn">Update Password</button>
      <a href="profile.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>

</body>
</html>
