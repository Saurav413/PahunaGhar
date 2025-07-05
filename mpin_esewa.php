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

$esewa_id = $_SESSION['esewa_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa MPIN Confirmation</title>
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
        .mpin-box {
            background: #23272f;
            padding: 36px 32px 28px 32px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            min-width: 320px;
            max-width: 90vw;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 auto;
        }
        .mpin-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 18px;
        }
        .mpin-input-container {
            position: relative;
            width: 100%;
            margin-bottom: 18px;
        }
        .mpin-input {
            padding: 12px 16px;
            padding-right: 50px;
            border-radius: 8px;
            border: 1.5px solid #353945;
            background: #2d323c;
            color: #f3f4f6;
            font-size: 1.1rem;
            width: 100%;
            outline: none;
            box-sizing: border-box;
        }
        .toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #b0b3b8;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 4px;
        }
        .mpin-confirm-btn {
            width: 100%;
            background: #8bc34a;
            color: #23272f;
            font-weight: 900;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 1.15rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .mpin-confirm-btn:hover {
            background: #7cb342;
        }
        .mpin-msg {
            margin-top: 14px;
            font-weight: 700;
            font-size: 1.08rem;
            text-align: center;
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
            <div class="mpin-box">
                <div class="mpin-title">Enter MPIN to Confirm Payment</div>
                <div class="mpin-input-container">
                    <input type="password" id="mpinInput" class="mpin-input" placeholder="MPIN" maxlength="10">
                    <button type="button" id="toggleMpin" class="toggle-btn" onclick="toggleMpinVisibility()">👁️</button>
                </div>
                <button id="confirmMpinBtn" class="mpin-confirm-btn" type="button">Confirm</button>
                <div id="mpinMsg" class="mpin-msg"></div>
            </div>
            <script>
                function toggleMpinVisibility() {
                    var mpinInput = document.getElementById('mpinInput');
                    var toggleBtn = document.getElementById('toggleMpin');
                    if (mpinInput.type === 'password') {
                        mpinInput.type = 'text';
                        toggleBtn.textContent = '🙈';
                        toggleBtn.title = 'Hide MPIN';
                    } else {
                        mpinInput.type = 'password';
                        toggleBtn.textContent = '👁️';
                        toggleBtn.title = 'Show MPIN';
                    }
                }
                document.getElementById('confirmMpinBtn').onclick = function() {
                    var mpin = document.getElementById('mpinInput').value;
                    var msg = document.getElementById('mpinMsg');
                    if (!mpin) {
                        msg.style.color = '#ef4444';
                        msg.textContent = 'Please enter your MPIN.';
                        return;
                    }
                    // AJAX to check MPIN
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'check_esewa_mpin.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            console.log('check_esewa_mpin.php response:', xhr.responseText);
                            if (xhr.status === 200 && xhr.responseText.trim() === 'success') {
                                console.log('MPIN success, sending update_booking_status.php AJAX...');
                                // Now update booking status
                                var xhr2 = new XMLHttpRequest();
                                xhr2.open('POST', 'update_booking_status.php', true);
                                xhr2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                xhr2.onreadystatechange = function() {
                                    if (xhr2.readyState === 4) {
                                        console.log('update_booking_status.php response:', xhr2.responseText);
                                        if (xhr2.status === 200 && xhr2.responseText.trim() === 'success') {
                                            msg.style.color = '#10b981';
                                            msg.textContent = 'Payment Successful!';
                                        } else {
                                            msg.style.color = '#ef4444';
                                            msg.textContent = 'Payment succeeded, but failed to update booking status. Response: ' + xhr2.responseText;
                                        }
                                    }
                                };
                                xhr2.send('booking_id=<?php echo $booking_id; ?>');
                            } else {
                                msg.style.color = '#ef4444';
                                msg.textContent = 'Invalid MPIN. Please try again.';
                            }
                        }
                    };
                    xhr.send('mpin=' + encodeURIComponent(mpin) + '&booking_id=<?php echo $booking_id; ?>');
                };
            </script>
        <?php else: ?>
            <div class="esewa-detail-row">
                <span class="esewa-detail-label" style="color:#ef4444;">Error</span>
                <span class="esewa-detail-value" style="color:#ef4444;">Booking not found</span>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 