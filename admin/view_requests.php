<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$action_message = "";

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = intval($_POST['request_id'] ?? 0);
    $action = trim($_POST['action'] ?? '');
    
    if ($request_id > 0 && in_array($action, ['Approved', 'Rejected'])) {
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $request_id);
        
        if ($stmt->execute()) {
            $action_message = "Request " . strtolower($action) . " successfully.";
        } else {
            $action_message = "Error processing request.";
        }
        $stmt->close();
    }
}

// Fetch pending requests (Pending status)
$stmt = $conn->prepare("
    SELECT r.*, u.name, u.email, u.phone 
    FROM requests r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.status = 'Pending' 
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
    <title>Review Requests - Admin Portal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>🔔 Pending Requests</h1>
            <div class="user-info">
                <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if ($action_message): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <span><?php echo htmlspecialchars($action_message); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (count($requests) === 0): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">✓</div>
                <div class="alert alert-info">
                    <span>ℹ️</span>
                    <span>No pending requests at the moment. All requests have been processed!</span>
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
                            <th>Contact</th>
                            <th>Request Title</th>
                            <th>Category</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $serial = 1; foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo $serial++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($request['name']); ?></strong>
                                </td>
                                <td>
                                    <small>
                                        <div><?php echo htmlspecialchars($request['email']); ?></div>
                                        <div><?php echo htmlspecialchars($request['phone']); ?></div>
                                    </small>
                                </td>
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
                                    <form method="POST" action="" style="display: flex; gap: 6px;">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="action" value="Approved" class="btn btn-small btn-success">✓ Approve</button>
                                        <button type="submit" name="action" value="Rejected" class="btn btn-small btn-danger">✕ Reject</button>
                                    </form>
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
