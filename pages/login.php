<?php
session_start();
include '../config/db.php'; // adjust path if needed

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login_input']); // can be username or email
    $password = trim($_POST['password']);

    if (empty($login_input) || empty($password)) {
        $message = "Please enter your username/email and password.";
    } else {
        // Fetch user by username or email
        $stmt = $conn->prepare("SELECT id, username, name, password, is_admin FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                if ($user['is_admin'] == 1) {
                    header("Location: ../admin/dashboard.php"); // admin panel
                } else {
                    header("Location: ../index.php"); // normal user
                }
                exit();
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No account found with this username/email.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Cas’E’Store</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- adjust -->
</head>
<body>
      <a href="../index.php" class="back-btn">← Back</a>
    <h2>Login</h2>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form method="POST" action="">
        <label>Username/Email:</label>
        <input type="text" name="login_input" required>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
</body>
</html>
