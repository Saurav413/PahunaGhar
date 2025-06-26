<?php
session_start();
require_once 'user_config.php';

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$isAdmin = true; // Now properly authenticated
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_users.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_users.php" class="active">Users</a>
                <a href="admin_bookings.php">Bookings</a>
                <a href="admin_reviews.php">Reviews</a>
                <a href="admin_hotels.php">Hotels</a>
                <a href="admin_contacts.php">Contacts</a>
            </div>
            <div>
                <a href="homepage.php">View Site</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Users</h1>
            <p>View and manage user accounts</p>
        </div>

        <div class="users-table">
            <div class="table-header">
                <h2>All Users</h2>
                <input type="text" id="searchUsers" placeholder="Search users..." class="search-users-input">
            </div>
            <div id="usersList">
                <div class="no-users">
                    <h3>No Users Found</h3>
                    <p>The users table hasn't been created yet or is empty.</p>
                    <p>Users will appear here once they register through the main site.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            document.getElementById('searchUsers').addEventListener('input', function() {
                filterUsers(this.value);
            });
        });

        function loadUsers() {
            fetch('admin_api.php?action=get_users')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.users.length > 0) {
                        displayUsers(data.users);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function displayUsers(users) {
            const container = document.getElementById('usersList');
            container.innerHTML = users.map(user => `
                <div class="user-row">
                    <div><strong>${user.username || user.email}</strong></div>
                    <div>${user.email || 'No email'}</div>
                    <div>${user.created_at || 'Unknown'}</div>
                    <div>${user.status || 'Active'}</div>
                    <div>
                        <button class="btn btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    </div>
                </div>
            `).join('');
        }

        function filterUsers(searchTerm) {
            // This would filter users if there were any
            console.log('Searching for:', searchTerm);
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                alert('User deletion functionality would be implemented here.');
            }
        }
    </script>
</body>
</html> 