<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$message = "";

// Handle update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (!empty($name) && !empty($username) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $username, $email, $phone, $uid);
        if ($stmt->execute()) {
            $message = "✅ Profile updated successfully!";
        } else {
            $message = "❌ Error updating profile.";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Fetch latest user data
$q = mysqli_query($conn, "SELECT * FROM users WHERE id=$uid");
$user = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile - Cas’E’Store</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .profile-edit-box {
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
    .message {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-bottom: 15px;
    }
    .error {
      color: red;
    }
  </style>
</head>
<body>
  <div class="profile-edit-box">
    <h2>Edit Profile</h2>

    <?php if ($message): ?>
      <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

      <label for="phone">Phone:</label>
      <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

      <button type="submit" class="btn">Update Profile</button>
      <a href="profile.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>

</body>
</html>
