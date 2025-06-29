<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// Handle status update
if (isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    
    if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        try {
            $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->execute([$status, $booking_id]);
            $message = 'Booking status updated successfully.';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating booking status.';
            $messageType = 'error';
        }
    }
}

// Handle delete action
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = 'Booking deleted successfully.';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting booking.';
        $messageType = 'error';
    }
}

// Get all bookings
try {
    $stmt = $pdo->query("SELECT b.*, u.name as user_name, u.email as user_email FROM bookings b LEFT JOIN register_form u ON b.user_id = u.id ORDER BY b.created_at DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $bookings = [];
    $message = 'Error loading bookings.';
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_bookings.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_users.php">Users</a>
                <a href="admin_bookings.php" class="active">Bookings</a>
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
            <h1>Manage Bookings</h1>
            <p>View and manage hotel bookings</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="bookings-section">
            <h2 class="section-title">All Bookings (<?php echo count($bookings); ?>)</h2>
            
            <?php if (empty($bookings)): ?>
                <div class="no-data">
                    <p>No bookings found.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Hotel</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Guests</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-name"><?php echo htmlspecialchars($booking['user_name'] ?? 'Unknown'); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($booking['user_email'] ?? 'No email'); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="hotel-name"><?php echo htmlspecialchars($booking['hotel_name']); ?></div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <?php echo date('M j, Y', strtotime($booking['check_in'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <?php echo date('M j, Y', strtotime($booking['check_out'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="guests-info">
                                        <?php echo $booking['guests']; ?> <?php echo $booking['guests'] == 1 ? 'Guest' : 'Guests'; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="price-info">
                                        $<?php echo number_format($booking['total_price'], 2); ?>
                                    </div>
                                </td>
                                <td>
                                    <select class="status-select status-<?php echo $booking['status']; ?>" onchange="updateStatus(<?php echo $booking['id']; ?>, this.value)">
                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <form method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateStatus(bookingId, status) {
            const formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('status', status);
            
            fetch('admin_bookings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                // Reload the page to show updated status
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating booking status');
            });
        }
    </script>
</body>
</html> 