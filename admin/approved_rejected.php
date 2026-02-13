<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch processed requests (Approved or Rejected status)
$stmt = $conn->prepare("
    SELECT r.*, u.name, u.email 
    FROM requests r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.status IN ('Approved', 'Rejected') 
    ORDER BY r.submitted_at DESC
");
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
    <title>Processed Requests - Admin Portal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>✓ Processed Requests</h1>
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
                    <span>No processed requests yet. They will appear here after approval or rejection.</span>
                </div>
                <a href="dashboard.php" class="btn btn-primary" style="margin-top: 20px;">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requester</th>
                            <th>Email</th>
                            <th>Request Title</th>
                            <th>Category</th>
                            <th>Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $serial = 1; foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo $serial++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($request['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                    <br>
                                    <small style="color: #6b7280;">
                                        <?php echo htmlspecialchars(substr($request['details'], 0, 50)) . (strlen($request['details']) > 50 ? '...' : ''); ?>
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
        <?php endif; ?>
    </div>
</body>
</html>
