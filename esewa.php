<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?redirect=user_bookings');
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$message = '';
$booking = null;

if ($booking_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        $message = 'Booking not found.';
    }
} else {
    $message = 'Invalid booking.';
}

// Get the user's esewa_id from session (set during login)
$esewa_id = $_SESSION['esewa_id'] ?? null;
$mpin_from_db = null;
if ($esewa_id) {
    $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
    $stmt->execute([$esewa_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $mpin_from_db = $row['mpin'];
    }
}

error_log('eSewa ID in session: ' . ($_SESSION['esewa_id'] ?? 'NOT SET'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa Payment</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap">
    <style>
        body {
            background: #23272f;
            min-height: 100vh;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            color: #f3f4f6;
        }
        .esewa-header {
            display: flex;
            align-items: center;
            padding: 32px 0 0 44px;
        }
        .esewa-header img {
            height: 44px;
        }
        .esewa-main {
            max-width: 480px;
            margin: 40px auto 0 auto;
            background: #2d323c;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            padding: 44px 38px 38px 38px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .esewa-title {
            color: #fff;
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .esewa-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 1.25rem;
        }
        .esewa-detail-label {
            color: #b0b3b8;
            font-weight: 600;
            font-size: 1.18rem;
        }
        .esewa-detail-value {
            color: #f3f4f6;
            font-weight: 800;
            font-size: 1.18rem;
        }
        .esewa-detail-row.price {
            font-size: 1.35rem;
        }
        .esewa-detail-value.price {
            color: #10b981;
        }
        .esewa-detail-row.status {
            margin-top: 8px;
        }
        .esewa-detail-value.status {
            color: #7c3aed;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.18rem;
        }
        .esewa-pay-btn {
            margin-top: 32px;
            width: 100%;
            background: #8bc34a;
            color: #23272f;
            font-weight: 900;
            border: none;
            border-radius: 8px;
            padding: 18px;
            font-size: 1.25rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .esewa-pay-btn:hover {
            background: #7cb342;
        }
        @media (max-width: 600px) {
            .esewa-main {
                max-width: 98vw;
                padding: 24px 8px 24px 8px;
            }
            .esewa-header {
                padding: 18px 0 0 8px;
            }
        }
    </style>
</head>
<body>
    <div class="esewa-header" style="display:flex;align-items:center;gap:24px;">
        <a href="homepage.php" style="color:#10b981;font-weight:900;font-size:1.3rem;text-decoration:none;padding:8px 18px;border-radius:8px;background:#23272f;box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:background 0.2s;">PahunaGhar</a>
        <img src="uploads/images/esewa-logo.png" alt="eSewa Logo">
    </div>
    <div class="esewa-main">
        <div class="esewa-title">Booking Details</div>
        <?php if ($booking): ?>
            <div class="esewa-detail-row">
                <span class="esewa-detail-label">Hotel Name</span>
                <span class="esewa-detail-value"><?php echo htmlspecialchars($booking['hotel_name']); ?></span>
            </div>
            <div class="esewa-detail-row">
                <span class="esewa-detail-label">Check-in</span>
                <span class="esewa-detail-value"><?php echo htmlspecialchars($booking['check_in_date']); ?></span>
            </div>
            <div class="esewa-detail-row">
                <span class="esewa-detail-label">Check-out</span>
                <span class="esewa-detail-value"><?php echo htmlspecialchars($booking['check_out_date']); ?></span>
            </div>
            <div class="esewa-detail-row">
                <span class="esewa-detail-label">Guests</span>
                <span class="esewa-detail-value"><?php echo htmlspecialchars($booking['guests']); ?></span>
            </div>
            <div class="esewa-detail-row price">
                <span class="esewa-detail-label">Total Price</span>
                <span class="esewa-detail-value price">$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></span>
            </div>
            <div class="esewa-detail-row status">
                <span class="esewa-detail-label">Status</span>
                <span class="esewa-detail-value status"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></span>
            </div>
            <a href="esewa_login.php?booking_id=<?php echo $booking_id; ?>" style="color:#10b981;text-decoration:underline;display:block;text-align:center;margin-bottom:18px;font-weight:700;">Back to eSewa Login Page</a>
            <form id="payForm" method="get" action="mpin_esewa.php">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                <button type="submit" class="esewa-pay-btn">Pay Via Esewa</button>
            </form>
        <?php else: ?>
            <div class="esewa-detail-row">
                <span class="esewa-detail-label" style="color:#ef4444;">Error</span>
                <span class="esewa-detail-value" style="color:#ef4444;">Booking not found</span>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 