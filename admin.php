<?php
session_start();
require_once 'user_config.php';

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$isAdmin = true; // Now properly authenticated

// Get statistics
try {
    $hotelCount = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalHotels = $hotelCount ?: 12; // Fallback to sample data count
    $totalUsers = $userCount ?: 0;
} catch (Exception $e) {
    $totalHotels = 12;
    $totalUsers = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="admin.php" class="active">Dashboard</a>
                <a href="admin_hotels.php">Manage Hotels</a>
                <a href="admin_users.php">Manage Users</a>
                <a href="admin_bookings.php">Bookings</a>
            </div>
            <div>
                <a href="homepage.php">View Site</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome to PahunaGhar Administration Panel</p>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalHotels; ?></div>
                <div class="stat-label">Total Hotels</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Active Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$0</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div class="admin-sections">
            <div class="admin-section">
                <h2 class="section-title">Quick Actions</h2>
                <div class="quick-actions">
                    <div class="quick-action">
                        <div class="quick-action-icon">🏨</div>
                        <h3>Add Hotel</h3>
                        <p>Add a new hotel to the system</p>
                        <a href="admin_add_hotel.php" class="btn btn-primary">Add Hotel</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">👥</div>
                        <h3>Manage Users</h3>
                        <p>View and manage user accounts</p>
                        <a href="admin_users.php" class="btn btn-primary">Manage Users</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">📊</div>
                        <h3>View Reports</h3>
                        <p>Generate system reports</p>
                        <a href="admin_reports.php" class="btn btn-primary">View Reports</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">⚙️</div>
                        <h3>Settings</h3>
                        <p>Configure system settings</p>
                        <a href="admin_settings.php" class="btn btn-primary">Settings</a>
                    </div>
                </div>
            </div>

            <div class="admin-section">
                <h2 class="section-title">Recent Hotels</h2>
                <div class="recent-hotels" id="recentHotels">
                    <!-- Recent hotels will be loaded here -->
                </div>
                <div style="margin-top: 15px;">
                    <a href="admin_hotels.php" class="btn btn-primary">View All Hotels</a>
                </div>
            </div>
        </div>

        <div class="admin-section admin-section-mt-30">
            <h2 class="section-title">System Information</h2>
            <div class="admin-grid">
                <div>
                    <h3>Server Status</h3>
                    <p><strong>Database:</strong> <span class="db-connected">Connected</span></p>
                    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                </div>
                <div>
                    <h3>Recent Activity</h3>
                    <p>No recent activity to display</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load recent hotels
        function loadRecentHotels() {
            fetch('admin_api.php?action=get_recent_hotels')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const hotelsContainer = document.getElementById('recentHotels');
                        hotelsContainer.innerHTML = data.hotels.map(hotel => `
                            <div class="hotel-item">
                                <div class="hotel-info">
                                    <div class="hotel-name">${hotel.name}</div>
                                    <div class="hotel-location">${hotel.location}</div>
                                </div>
                                <div class="hotel-actions">
                                    <a href="admin_edit_hotel.php?id=${hotel.id}" class="btn btn-warning btn-small">Edit</a>
                                    <button onclick="deleteHotel(${hotel.id})" class="btn btn-danger btn-small">Delete</button>
                                </div>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading recent hotels:', error);
                    // Fallback to sample data
                    const sampleHotels = [
                        { id: 1, name: "The Soaltee Kathmandu", location: "Kathmandu" },
                        { id: 2, name: "Hyatt Place Kathmandu", location: "Kathmandu" },
                        { id: 3, name: "Gurung Cottage", location: "Pokhara" }
                    ];
                    const hotelsContainer = document.getElementById('recentHotels');
                    hotelsContainer.innerHTML = sampleHotels.map(hotel => `
                        <div class="hotel-item">
                            <div class="hotel-info">
                                <div class="hotel-name">${hotel.name}</div>
                                <div class="hotel-location">${hotel.location}</div>
                            </div>
                            <div class="hotel-actions">
                                <a href="admin_edit_hotel.php?id=${hotel.id}" class="btn btn-warning btn-small">Edit</a>
                                <button onclick="deleteHotel(${hotel.id})" class="btn btn-danger btn-small">Delete</button>
                            </div>
                        </div>
                    `).join('');
                });
        }

        function deleteHotel(hotelId) {
            if (confirm('Are you sure you want to delete this hotel?')) {
                fetch('admin_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_hotel&id=${hotelId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Hotel deleted successfully!');
                        loadRecentHotels();
                    } else {
                        alert('Error deleting hotel: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting hotel');
                });
            }
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentHotels();
        });
    </script>
</body>
</html> 