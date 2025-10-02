<?php
session_start();
include '../config/db.php'; // adjust path if needed

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        // Fetch user
        $stmt = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                if ($user['is_admin'] == 1) {
                    header("Location: ../admin/index.php"); // admin panel
                } else {
                    header("Location: ../index.php"); // normal user
                }
                exit();
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No account found with this email.";
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
    <h2>Login</h2>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form method="POST" action="">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
</body>
</html>
