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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_id = $_POST['khalti_id'] ?? '';
    $input_pass = $_POST['khalti_pass'] ?? '';
    // Check against database
    $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ? AND password = ?");
    $stmt->execute([$input_id, $input_pass]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['khalti_id'] = $input_id;
        header('Location: khalti.php?booking_id=' . $booking_id);
        exit;
    } else {
        $error = 'Invalid Khalti ID or Password/MPIN.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap">
    <style>
        body {
            background: #23272f;
            min-height: 100vh;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            color: #f3f4f6;
        }
        .khalti-main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .khalti-card {
            display: flex;
            background: #2d323c;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            overflow: hidden;
            min-width: 700px;
            max-width: 900px;
        }
        .khalti-left {
            background: #23272f;
            padding: 44px 38px 44px 38px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1.5px solid #353945;
        }
        .khalti-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .khalti-logo img {
            height: 38px;
        }
        .khalti-merchant {
            color: #fff;
            font-size: 1.13rem;
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .khalti-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 1.04rem;
        }
        .khalti-detail-label {
            color: #b0b3b8;
            font-weight: 500;
        }
        .khalti-detail-value {
            color: #f3f4f6;
            font-weight: 700;
        }
        .khalti-detail-row.price {
            margin-top: 18px;
            font-size: 1.18rem;
        }
        .khalti-detail-value.price {
            color: #7c3aed;
        }
        .khalti-detail-row.status {
            margin-top: 8px;
        }
        .khalti-detail-value.status {
            color: #7c3aed;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .khalti-right {
            background: #2d323c;
            padding: 44px 38px 44px 38px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .khalti-form-title {
            color: #fff;
            font-size: 1.18rem;
            font-weight: 800;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .khalti-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .khalti-input {
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #23272f;
            color: #f3f4f6;
            font-size: 1rem;
            border: 1.5px solid #353945;
            outline: none;
        }
        .khalti-input:focus {
            border-color: #7c3aed;
            background: #23272f;
        }
        .khalti-btn {
            background: #7c3aed;
            color: #fff;
            font-weight: 800;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .khalti-btn:hover {
            background: #5b21b6;
        }
        .khalti-forgot {
            color: #b0b3b8;
            text-decoration: underline;
            font-size: 1rem;
            margin-top: 12px;
            display: inline-block;
            text-align: center;
        }
        .khalti-cancel {
            color: #ef4444;
            text-decoration: underline;
            font-size: 1rem;
            margin-top: 18px;
            display: inline-block;
            text-align: center;
        }
        @media (max-width: 900px) {
            .khalti-card {
                flex-direction: column;
                min-width: 320px;
                max-width: 98vw;
            }
            .khalti-left, .khalti-right {
                min-width: unset;
                padding: 32px 16px 32px 16px;
            }
        }
    </style>
</head>
<body>
    <a href="homepage.php" style="position:absolute;left:44px;top:32px;color:#7c3aed;font-weight:900;font-size:1.3rem;text-decoration:none;padding:8px 18px;border-radius:8px;background:#23272f;box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:background 0.2s;z-index:10;">PahunaGhar</a>
    <div class="khalti-main-container">
        <div class="khalti-card">
            <div class="khalti-left">
                <div class="khalti-logo">
                    <img src="uploads/images/khalti-logo.png" alt="Khalti Logo">
                </div>
                <div class="khalti-merchant">PahunaGhar - Hotel Booking</div>
                <?php if ($booking): ?>
                    <div class="khalti-detail-row">
                        <span class="khalti-detail-label">Hotel Name</span>
                        <span class="khalti-detail-value"><?php echo htmlspecialchars($booking['hotel_name']); ?></span>
                    </div>
                    <div class="khalti-detail-row">
                        <span class="khalti-detail-label">Check-in</span>
                        <span class="khalti-detail-value"><?php echo htmlspecialchars($booking['check_in_date']); ?></span>
                    </div>
                    <div class="khalti-detail-row">
                        <span class="khalti-detail-label">Check-out</span>
                        <span class="khalti-detail-value"><?php echo htmlspecialchars($booking['check_out_date']); ?></span>
                    </div>
                    <div class="khalti-detail-row">
                        <span class="khalti-detail-label">Guests</span>
                        <span class="khalti-detail-value"><?php echo htmlspecialchars($booking['guests']); ?></span>
                    </div>
                    <div class="khalti-detail-row price">
                        <span class="khalti-detail-label">Total Price</span>
                        <span class="khalti-detail-value price">$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></span>
                    </div>
                    <div class="khalti-detail-row status">
                        <span class="khalti-detail-label">Status</span>
                        <span class="khalti-detail-value status"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></span>
                    </div>
                <?php else: ?>
                    <div class="khalti-detail-row">
                        <span class="khalti-detail-label" style="color:#ef4444;">Error</span>
                        <span class="khalti-detail-value" style="color:#ef4444;">Booking not found</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="khalti-right">
                <div class="khalti-form-title">Sign in to your account</div>
                <form class="khalti-form" method="post">
                    <input type="text" class="khalti-input" name="khalti_id" placeholder="Khalti ID" required>
                    <input type="password" class="khalti-input" name="khalti_pass" placeholder="Password/MPIN" required>
                    <button type="submit" class="khalti-btn">LOGIN</button>
                    <?php if (!empty($error)): ?>
                        <div style="color:#ef4444;text-align:center;font-weight:700;margin-top:10px;"> <?php echo htmlspecialchars($error); ?> </div>
                    <?php endif; ?>
                </form>
                
        
                <a href="payment.php?booking_id=<?php echo $booking_id; ?>" class="khalti-back" style="color:#10b981;text-decoration:underline;display:block;text-align:center;margin-top:12px;font-weight:700;">Back to Payment Method</a>
            </div>
        </div>
    </div>
</body>
</html> 