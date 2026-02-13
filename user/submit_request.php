<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$register_id = $_SESSION['user_id']; // Using user_id as register_id
$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $details = trim($_POST['details'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    // Validate inputs
    if (empty($title) || empty($details) || empty($category)) {
        $error = "All fields are required.";
    } else {
        // Insert request into database
        $submitted_at = date('Y-m-d H:i:s');
        $status = 'Pending';
        
        $stmt = $conn->prepare("INSERT INTO requests (user_id, register_id, title, details, category, submitted_at, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $user_id, $register_id, $title, $details, $category, $submitted_at, $status);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Failed to submit request. Please try again.";
        }
        $stmt->close();
    }
}

if ($success) {
    $title = "";
    $details = "";
    $category = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Request - Online Request Portal</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <h1>📋 Submit Request</h1>
            <div class="user-info">
                <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>
        </div>
    </div>
    <main class="page">
        <?php if ($success): ?>
            <div class="modal-overlay active" id="successModal">
                <div class="modal">
                    <h2>✓ Success!</h2>
                    <p>Your request has been submitted successfully and is now pending review.</p>
                    <div class="modal-buttons">
                        <button onclick="submitAnother()" class="btn btn-primary">Submit Another</button>
                        <button onclick="viewRequests()" class="btn btn-secondary">View My Requests</button>
                        <button onclick="goToDashboard()" class="btn btn-secondary">Return to Dashboard</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="container">
            <div class="form-container" style="max-width: 600px;">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Request Title</label>
                    <input type="text" id="title" name="title" placeholder="Brief title for your request" required value="<?php echo htmlspecialchars($title ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="details">Detailed Description</label>
                    <textarea id="details" name="details" placeholder="Provide detailed information about your request..." required><?php echo htmlspecialchars($details ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select a Category</option>
                        <option value="General Inquiry" <?php echo ($category === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                        <option value="Technical Support" <?php echo ($category === 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                        <option value="Document Request" <?php echo ($category === 'Document Request') ? 'selected' : ''; ?>>Document Request</option>
                        <option value="Complaint" <?php echo ($category === 'Complaint') ? 'selected' : ''; ?>>Complaint</option>
                        <option value="Other" <?php echo ($category === 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; text-align: center;">Submit Request</button>
            </form>
            </div>
        </div>
    
    </main>
    <script>
        function submitAnother() {
            location.reload();
        }
        
        function viewRequests() {
            window.location.href = 'view_status.php';
        }
        
        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
