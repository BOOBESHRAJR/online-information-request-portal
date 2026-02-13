<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Online Request Portal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>👤 User Dashboard</h1>
            <div class="user-info">
                <p>Welcome back, <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>
        </div>
    </div>
    
    <main class="page">
    <div class="container">
        <div class="dashboard-cards">
            <div class="card">
                <h3>📝 Submit Request</h3>
                <p>Create a new information request and track its progress in real-time</p>
                <a href="submit_request.php" class="btn btn-primary">Submit New Request</a>
            </div>
            
            <div class="card">
                <h3>📊 View Status</h3>
                <p>Check the status and details of all your submitted requests</p>
                <a href="view_status.php" class="btn btn-primary">View My Requests</a>
            </div>
        </div>
    </div>
    </main>
</body>
</html>
