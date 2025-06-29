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
    $userCount = $user_pdo->query("SELECT COUNT(*) FROM register_form")->fetchColumn();
    $bookingCount = $user_pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $reviewCount = $user_pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    $hotelCount = $user_pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
    $avgRating = $user_pdo->query("SELECT AVG(rating) FROM reviews")->fetchColumn();
    
    // Get contact count from the main database
    require_once 'config.php';
    $contactCount = $pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();
    
    // Fallback values if tables don't exist
    $totalUsers = $userCount ?: 0;
    $totalBookings = $bookingCount ?: 0;
    $totalReviews = $reviewCount ?: 0;
    $totalHotels = $hotelCount ?: 0;
    $averageRating = $avgRating ? round($avgRating, 1) : 0;
    $totalContacts = $contactCount ?: 0;
} catch (Exception $e) {
    $totalUsers = 0;
    $totalBookings = 0;
    $totalReviews = 0;
    $totalHotels = 0;
    $averageRating = 0;
    $totalContacts = 0;
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
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="admin_dashboard.php" class="active">Dashboard</a>
                <a href="admin_users.php">Users</a>
                <a href="admin_bookings.php">Bookings</a>
                <a href="admin_reviews.php">Reviews</a>
                <a href="admin_hotels.php">Hotels</a>
                <a href="admin_lets_chat.php">Let's Chat</a>
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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalBookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalReviews; ?></div>
                <div class="stat-label">Total Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalHotels; ?></div>
                <div class="stat-label">Total Hotels</div>
            </div>
            <div class="stat-card">
                <button class="stat-number stat-btn" id="showContactsBtn" type="button"><?php echo $totalContacts; ?></button>
                <div class="stat-label">Total Let's Chat</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $averageRating; ?>/5</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="dashboard-section">
                <h2 class="section-title">
                    Manage Users
                    <a href="admin_users.php" class="btn btn-primary">View All</a>
                </h2>
                <div id="recentUsers">
                    <div class="loading">Loading users...</div>
                </div>
            </div>

            <div class="dashboard-section">
                <h2 class="section-title">
                    Recent Bookings
                    <a href="admin_bookings.php" class="btn btn-primary">View All</a>
                </h2>
                <div id="recentBookings">
                    <div class="loading">Loading bookings...</div>
                </div>
            </div>

            <div class="dashboard-section">
                <h2 class="section-title">
                    Recent Reviews
                    <a href="admin_reviews.php" class="btn btn-primary">View All</a>
                </h2>
                <div id="recentReviews">
                    <div class="loading">Loading reviews...</div>
                </div>
            </div>

            <div class="dashboard-section">
                <h2 class="section-title">Quick Actions</h2>
                <div class="quick-actions">
                    <div class="quick-action">
                        <div class="quick-action-icon">🏨</div>
                        <h3>Manage Hotels</h3>
                        <p>Add, edit, or remove hotels</p>
                        <a href="admin_hotels.php" class="btn btn-primary">Manage Hotels</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">📅</div>
                        <h3>View Bookings</h3>
                        <p>Check booking status and details</p>
                        <a href="admin_bookings.php" class="btn btn-primary">View Bookings</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">⭐</div>
                        <h3>Manage Reviews</h3>
                        <p>View and moderate reviews</p>
                        <a href="admin_reviews.php" class="btn btn-primary">Manage Reviews</a>
                    </div>
                    <div class="quick-action">
                        <div class="quick-action-icon">📧</div>
                        <h3>Manage Let's Chat</h3>
                        <p>View and respond to Let's Chat messages</p>
                        <a href="admin_lets_chat.php" class="btn btn-primary">Manage Let's Chat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for let's chat details -->
    <div id="contactsModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="closeContactsModal">&times;</span>
            <h2>Let's Chat Details</h2>
            <div id="contactsDetails">
                <div class="loading">Loading Let's Chat messages...</div>
            </div>
        </div>
    </div>

    <script>
        // Load recent users
        function loadRecentUsers() {
            fetch('admin_api.php?action=get_recent_users')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentUsers');
                    if (data.success && data.users.length > 0) {
                        container.innerHTML = `
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.users.map(user => `
                                        <tr>
                                            <td>${user.name}</td>
                                            <td>${user.email}</td>
                                            <td><span class="status-active">${user.user_type}</span></td>
                                            <td>
                                                <button class="btn btn-warning" onclick="editUser(${user.id})">Edit</button>
                                                <button class="btn btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                    } else {
                        container.innerHTML = '<div class="no-data">No users found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('recentUsers').innerHTML = '<div class="no-data">Error loading users</div>';
                });
        }

        // Load recent bookings
        function loadRecentBookings() {
            fetch('admin_api.php?action=get_recent_bookings')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentBookings');
                    if (data.success && data.bookings.length > 0) {
                        container.innerHTML = `
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Hotel</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.bookings.map(booking => `
                                        <tr>
                                            <td>${booking.user_name}</td>
                                            <td>${booking.hotel_name}</td>
                                            <td><span class="status-${booking.status.toLowerCase()}">${booking.status}</span></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="viewBooking(${booking.id})">View</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                    } else {
                        container.innerHTML = '<div class="no-data">No bookings found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('recentBookings').innerHTML = '<div class="no-data">Error loading bookings</div>';
                });
        }

        // Load recent reviews
        function loadRecentReviews() {
            fetch('admin_api.php?action=get_recent_reviews')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentReviews');
                    if (data.success && data.reviews.length > 0) {
                        container.innerHTML = `
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Hotel</th>
                                        <th>Rating</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.reviews.map(review => `
                                        <tr>
                                            <td>${review.user_name}</td>
                                            <td>${review.hotel_name}</td>
                                            <td><span class="rating-stars">★ ${review.rating}/5</span></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="viewReview(${review.id})">View</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                    } else {
                        container.innerHTML = '<div class="no-data">No reviews found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('recentReviews').innerHTML = '<div class="no-data">Error loading reviews</div>';
                });
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentUsers();
            loadRecentBookings();
            loadRecentReviews();

            // Modal logic for contacts (with debug logs)
            var showContactsBtn = document.getElementById('showContactsBtn');
            var contactsModal = document.getElementById('contactsModal');
            var closeContactsModal = document.getElementById('closeContactsModal');
            console.log('showContactsBtn:', showContactsBtn);
            console.log('contactsModal:', contactsModal);
            console.log('closeContactsModal:', closeContactsModal);
            if (showContactsBtn && contactsModal && closeContactsModal) {
                showContactsBtn.addEventListener('click', function() {
                    console.log('Total Let's Chat button clicked');
                    contactsModal.style.display = 'block';
                    loadContactsDetails();
                });
                closeContactsModal.onclick = function() {
                    contactsModal.style.display = 'none';
                };
                window.addEventListener('click', function(event) {
                    if (event.target === contactsModal) {
                        contactsModal.style.display = 'none';
                    }
                });
            } else {
                console.log('Modal logic not attached: missing element(s)');
            }
        });

        // Placeholder functions for actions
        function editUser(userId) {
            const newType = prompt('Enter new user type (admin or customer):');
            if (!newType || (newType !== 'admin' && newType !== 'customer')) {
                alert('Invalid user type. Please enter "admin" or "customer".');
                return;
            }
            fetch('admin_api.php?action=update_user_type', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `user_id=${userId}&user_type=${encodeURIComponent(newType)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User type updated successfully.');
                    loadRecentUsers();
                } else {
                    alert(data.error || 'Failed to update user type.');
                }
            })
            .catch(() => alert('Error updating user type.'));
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                fetch('admin_api.php?action=delete_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully.');
                        loadRecentUsers();
                    } else {
                        alert(data.error || 'Failed to delete user.');
                    }
                })
                .catch(() => alert('Error deleting user.'));
            }
        }

        function viewBooking(bookingId) {
            alert('View booking functionality would be implemented here');
        }

        function viewReview(reviewId) {
            alert('View review functionality would be implemented here');
        }

        function loadContactsDetails() {
            const container = document.getElementById('contactsDetails');
            container.innerHTML = '<div class="loading">Loading Let's Chat messages...</div>';
            fetch('admin_api.php?action=get_all_contacts')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.contacts.length > 0) {
                        container.innerHTML = `
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.contacts.map(contact => `
                                        <tr>
                                            <td>${contact.name}</td>
                                            <td>${contact.email}</td>
                                            <td>${contact.message.replace(/\n/g, '<br>')}</td>
                                            <td>${contact.created_at ? new Date(contact.created_at).toLocaleString() : ''}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                    } else {
                        container.innerHTML = '<div class="no-data">No contact messages found.</div>';
                    }
                })
                .catch(() => {
                    container.innerHTML = '<div class="no-data">Failed to load contact messages.</div>';
                });
        }
    </script>
</body>
</html> 