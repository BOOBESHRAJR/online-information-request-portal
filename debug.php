<?php
session_start();
require_once 'config/database.php';

// Check database connection
$connection_ok = !$conn->connect_error;
$table_ok = false;
$users_count = 0;

if ($connection_ok) {
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    $table_ok = $result->num_rows > 0;
    
    if ($table_ok) {
        $users_result = $conn->query("SELECT COUNT(*) as count FROM users");
        $users_count = $users_result->fetch_assoc()['count'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_name = "Test User " . date('Y-m-d H:i:s');
    $test_email = "test_" . time() . "@example.com";
    $test_password = password_hash("test123", PASSWORD_DEFAULT);
    $test_phone = "1234567890";
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'user')");
    if ($stmt) {
        $stmt->bind_param("ssss", $test_name, $test_email, $test_password, $test_phone);
        $stmt->execute();
        $stmt->close();
    }
    header("Refresh: 1");
    exit();
}

$users_result = $conn->query("SELECT id, name, email, role, created_at FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Debug - Online Request Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f0f9ff 100%);
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            color: #374151;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #7c3aed 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 32px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
        }
        .header p {
            margin: 8px 0 0 0;
            color: rgba(255, 255, 255, 0.8);
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #3b82f6;
        }
        .card h3 {
            margin: 0 0 12px 0;
            font-size: 16px;
            color: #111827;
        }
        .card p {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #3b82f6;
        }
        .card.error {
            border-top-color: #ef4444;
            color: #991b1b;
        }
        .card.error p {
            color: #ef4444;
        }
        .section {
            background: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
        }
        .section h2 {
            margin: 0 0 20px 0;
            font-size: 24px;
            color: #111827;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 700;
            border-bottom: 2px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
            color: #374151;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:hover {
            background: #f9fafb;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .status.ok {
            background: #d1fae5;
            color: #065f46;
        }
        .status.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 2px solid #e5e7eb;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
        .empty-state p {
            margin: 12px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 Database Diagnostic Tool</h1>
        <p>Check database connection and perform tests</p>
    </div>

    <div class="cards">
        <div class="card <?php echo $connection_ok ? '' : 'error'; ?>">
            <h3>Connection</h3>
            <p><?php echo $connection_ok ? '✓' : '✕'; ?></p>
        </div>
        <div class="card <?php echo $table_ok ? '' : 'error'; ?>">
            <h3>Users Table</h3>
            <p><?php echo $table_ok ? '✓' : '✕'; ?></p>
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $users_count; ?></p>
        </div>
    </div>

    <?php if (!$connection_ok): ?>
        <div class="section" style="border-left: 4px solid #ef4444;">
            <h2>❌ Connection Error</h2>
            <p style="color: #991b1b;"><?php echo htmlspecialchars($conn->connect_error); ?></p>
            <p>Check your database credentials in <code>config/database.php</code></p>
        </div>
    <?php else: ?>
        <div class="section">
            <h2>📋 Existing Users</h2>
            <?php if ($users_result && $users_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><span class="status ok"><?php echo htmlspecialchars($row['role']); ?></span></td>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($row['created_at']))); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>📭 No users found in database</p>
                    <p>Create your first account by <a href="register.php" style="color: #3b82f6; font-weight: 700;">registering here</a></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>🧪 Test INSERT Operation</h2>
            <p>Click the button below to create a test user account:</p>
            <form method="POST">
                <button type="submit" class="btn btn-primary">Create Test User</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="section" style="text-align: center;">
        <a href="register.php" class="btn btn-secondary">← Back to Registration</a>
    </div>
</body>
</html>
