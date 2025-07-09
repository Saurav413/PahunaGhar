<?php
session_start();
require_once 'user_config.php';

// Only allow admin users
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// Handle admin account deactivation/activation
if (isset($_POST['toggle_admin_status'])) {
    $admin_id = (int)$_POST['admin_id'];
    $new_status = $_POST['new_status'] === 'true' ? 1 : 0;
    
    // Prevent deactivating own account
    if ($admin_id === $_SESSION['admin_id']) {
        $message = 'You cannot deactivate your own account.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $user_pdo->prepare('UPDATE admin_register_form SET is_active = ? WHERE id = ?');
            $stmt->execute([$new_status, $admin_id]);
            $message = 'Admin account status updated successfully.';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error updating admin status: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Handle admin deletion
if (isset($_POST['delete_admin'])) {
    $admin_id = (int)$_POST['admin_id'];
    
    // Prevent deleting own account
    if ($admin_id === $_SESSION['admin_id']) {
        $message = 'You cannot delete your own account.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $user_pdo->prepare('DELETE FROM admin_register_form WHERE id = ?');
            $stmt->execute([$admin_id]);
            $message = 'Admin account deleted successfully.';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error deleting admin account: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all admin accounts
$allAdmins = [];
try {
    $stmt = $user_pdo->query('SELECT id, name, email, admin_role, created_at, last_login, is_active FROM admin_register_form ORDER BY created_at DESC');
    $allAdmins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $allAdmins = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - PahunaGhar</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .admin-navbar {
            width: 100%;
            background: #1a2332;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            min-height: 54px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.04);
        }
        .admin-navbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-navbar-link {
            color: #fff;
            font-weight: 500;
            font-size: 1.08rem;
            padding: 8px 22px;
            border-radius: 8px;
            text-decoration: none;
            margin: 0 2px;
            transition: background 0.18s, color 0.18s;
        }
        .admin-navbar-link.active, .admin-navbar-link:focus {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
        }
        .admin-navbar-link:hover:not(.active) {
            background: #222e44;
            color: #fff;
        }
        .admin-header-box {
            background: #fff;
            padding: 32px 40px;
            margin: 24px 40px;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(44,62,80,0.08);
        }
        .admin-header-box h1 {
            margin: 0 0 8px 0;
            color: #1a2332;
            font-size: 2.2em;
        }
        .admin-header-box p {
            margin: 0;
            color: #6b7280;
            font-size: 1.1em;
        }
        .admin-admins-container {
            background: #fff;
            margin: 0 40px 40px 40px;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(44,62,80,0.08);
            overflow: hidden;
        }
        .admin-admins-header {
            padding: 24px 32px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-count {
            font-weight: 600;
            color: #6b7280;
        }
        .admins-table {
            width: 100%;
            border-collapse: collapse;
        }
        .admins-table th {
            background: #f9fafb;
            padding: 16px 24px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .admins-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .admins-table tr:hover {
            background: #f9fafb;
        }
        .status-active {
            color: #10b981;
            font-weight: 600;
        }
        .status-inactive {
            color: #ef4444;
            font-weight: 600;
        }
        .role-super-admin {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .role-admin {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-danger {
            background: #ef4444;
            color: #fff;
        }
        .btn-success {
            background: #10b981;
            color: #fff;
        }
        .btn-warning {
            background: #f59e0b;
            color: #fff;
        }
        .message {
            padding: 12px 16px;
            border-radius: 6px;
            margin: 16px 0;
        }
        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="admin-navbar-left">
            <a href="admin_dashboard.php" class="admin-navbar-link">Dashboard</a>
            <a href="admin_users.php" class="admin-navbar-link">Users</a>
            <a href="admin_manage_admins.php" class="admin-navbar-link active">Admins</a>
            <a href="admin_bookings.php" class="admin-navbar-link">Bookings</a>
            <a href="admin_reviews.php" class="admin-navbar-link">Reviews</a>
            <a href="admin_hotels.php" class="admin-navbar-link">Hotels</a>
            <a href="admin_lets_chat.php" class="admin-navbar-link">Let's Chat</a>
        </div>
        <div class="admin-navbar-right">
            <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'Admin'); ?>)</span>
            <a href="homepage.php" class="admin-navbar-link">View Site</a>
            <a href="logout.php" class="admin-navbar-link">Logout</a>
        </div>
    </nav>
    
    <div class="admin-header-box">
        <h1>Manage Admin Accounts</h1>
        <p>View and manage admin accounts</p>
    </div>
    
    <div class="admin-admins-container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="admin-admins-header">
            <span class="total-count">Total Admins: <?php echo count($allAdmins); ?></span>
            <a href="admin_register.php" class="btn btn-success">Create New Admin</a>
        </div>
        
        <table class="admins-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allAdmins)): ?>
                    <tr><td colspan="7" style="text-align:center;">No admin accounts found.</td></tr>
                <?php else: ?>
                    <?php foreach ($allAdmins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td>
                                <?php if ($admin['admin_role'] === 'super_admin'): ?>
                                    <span class="role-super-admin">Super Admin</span>
                                <?php else: ?>
                                    <span class="role-admin">Admin</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?php echo $admin['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($admin['created_at'])); ?></td>
                            <td><?php echo $admin['last_login'] ? date('Y-m-d H:i', strtotime($admin['last_login'])) : 'Never'; ?></td>
                            <td>
                                <?php if ($admin['id'] !== $_SESSION['admin_id'] && $admin['admin_role'] !== 'super_admin'): ?>
                                    <?php if ($admin['is_active']): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="hidden" name="new_status" value="false">
                                            <button type="submit" name="toggle_admin_status" class="btn btn-warning" onclick="return confirm('Deactivate this admin account?')">Deactivate</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="hidden" name="new_status" value="true">
                                            <button type="submit" name="toggle_admin_status" class="btn btn-success">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                        <button type="submit" name="delete_admin" class="btn btn-danger" onclick="return confirm('Delete this admin account? This action cannot be undone.')">Delete</button>
                                    </form>
                                <?php elseif ($admin['id'] === $_SESSION['admin_id']): ?>
                                    <span style="color: #6b7280; font-style: italic;">Current User</span>
                                <?php elseif ($admin['admin_role'] === 'super_admin'): ?>
                                    <span style="color: #6b7280; font-style: italic;">Super Admin (protected)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 