<?php
session_start();
include '../config/db.php'; // adjust path if needed

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // new field
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate
    if (empty($username) || empty($name) || empty($email) || empty($password)) {
        $message = "All required fields must be filled.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Phone number must be exactly 10 digits.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Email or Username already registered.";
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (username, name, email, password, phone, is_admin, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
            $stmt->bind_param("sssss", $username, $name, $email, $hashed, $phone);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['is_admin'] = 0;
                header("Location: ../index.php");
                exit();
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup - Cas’E’Store</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- adjust -->
</head>
<body>
    <h2>Create an Account</h2>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form method="POST" action="">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Phone:</label>
        <input type="text" name="phone" pattern="[0-9]{10}" title="Phone number must be exactly 10 digits" required>

        <label>Password:</label>
        <input type="password" name="password" required minlength="6">

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required minlength="6"><br><br>

        <button type="submit" style="padding:10px 20px; border:none; background:#2563eb; color:#fff; border-radius:8px;">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
