<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    
    if (in_array($status, ['pending', 'available', 'confirmed', 'cancelled'])) {
        try {
            $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->execute([$status, $booking_id]);
            $message = 'Booking status updated successfully.';
            $messageType = 'success';

            if ($status === 'confirmed') {
                // Fetch user email and booking details
                $stmt = $pdo->prepare("SELECT u.email, u.name, b.hotel_name, b.check_in_date, b.check_out_date FROM bookings b LEFT JOIN user_register_form u ON b.user_id = u.id WHERE b.id = ?");
                $stmt->execute([$booking_id]);
                $bookingInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($bookingInfo && !empty($bookingInfo['email'])) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->SMTPDebug = 2;
                        $mail->Debugoutput = 'html';
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'pahunaghar76@gmail.com'; // Your Gmail address
                        $mail->Password = 'ecgk wujk owbs orpr';     // Your Gmail app password
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        $mail->setFrom('pahunaghar76@gmail.com', 'PahunaGhar');
                        $mail->addAddress($bookingInfo['email'], $bookingInfo['name']);

                        $mail->Subject = 'Booking Confirmed - PahunaGhar';
                        $mail->Body    = "Dear {$bookingInfo['name']},\n\nYour booking for {$bookingInfo['hotel_name']} from {$bookingInfo['check_in_date']} to {$bookingInfo['check_out_date']} has been confirmed!\n\nThank you for choosing PahunaGhar.";

                        $mail->send();
                    } catch (Exception $e) {
                        echo "Mailer Error: " . $mail->ErrorInfo;
                    }
                }
            }
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
    $stmt = $pdo->query("SELECT b.*, u.name as user_name, u.email as user_email FROM bookings b LEFT JOIN user_register_form u ON b.user_id = u.id ORDER BY b.created_at DESC");
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
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="styles.css">
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
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="admin-navbar-left">
            <a href="admin_dashboard.php" class="admin-navbar-link">Dashboard</a>
            <a href="admin_users.php" class="admin-navbar-link">Users</a>
            <a href="admin_bookings.php" class="admin-navbar-link active">Bookings</a>
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
        <h1>Manage Bookings</h1>
        <p>View and manage hotel bookings</p>
    </div>

    <div class="admin-container">
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
                                        <?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?>
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
                                        <option value="available" <?php echo $booking['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
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