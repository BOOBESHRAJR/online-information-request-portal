<?php
// Home page - redirect to appropriate login based on role
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Information Request Portal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>📋 Request Portal</h1>
            <div class="user-info">
                <span style="color: rgba(255, 255, 255, 0.8);">Welcome to our platform</span>
            </div>
        </div>
    </div>

    <main class="page">
        <div class="container">
            <div class="welcome-section">
                <h1>Online Information Request Portal</h1>
                <p>Streamline your information requests with our modern, secure, and easy-to-use platform. Submit requests, track status, and receive responses efficiently.</p>
                
                <div class="button-group">
                    <a href="register.php" class="btn btn-primary">Create Account</a>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
