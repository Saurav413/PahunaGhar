<?php
session_start();
require_once 'user_config.php';

// Check if super admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'super_admin') {
    header('Location: super_admin_login.php');
    exit;
}

$isSuperAdmin = true; // Now properly authenticated

// Fetch statistics for dashboard
$stats = [];
try {
    // Total users
    $stmt = $user_pdo->query("SELECT COUNT(*) as count FROM user_register_form");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total admins
    $stmt = $user_pdo->query("SELECT COUNT(*) as count FROM admin_register_form");
    $stats['total_admins'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active admins
    $stmt = $user_pdo->query("SELECT COUNT(*) as count FROM admin_register_form WHERE is_active = 1");
    $stats['active_admins'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total hotels
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM hotels");
    $stats['total_hotels'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total bookings
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $stats['total_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Recent bookings (last 7 days)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stats['recent_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total reviews
    $stmt = $user_pdo->query("SELECT COUNT(*) as count FROM reviews");
    $stats['total_reviews'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
} catch (Exception $e) {
    // Handle errors gracefully
    $stats = [
        'total_users' => 0,
        'total_admins' => 0,
        'active_admins' => 0,
        'total_hotels' => 0,
        'total_bookings' => 0,
        'recent_bookings' => 0,
        'total_reviews' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="super_admin_dashboard.php" class="active">Dashboard</a>
                <a href="super_admin_manage_admins.php">Manage Admins</a>
                <a href="admin_users.php">Users</a>
                <a href="admin_bookings.php">Bookings</a>
                <a href="admin_reviews.php">Reviews</a>
                <a href="admin_hotels.php">Hotels</a>
                <a href="admin_lets_chat.php">Let's Chat</a>
            </div>
            <div>
                <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> <span class="role-super-admin">Super Admin</span></span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Super Admin Dashboard</h1>
            <p>Complete system overview and management</p>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_admins']; ?></div>
                <div class="stat-label">Total Admins</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_admins']; ?></div>
                <div class="stat-label">Active Admins</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_hotels']; ?></div>
                <div class="stat-label">Total Hotels</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_bookings']; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['recent_bookings']; ?></div>
                <div class="stat-label">Recent Bookings (7 days)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_reviews']; ?></div>
                <div class="stat-label">Total Reviews</div>
            </div>
        </div>
        <div class="dashboard-sections">
            <div class="dashboard-section">
                <h2 class="section-title">Quick Actions</h2>
                <div class="quick-actions">
                    <div class="quick-action">
                        <div class="quick-action-icon">👥</div>
                        <h3>Manage Admins</h3>
                        <p>Create, edit, and manage admin accounts</p>
                        <a href="super_admin_manage_admins.php" class="btn btn-primary">Manage Admins</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">👤</div>
                        <h3>Manage Users</h3>
                        <p>View and manage user accounts</p>
                        <a href="admin_users.php" class="btn btn-primary">Manage Users</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">🏨</div>
                        <h3>Manage Hotels</h3>
                        <p>Add, edit, and manage hotel listings</p>
                        <a href="admin_hotels.php" class="btn btn-primary">Manage Hotels</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">📅</div>
                        <h3>View Bookings</h3>
                        <p>Monitor and manage all bookings</p>
                        <a href="admin_bookings.php" class="btn btn-primary">View Bookings</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">⭐</div>
                        <h3>Manage Reviews</h3>
                        <p>View and moderate user reviews</p>
                        <a href="admin_reviews.php" class="btn btn-primary">Manage Reviews</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">💬</div>
                        <h3>Let's Chat</h3>
                        <p>Manage customer support messages</p>
                        <a href="admin_lets_chat.php" class="btn btn-primary">Let's Chat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="admin-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. Super Admin Dashboard. All rights reserved.
    </footer>
</body>
</html> 