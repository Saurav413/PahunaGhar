<?php
session_start();
require_once 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?redirect=user_bookings');
    exit;
}

$user_id = $_SESSION['user_id'];
$bookings = [];
$error = '';
$success = '';

// Handle booking cancellation
if (isset($_POST['cancel_booking_id'])) {
    $cancel_id = (int)$_POST['cancel_booking_id'];
    try {
        // Only allow cancelling user's own booking if not already cancelled/completed
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status NOT IN ('cancelled', 'completed')");
        $stmt->execute([$cancel_id, $user_id]);
        if ($stmt->rowCount() > 0) {
            $success = 'Booking cancelled successfully.';
        } else {
            $error = 'Unable to cancel booking. It may already be cancelled or completed.';
        }
    } catch (PDOException $e) {
        $error = 'Error cancelling booking: ' . $e->getMessage();
    }
}

// Handle payment action
if (isset($_POST['pay_booking_id'])) {
    $pay_id = (int)$_POST['pay_booking_id'];
    // Redirect to payment page with booking ID
    header('Location: payment.php?booking_id=' . $pay_id);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get user's reviews for each hotel
    $user_reviews = [];
    if (!empty($bookings)) {
        $hotel_ids = array_column($bookings, 'hotel_id');
        $placeholders = str_repeat('?,', count($hotel_ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT hotel_id, rating, comment FROM reviews WHERE user_id = ? AND hotel_id IN ($placeholders)");
        $params = array_merge([$user_id], $hotel_ids);
        $stmt->execute($params);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($reviews as $review) {
            $user_reviews[$review['hotel_id']] = $review;
        }
    }
} catch (PDOException $e) {
    $error = 'Error fetching bookings: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - PahunaGhar</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="booking.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
        }
        .booking-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 70vh;
        }
        .booking-header {
            margin-top: 40px;
            margin-bottom: 20px;
            text-align: center;
        }
        .booking-header h1 {
            font-size: 2.8rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #2d3a4b;
            margin-bottom: 10px;
        }
        .bookings-panel {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            padding: 32px 28px 24px 28px;
            max-width: 900px;
            width: 100%;
            margin: 0 auto 40px auto;
        }
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .bookings-table th, .bookings-table td {
            padding: 14px 10px;
            text-align: center;
        }
        .bookings-table th {
            background: #f5f8fa;
            color: #2d3a4b;
            font-weight: 700;
            font-size: 1.08rem;
            border-bottom: 2px solid #e0eafc;
        }
        .bookings-table tr {
            transition: background 0.2s;
        }
        .bookings-table tr:nth-child(even) {
            background: #f8fbfd;
        }
        .bookings-table tr:hover {
            background: #eaf3fa;
        }
        .bookings-table td {
            color: #34495e;
            font-size: 1rem;
            border-bottom: 1px solid #e0eafc;
        }
        .message {
            margin: 30px auto;
            padding: 18px 24px;
            background: #f5f8fa;
            border-radius: 10px;
            color: #2d3a4b;
            font-size: 1.1rem;
            max-width: 500px;
            text-align: center;
        }
        .review-btn {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .review-btn:hover {
            transform: translateY(-1px);
            color: white;
        }
        .update-review {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        .review-status {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
        }
        .reviewed-badge {
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        @media (max-width: 1000px) {
            .bookings-panel {
                padding: 18px 4px 12px 4px;
            }
            .bookings-table th, .bookings-table td {
                padding: 8px 2px;
                font-size: 0.95rem;
            }
        }
        @media (max-width: 600px) {
            .bookings-panel {
                padding: 6px 0 6px 0;
            }
            .booking-header h1 {
                font-size: 2rem;
            }
            .bookings-table th, .bookings-table td {
                font-size: 0.85rem;
                padding: 6px 1px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span></a>
            <a href="user_bookings.php" class="nav-link">My Bookings</a>
            <a href="lets_chat.php" class="nav-link">Let's Chat</a>
        </div>
        <div class="navbar-right">
            <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
    </nav>
    <div class="container booking-container">
        <div class="booking-header">
            <a href="homepage.php" class="back-link">← Back to Hotels</a>
            <h1>My Bookings</h1>
        </div>
        <div class="bookings-panel">
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (empty($bookings)): ?>
            <div class="message">You have no bookings yet.</div>
        <?php else: ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Hotel Name</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th>Total Price</th>
                        <th>Booked At</th>
                        <th>Review</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['hotel_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['check_in_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['check_out_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
                            <td>
                                <?php if (isset($user_reviews[$booking['hotel_id']])): ?>
                                    <div class="review-status">
                                        <span class="reviewed-badge">Reviewed ⭐ <?php echo htmlspecialchars($user_reviews[$booking['hotel_id']]['rating']); ?></span>
                                        <a href="submit_review.php?hotel_id=<?php echo $booking['hotel_id']; ?>&booking_id=<?php echo $booking['id']; ?>" class="review-btn update-review">Update Review</a>
                                    </div>
                                <?php else: ?>
                                    <a href="submit_review.php?hotel_id=<?php echo $booking['hotel_id']; ?>&booking_id=<?php echo $booking['id']; ?>" class="review-btn">Write Review</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!in_array($booking['status'], ['cancelled', 'completed'])): ?>
                                    <form method="post" onsubmit="return confirm('Are you sure you want to cancel this booking?');" style="display:inline;">
                                        <input type="hidden" name="cancel_booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="review-btn" style="background:linear-gradient(135deg,#e74c3c,#c0392b);margin-top:4px;">Cancel</button>
                                    </form>
                                    <?php if (strtolower(trim($booking['status'])) === 'available'): ?>
                                        <form method="post" style="display:inline; margin-left: 8px;">
                                            <input type="hidden" name="pay_booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" class="review-btn" style="background:linear-gradient(135deg,#10b981,#059669);margin-top:4px;">Pay Now</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:#e74c3c;font-weight:600;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>
    <footer class="booking-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 