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

try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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