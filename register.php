<?php
session_start();
require_once 'config/database.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    
    // Validate role
    $allowed_roles = ['user', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'user';
    }
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            $error = "Database error: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Email already registered.";
            } else {
                // Hash password and insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
                if (!$insert_stmt) {
                    $error = "Database error: " . htmlspecialchars($conn->error);
                } else {
                    $insert_stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $role);
                    
                    if ($insert_stmt->execute()) {
                        $success = "Registration successful! Redirecting to login...";
                        header("Refresh: 2; url=login.php");
                        exit();
                    } else {
                        $error = "Registration failed: " . htmlspecialchars($insert_stmt->error);
                    }
                    $insert_stmt->close();
                }
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Online Request Portal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>✋ Create Account</h1>
            <div class="user-info">
                <a href="index.php" class="btn btn-secondary">← Back to Home</a>
            </div>
        </div>
    </div>
    <main class="page">
    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            <p>Create your account to get started</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <span>✓</span>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter Name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+1 (555) 123-4567" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user">Regular User</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 12px;">Create Account</button>
            </form>
            
            <p class="link">Already have an account? <a href="login.php">Sign in</a></p>
            <p class="link" style="margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
                <small><a href="debug.php">Database Issues?</a></small>
            </p>
        </div>
    </div>
    </main>
</body>
</html>
