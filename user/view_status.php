<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch requests for logged-in user
$stmt = $conn->prepare("SELECT id, title, details, category, submitted_at, status FROM requests WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Requests - Online Request Portal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>📊 My Requests</h1>
            <div class="user-info">
                <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (count($requests) === 0): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">📭</div>
                <div class="alert alert-info">
                    <span>ℹ️</span>
                    <span>You haven't submitted any requests yet.</span>
                </div>
                <a href="submit_request.php" class="btn btn-primary" style="margin-top: 20px;">Submit Your First Request</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                    <br>
                                    <small style="color: #6b7280;">
                                        <?php echo htmlspecialchars(substr($request['details'], 0, 60)) . (strlen($request['details']) > 60 ? '...' : ''); ?>
                                    </small>
                                </td>
                                <td><?php echo htmlspecialchars($request['category']); ?></td>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($request['submitted_at']))); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($request['status']); ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <a href="submit_request.php" class="btn btn-primary">Submit New Request</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
