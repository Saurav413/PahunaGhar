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

// Handle payment confirmation
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $booking &&
    $booking['status'] === 'available' &&
    isset($_POST['payment_method']) &&
    in_array($_POST['payment_method'], ['esewa', 'khalti'])
) {
    $selected_method = $_POST['payment_method'];
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ? AND status = 'available'");
        $stmt->execute([$booking_id, $user_id]);
        if ($stmt->rowCount() > 0) {
            // Send confirmation email to user
            try {
                require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
                require_once __DIR__ . '/PHPMailer/src/SMTP.php';
                require_once __DIR__ . '/PHPMailer/src/Exception.php';
                
                $stmt = $pdo->prepare("SELECT u.email, u.name, b.hotel_name, b.check_in_date, b.check_out_date, b.total_price FROM bookings b LEFT JOIN user_register_form u ON b.user_id = u.id WHERE b.id = ?");
                $stmt->execute([$booking_id]);
                $bookingInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($bookingInfo && !empty($bookingInfo['email'])) {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'pahunaghar76@gmail.com';
                    $mail->Password = 'ecgk wujk owbs orpr';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->setFrom('pahunaghar76@gmail.com', 'PahunaGhar');
                    $mail->addAddress($bookingInfo['email'], $bookingInfo['name']);
                    $mail->Subject = 'Booking Confirmed - PahunaGhar';
                    $mail->isHTML(true);
                    $mail->Body = "<h2>Booking Confirmed!</h2>"
                        . "<p>Dear {$bookingInfo['name']},</p>"
                        . "<p>Your booking for <strong>{$bookingInfo['hotel_name']}</strong> has been <b>confirmed</b>.</p>"
                        . "<ul>"
                        . "<li><b>Hotel:</b> {$bookingInfo['hotel_name']}</li>"
                        . "<li><b>Check-in:</b> {$bookingInfo['check_in_date']}</li>"
                        . "<li><b>Check-out:</b> {$bookingInfo['check_out_date']}</li>"
                        . "<li><b>Total Payment:</b> $" . number_format($bookingInfo['total_price'], 2) . "</li>"
                        . "</ul>"
                        . "<p>Thank you for booking with PahunaGhar!</p>";
                    $mail->send();
                }
            } catch (Exception $e) {
                error_log('Mail error: ' . $e->getMessage());
            }
            $_SESSION['success'] = ucfirst($selected_method) . ' payment successful! Your booking is now confirmed.';
            header('Location: user_bookings.php');
            exit;
        } else {
            $message = 'Unable to process payment. Please try again or contact support.';
            $messageType = 'error';
        }
    } catch (PDOException $e) {
        $message = 'Error processing payment: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - PahunaGhar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap">
    <style>
        body {
            background: #23272f;
            min-height: 100vh;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            color: #f3f4f6;
        }
        .payment-main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-card {
            display: flex;
            background: #2d323c;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            overflow: hidden;
            min-width: 700px;
            max-width: 900px;
        }
        .payment-left {
            background: #23272f;
            padding: 44px 38px 44px 38px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1.5px solid #353945;
        }
        .payment-left h2 {
            color: #fff;
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
        }
        .booking-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 1.25rem;
        }
        .booking-detail-label {
            color: #b0b3b8;
            font-weight: 600;
            font-size: 1.18rem;
        }
        .booking-detail-value {
            color: #f3f4f6;
            font-weight: 800;
            font-size: 1.18rem;
        }
        .booking-detail-row.price {
            margin-top: 18px;
            font-size: 1.35rem;
        }
        .booking-detail-value.price {
            color: #10b981;
        }
        .booking-detail-row.status {
            margin-top: 8px;
        }
        .booking-detail-value.status {
            color: #7c3aed;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.18rem;
        }
        .payment-right {
            background: #2d323c;
            padding: 44px 38px 44px 38px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .payment-header {
            color: #fff;
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .payment-methods {
            display: flex;
            gap: 32px;
            justify-content: center;
            margin-bottom: 18px;
            align-items: center;
        }
        .payment-methods button {
            background: #23272f;
            border: 2px solid #353945;
            border-radius: 14px;
            padding: 26px 36px 18px 36px;
            cursor: pointer;
            transition: box-shadow 0.2s, border-color 0.2s, background 0.2s, transform 0.18s;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            outline: none;
        }
        .payment-methods button:hover, .payment-methods button:focus {
            border-color: #10b981;
            background: #23272f;
            box-shadow: 0 6px 24px rgba(16,185,129,0.13);
            transform: translateY(-2px) scale(1.03);
        }
        .payment-methods img {
            height: 60px;
            margin-bottom: 14px;
            filter: drop-shadow(0 2px 6px rgba(44,62,80,0.07));
        }
        .payment-methods button span {
            font-weight: 900;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
            color: #f3f4f6;
            text-shadow: 0 1px 4px rgba(55,65,81,0.07);
        }
        .payment-methods .divider {
            width: 2px;
            height: 44px;
            background: #353945;
            border-radius: 2px;
        }
        .pay-instruction {
            text-align: center;
            color: #b0b3b8;
            font-weight: 700;
            margin-bottom: 18px;
            font-size: 1.08rem;
            letter-spacing: 0.5px;
        }
        .message {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-weight: 700;
            font-size: 1.08rem;
            letter-spacing: 0.5px;
            background: #23272f;
            color: #ffb300;
        }
        .message.error {
            background: #3a2323;
            color: #ef4444;
        }
        .message.success {
            background: #233a2f;
            color: #10b981;
        }
        @media (max-width: 900px) {
            .payment-card {
                flex-direction: column;
                min-width: 320px;
                max-width: 98vw;
            }
            .payment-left, .payment-right {
                min-width: unset;
                padding: 32px 16px 32px 16px;
            }
        }
    </style>
</head>
<body>
    <a href="homepage.php" style="position:absolute;left:44px;top:32px;font-weight:900;font-size:1.3rem;text-decoration:none;padding:8px 18px;border-radius:8px;background:#23272f;box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:background 0.2s;z-index:10;">
        <span style="color:#2563eb;">Pahuna</span><span style="color:#10b981;">Ghar</span>
    </a>
    <div class="payment-main-container">
        <div class="payment-card">
            <div class="payment-left">
                <h2>Booking Details</h2>
                <?php if ($booking): ?>
                    <div class="booking-detail-row">
                        <span class="booking-detail-label">Hotel Name</span>
                        <span class="booking-detail-value"><?php echo htmlspecialchars($booking['hotel_name']); ?></span>
                    </div>
                    <div class="booking-detail-row">
                        <span class="booking-detail-label">Check-in</span>
                        <span class="booking-detail-value"><?php echo htmlspecialchars($booking['check_in_date']); ?></span>
                    </div>
                    <div class="booking-detail-row">
                        <span class="booking-detail-label">Check-out</span>
                        <span class="booking-detail-value"><?php echo htmlspecialchars($booking['check_out_date']); ?></span>
                    </div>
                    <div class="booking-detail-row">
                        <span class="booking-detail-label">Guests</span>
                        <span class="booking-detail-value"><?php echo htmlspecialchars($booking['guests']); ?></span>
                    </div>
                    <div class="booking-detail-row price">
                        <span class="booking-detail-label">Total Price</span>
                        <span class="booking-detail-value price">$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></span>
                    </div>
                    <div class="booking-detail-row status">
                        <span class="booking-detail-label">Status</span>
                        <span class="booking-detail-value status"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="payment-right">
                <div class="payment-header">Complete Your Payment</div>
                <?php if ($message): ?>
                    <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if ($booking && $booking['status'] === 'available'): ?>
                    <form id="paymentForm">
                        <div class="pay-instruction">Select a payment method</div>
                        <div class="payment-methods">
                            <button type="button" id="esewaBtn">
                                <img src="uploads/images/esewa-logo.png" alt="eSewa Logo">
                                <span>eSewa</span>
                            </button>
                            <div class="divider"></div>
                            <button type="button" id="khaltiBtn">
                                <img src="uploads/images/khalti-logo.png" alt="Khalti Logo">
                                <span>Khalti</span>
                            </button>
                        </div>
                    </form>
                <?php elseif ($booking && $booking['status'] === 'confirmed'): ?>
                    <div class="message success">This booking is already confirmed and paid.</div>
                <?php elseif ($booking): ?>
                    <div class="message">This booking cannot be paid for at this time.</div>
                <?php endif; ?>
                <div style="text-align:center;margin-top:18px;">
                    <a href="user_bookings.php" style="color:#b0b3b8;text-decoration:underline;">Back to My Bookings</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('esewaBtn').onclick = function() {
            window.location.href = 'esewa_login.php?booking_id=' + <?php echo $booking_id; ?>;
        };
        document.getElementById('khaltiBtn').onclick = function() {
            window.location.href = 'khalti_login.php?booking_id=' + <?php echo $booking_id; ?>;
        };
    </script>
    <?php echo 'Booking ID: ' . $booking_id; ?>
</body>
</html> 