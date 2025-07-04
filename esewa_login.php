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
$messageType = '';
$booking = null;

if ($booking_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        $message = 'Booking not found.';
        $messageType = 'error';
    }
} else {
    $message = 'Invalid booking.';
    $messageType = 'error';
}
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
        .esewa-main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .esewa-card {
            display: flex;
            background: #2d323c;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            overflow: hidden;
            min-width: 700px;
            max-width: 900px;
        }
        .esewa-left {
            background: #23272f;
            padding: 44px 38px 44px 38px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1.5px solid #353945;
        }
        .esewa-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .esewa-logo img {
            height: 38px;
        }
        .esewa-merchant {
            color: #fff;
            font-size: 1.13rem;
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .esewa-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 1.04rem;
        }
        .esewa-detail-label {
            color: #b0b3b8;
            font-weight: 500;
        }
        .esewa-detail-value {
            color: #f3f4f6;
            font-weight: 700;
        }
        .esewa-detail-row.price {
            margin-top: 18px;
            font-size: 1.18rem;
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
        }
        .esewa-right {
            background: #2d323c;
            padding: 44px 38px 44px 38px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .esewa-form-title {
            color: #fff;
            font-size: 1.18rem;
            font-weight: 800;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .esewa-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .esewa-input {
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #23272f;
            color: #f3f4f6;
            font-size: 1rem;
            border: 1.5px solid #353945;
            outline: none;
        }
        .esewa-input:focus {
            border-color: #10b981;
            background: #23272f;
        }
        .esewa-captcha {
            background: #353945;
            color: #b0b3b8;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            font-size: 0.98rem;
            margin-bottom: 6px;
        }
        .esewa-btn {
            background: #8bc34a;
            color: #23272f;
            font-weight: 800;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .esewa-btn:hover {
            background: #7cb342;
        }
        .esewa-forgot {
            color: #b0b3b8;
            text-decoration: underline;
            font-size: 1rem;
            margin-top: 12px;
            display: inline-block;
            text-align: center;
        }
        .esewa-register {
            color: #10b981;
            text-decoration: underline;
            font-size: 1rem;
            margin-top: 8px;
            display: inline-block;
            text-align: center;
        }
        .esewa-cancel {
            color: #ef4444;
            text-decoration: underline;
            font-size: 1rem;
            margin-top: 18px;
            display: inline-block;
            text-align: center;
        }
        @media (max-width: 900px) {
            .esewa-card {
                flex-direction: column;
                min-width: 320px;
                max-width: 98vw;
            }
            .esewa-left, .esewa-right {
                min-width: unset;
                padding: 32px 16px 32px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="esewa-main-container">
        <div class="esewa-card">
            <div class="esewa-left">
                <div class="esewa-logo">
                    <img src="uploads/images/esewa-logo.png" alt="eSewa Logo">
                </div>
                <div class="esewa-merchant">PahunaGhar - Hotel Booking</div>
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
                <?php else: ?>
                    <div class="esewa-detail-row">
                        <span class="esewa-detail-label" style="color:#ef4444;">Error</span>
                        <span class="esewa-detail-value" style="color:#ef4444;">Booking not found</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="esewa-right">
                <div class="esewa-form-title">Sign in to your account</div>
                <form class="esewa-form">
                    <input type="text" class="esewa-input" placeholder="eSewa ID" disabled>
                    <input type="password" class="esewa-input" placeholder="Password/MPIN" disabled>
                    <button type="button" class="esewa-btn" disabled>LOGIN</button>
                </form>
               
                
                <a href="payment.php?booking_id=<?php echo $booking_id; ?>" class="esewa-back" style="color:#10b981;text-decoration:underline;display:block;text-align:center;margin-top:12px;font-weight:700;">Back to Payment Method</a>
            </div>
        </div>
    </div>
</body>
</html> 