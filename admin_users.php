<?php
session_start();
require_once 'user_config.php';

// Only allow admin and super admin users
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'super_admin'])) {
    header('Location: login.php');
    exit;
}

// Fetch all users
$allUsers = [];
try {
    $stmt = $user_pdo->query('SELECT id, name, email, user_type, created_at FROM user_register_form ORDER BY created_at DESC');
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $allUsers = [];
}

// Handle user deletion
if (isset($_POST['delete_user_id'])) {
    $delete_id = (int)$_POST['delete_user_id'];
    try {
        $stmt = $user_pdo->prepare('DELETE FROM user_register_form WHERE id = ?');
        $stmt->execute([$delete_id]);
        // Optionally, add a success message or redirect
        header('Location: admin_users.php?deleted=1');
        exit;
    } catch (Exception $e) {
        // Optionally, handle error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
        .admin-navbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .admin-navbar-right .welcome-user {
            color: #fff;
            font-size: 1.01rem;
            margin-right: 8px;
        }
        @media (max-width: 900px) {
            .admin-navbar { padding: 0 8px; }
            .admin-navbar-link { padding: 8px 8px; font-size: 0.98rem; }
        }
        .admin-users-container {
            max-width: 1100px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(44, 62, 80, 0.10);
            padding: 36px 32px 32px 32px;
        }
        .admin-users-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .admin-users-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2563eb;
            margin: 0;
        }
        .admin-users-header .total-count {
            background: #2563eb;
            color: #fff;
            border-radius: 20px;
            padding: 8px 22px;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        .users-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .users-table th, .users-table td {
            padding: 14px 10px;
            text-align: left;
        }
        .users-table th {
            background: #f5f8fa;
            color: #2d3a4b;
            font-weight: 700;
            font-size: 1.08rem;
            border-bottom: 2px solid #e0eafc;
        }
        .users-table tr {
            background: #f8fbfd;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.04);
            transition: box-shadow 0.2s, background 0.2s;
        }
        .users-table tr:hover {
            background: #eaf3fa;
            box-shadow: 0 4px 16px rgba(44,62,80,0.10);
        }
        .users-table td {
            color: #34495e;
            font-size: 1rem;
            border-bottom: none;
        }
        @media (max-width: 900px) {
            .admin-users-container { padding: 16px 2vw; }
            .users-table th, .users-table td { padding: 8px 2px; font-size: 0.95rem; }
        }
        @media (max-width: 600px) {
            .admin-users-header { flex-direction: column; gap: 10px; align-items: flex-start; }
            .admin-users-header h1 { font-size: 1.3rem; }
            .users-table th, .users-table td { font-size: 0.85rem; }
        }
        .admin-header-box {
            background: #2563eb;
            border-radius: 32px;
            margin: 36px auto 32px auto;
            max-width: 1200px;
            padding: 48px 24px 36px 24px;
            text-align: center;
            color: #fff;
            box-shadow: 0 4px 32px rgba(44, 62, 80, 0.10);
        }
        .admin-header-box h1 {
            font-size: 2.6rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .admin-header-box p {
            font-size: 1.18rem;
            font-weight: 400;
            margin: 0;
        }
        @media (max-width: 900px) {
            .admin-header-box { padding: 24px 2vw 18px 2vw; }
            .admin-header-box h1 { font-size: 1.5rem; }
        }
        .action-btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            margin-right: 8px;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        .edit-btn {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #fff;
        }
        .edit-btn:hover {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: #fff;
        }
        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
        }
        .delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="admin-navbar-left">
            <a href="admin_dashboard.php" class="admin-navbar-link">Dashboard</a>
            <a href="admin_users.php" class="admin-navbar-link active">Users</a>
            <a href="admin_manage_admins.php" class="admin-navbar-link">Admins</a>
            <a href="admin_bookings.php" class="admin-navbar-link">Bookings</a>
            <a href="admin_reviews.php" class="admin-navbar-link">Reviews</a>
            <a href="admin_hotels.php" class="admin-navbar-link">Hotels</a>
            <a href="admin_lets_chat.php" class="admin-navbar-link">Let's Chat</a>
        </div>
        <div class="admin-navbar-right">
            <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Admin)</span>
            <a href="homepage.php" class="admin-navbar-link">View Site</a>
            <a href="logout.php" class="admin-navbar-link">Logout</a>
        </div>
    </nav>
    <div class="admin-header-box">
        <h1>Manage Users</h1>
        <p>View and manage user accounts</p>
    </div>
    <div class="admin-users-container">
        <div class="admin-users-header">
            <span class="total-count">Total: <?php echo count($allUsers); ?></span>
        </div>
        <table class="users-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allUsers)): ?>
                    <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
                <?php else: ?>
                    <?php foreach ($allUsers as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($user['user_type'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <form method="post" action="admin_users.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 