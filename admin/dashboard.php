<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['name'];

// Count pending requests
$pending_stmt = $conn->prepare("SELECT COUNT(*) as count FROM requests WHERE status = 'Pending'");
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result();
$pending_count = $pending_result->fetch_assoc()['count'];
$pending_stmt->close();

// Count processed requests
$processed_stmt = $conn->prepare("SELECT COUNT(*) as count FROM requests WHERE status IN ('Approved', 'Rejected')");
$processed_stmt->execute();
$processed_result = $processed_stmt->get_result();
$processed_count = $processed_result->fetch_assoc()['count'];
$processed_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Request Portal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>⚙️ Admin Dashboard</h1>
            <div class="user-info">
                <p>Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong></p>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-cards">
            <div class="card">
                <h3>🔔 Pending Requests</h3>
                <p class="card-count"><?php echo $pending_count; ?></p>
                <p>Requests awaiting your review and approval</p>
                <a href="view_requests.php" class="btn btn-primary">Review Requests</a>
            </div>
            
            <div class="card">
                <h3>✓ Processed</h3>
                <p class="card-count"><?php echo $processed_count; ?></p>
                <p>Approved and rejected requests</p>
                <a href="approved_rejected.php" class="btn btn-primary">View Processed</a>
            </div>
        </div>
    </div>
</body>
</html>
