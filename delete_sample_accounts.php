<?php
/**
 * Delete default sample accounts from database
 */

require_once 'config/database.php';

$deleted_count = 0;
$errors = [];

// Delete the sample accounts
$emails_to_delete = ['admin@portal.com', 'user@example.com'];

foreach ($emails_to_delete as $email) {
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        $deleted_count++;
    } else {
        $errors[] = $email . ": " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Sample Accounts - Online Request Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f0f9ff 100%);
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
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
            margin: 0 0 8px 0;
            font-size: 28px;
            font-weight: 800;
        }
        .section {
            background: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }
        .alert {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border-left-color: #10b981;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left-color: #ef4444;
        }
        .alert-info {
            background: #eff6ff;
            color: #0c4a6e;
            border-left-color: #3b82f6;
        }
        h2 {
            margin: 0 0 16px 0;
            font-size: 24px;
            color: #111827;
        }
        p, li {
            color: #4b5563;
            line-height: 1.6;
            margin: 8px 0;
        }
        ul {
            margin: 16px 0;
            padding-left: 20px;
        }
        pre {
            background: #f3f4f6;
            padding: 16px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 12px;
            color: #1f2937;
        }
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            flex-wrap: wrap;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>🗑️ Delete Sample Accounts</h1>
    </div>

    <div class="section">
        <?php if ($deleted_count > 0): ?>
            <div class="alert alert-success">
                <strong>✓ Success!</strong>
                <p><?php echo $deleted_count; ?> sample account(s) have been deleted.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <strong>ℹ️ No sample accounts found</strong>
                <p>No default accounts to delete.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>⚠️ Some errors occurred:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h2>🔐 Create New Admin Account</h2>
        <p>You can now create a new admin account using one of these methods:</p>

        <h3 style="font-size: 16px; color: #1f2937; margin-top: 20px;">Method 1: Administrative SQL</h3>
        <p>Run this SQL command in phpMyAdmin:</p>
        <pre>INSERT INTO users (name, email, password, phone, role) VALUES (
  'Admin User',
  'admin@example.com',
  '<?php echo htmlspecialchars(password_hash('admin123', PASSWORD_DEFAULT)); ?>',
  '1234567890',
  'admin'
);</pre>

        <h3 style="font-size: 16px; color: #1f2937; margin-top: 20px;">Method 2: Regular Registration</h3>
        <p>Use the registration form to create an account and manually change the role to 'admin' via phpMyAdmin.</p>

        <div class="alert alert-info" style="margin-top: 24px;">
            <strong>📝 Reminder:</strong>
            <p>After creating an admin account, you can delete this file for security purposes.</p>
        </div>
    </div>

    <div class="button-group">
        <a href="register.php" class="btn btn-primary">Register New Account</a>
        <a href="debug.php" class="btn btn-secondary">Database Debug</a>
        <a href="index.php" class="btn btn-secondary">Return Home</a>
    </div>
</body>
</html>
