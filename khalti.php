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

// Get the user's khalti_id from session (set during login)
$khalti_id = $_SESSION['khalti_id'] ?? null;
$mpin_from_db = null;
if ($khalti_id) {
    $stmt = $pdo->prepare("SELECT mpin FROM khalti_users WHERE khalti_id = ?");
    $stmt->execute([$khalti_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $mpin_from_db = $row['mpin'];
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
        .khalti-header {
            display: flex;
            align-items: center;
            padding: 32px 0 0 44px;
        }
        .khalti-header img {
            height: 44px;
        }
        .khalti-main {
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
        .khalti-title {
            color: #fff;
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .khalti-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 1.25rem;
        }
        .khalti-detail-label {
            color: #b0b3b8;
            font-weight: 600;
            font-size: 1.18rem;
        }
        .khalti-detail-value {
            color: #f3f4f6;
            font-weight: 800;
            font-size: 1.18rem;
        }
        .khalti-detail-row.price {
            font-size: 1.35rem;
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
            font-size: 1.18rem;
        }
        .khalti-pay-btn {
            margin-top: 32px;
            width: 100%;
            background: #7c3aed;
            color: #fff;
            font-weight: 900;
            border: none;
            border-radius: 8px;
            padding: 18px;
            font-size: 1.25rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .khalti-pay-btn:hover {
            background: #5b21b6;
        }
        @media (max-width: 600px) {
            .khalti-main {
                max-width: 98vw;
                padding: 24px 8px 24px 8px;
            }
            .khalti-header {
                padding: 18px 0 0 8px;
            }
        }
    </style>
</head>
<body>
    <div class="khalti-header" style="display:flex;align-items:center;gap:24px;">
        <a href="homepage.php" style="color:#7c3aed;font-weight:900;font-size:1.3rem;text-decoration:none;padding:8px 18px;border-radius:8px;background:#23272f;box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:background 0.2s;">PahunaGhar</a>
        <img src="uploads/images/khalti-logo.png" alt="Khalti Logo">
    </div>
    <div class="khalti-main">
        <div class="khalti-title">Booking Details</div>
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
            <a href="khalti_login.php?booking_id=<?php echo $booking_id; ?>" style="color:#7c3aed;text-decoration:underline;display:block;text-align:center;margin-bottom:18px;font-weight:700;">Back to Khalti Login Page</a>
            <form id="payForm" method="post" action="#" onsubmit="return false;">
                <button type="button" class="khalti-pay-btn" id="showMpinModal">Pay Via Khalti</button>
            </form>
            <div id="mpinModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(30,32,40,0.75);z-index:1000;align-items:center;justify-content:center;">
                <div style="background:#23272f;padding:36px 32px 28px 32px;border-radius:16px;box-shadow:0 8px 32px rgba(44,62,80,0.18);min-width:320px;max-width:90vw;position:relative;display:flex;flex-direction:column;align-items:center;">
                    <button onclick="closeMpinModal()" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;color:#b0b3b8;cursor:pointer;">&times;</button>
                    <div style="font-size:1.25rem;font-weight:700;color:#fff;margin-bottom:18px;">Enter MPIN to Confirm Payment</div>
                    <input type="password" id="mpinInput" placeholder="MPIN" maxlength="10" style="padding:12px 16px;border-radius:8px;border:1.5px solid #353945;background:#2d323c;color:#f3f4f6;font-size:1.1rem;width:100%;margin-bottom:18px;outline:none;">
                    <button id="confirmMpinBtn" style="width:100%;background:#7c3aed;color:#fff;font-weight:900;border:none;border-radius:8px;padding:14px;font-size:1.15rem;cursor:pointer;transition:background 0.2s;">Confirm</button>
                    <div id="mpinMsg" style="margin-top:14px;font-weight:700;font-size:1.08rem;text-align:center;"></div>
                </div>
            </div>
            <script>
                function closeMpinModal() {
                    document.getElementById('mpinModal').style.display = 'none';
                }
                document.getElementById('showMpinModal').onclick = function() {
                    document.getElementById('mpinModal').style.display = 'flex';
                    document.getElementById('mpinInput').value = '';
                    document.getElementById('mpinMsg').textContent = '';
                    document.getElementById('mpinInput').focus();
                };
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
                    xhr.open('POST', 'check_khalti_mpin.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200 && xhr.responseText === 'success') {
                                // Now update booking status
                                var xhr2 = new XMLHttpRequest();
                                xhr2.open('POST', 'update_booking_status.php', true);
                                xhr2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                xhr2.onreadystatechange = function() {
                                    if (xhr2.readyState === 4) {
                                        if (xhr2.status === 200 && xhr2.responseText === 'success') {
                                            msg.style.color = '#7c3aed';
                                            msg.textContent = 'Payment Successful!';
                                        } else {
                                            msg.style.color = '#ef4444';
                                            msg.textContent = 'Payment succeeded, but failed to update booking status.';
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
                    xhr.send('mpin=' + encodeURIComponent(mpin));
                };
            </script>
        <?php else: ?>
            <div class="khalti-detail-row">
                <span class="khalti-detail-label" style="color:#ef4444;">Error</span>
                <span class="khalti-detail-value" style="color:#ef4444;">Booking not found</span>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 