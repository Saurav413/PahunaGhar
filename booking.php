<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?redirect=booking&hotel_id=' . $hotel_id);
    exit;
}

$hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$messageType = '';

// Get hotel details
$hotel = null;
if ($hotel_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->execute([$hotel_id]);
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$hotel) {
            header('Location: homepage.php');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: homepage.php');
        exit;
    }
} else {
    header('Location: homepage.php');
    exit;
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = (int)($_POST['guests'] ?? 1);
    $special_requests = trim($_POST['special_requests'] ?? '');
    
    // Basic validation
    if (empty($check_in) || empty($check_out)) {
        $message = 'Please select check-in and check-out dates.';
        $messageType = 'error';
    } elseif (strtotime($check_in) >= strtotime($check_out)) {
        $message = 'Check-out date must be after check-in date.';
        $messageType = 'error';
    } elseif (strtotime($check_in) < strtotime(date('Y-m-d'))) {
        $message = 'Check-in date cannot be in the past.';
        $messageType = 'error';
    } elseif ($guests < 1 || $guests > 10) {
        $message = 'Number of guests must be between 1 and 10.';
        $messageType = 'error';
    } else {
        try {
            // Calculate total price (simple calculation: price per night * number of nights)
            $check_in_date = new DateTime($check_in);
            $check_out_date = new DateTime($check_out);
            $nights = $check_out_date->diff($check_in_date)->days;
            $price_per_night = (float)str_replace(['$', ','], '', $hotel['price']);
            $total_price = $price_per_night * $nights;
            
            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, hotel_id, hotel_name, check_in_date, check_out_date, guests, special_requests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $hotel_id, $hotel['name'], $check_in, $check_out, $guests, $special_requests, $total_price]);
            
            $message = 'Booking submitted successfully! We will confirm your reservation soon.';
            $messageType = 'success';
            
            // Clear form data after successful submission
            $check_in = $check_out = '';
            $guests = 1;
            $special_requests = '';
        } catch (PDOException $e) {
            $message = 'Sorry, there was an error processing your booking: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($hotel['name']); ?> - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/booking.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span></a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <?php if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'super_admin'])): ?>
                    <a href="user_bookings.php" class="nav-link">My Bookings</a>
                <?php endif; ?>
                <a href="lets_chat.php" class="nav-link">Let's Chat</a>
            <?php endif; ?>
        </div>
        <div class="navbar-center">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search destinations, hotels, or prices...">
                <button id="searchBtn">Search</button>
            </div>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <?php if (isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['admin', 'super_admin'])): ?>
                    <a href="<?php echo $_SESSION['user_type'] === 'super_admin' ? 'super_admin_dashboard.php' : 'admin_dashboard.php'; ?>" class="nav-link">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container booking-container">
        <div class="booking-header">
            <a href="homepage.php" class="back-link">← Back to Hotels</a>
            <h1>Book Your Stay</h1>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="booking-content">
            <div class="hotel-details">
                <div class="hotel-image">
                    <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                </div>
                <div class="hotel-info">
                    <h2><?php echo htmlspecialchars($hotel['name']); ?></h2>
                    <p class="hotel-location">📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
                    <p class="hotel-description"><?php echo htmlspecialchars($hotel['description']); ?></p>
                    <div class="hotel-rating">⭐ <?php echo htmlspecialchars($hotel['rating']); ?>/5</div>
                    <div class="hotel-price"><?php echo htmlspecialchars($hotel['price']); ?> per night</div>
                </div>
            </div>

            <div class="booking-form-section">
                <h3>Reservation Details</h3>
                <form class="booking-form" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="check_in">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($check_in ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="check_out">Check-out Date</label>
                            <input type="date" id="check_out" name="check_out" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" value="<?php echo htmlspecialchars($check_out ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <select id="guests" name="guests" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo (isset($guests) && $guests == $i) ? 'selected' : ''; ?>><?php echo $i; ?> <?php echo $i == 1 ? 'Guest' : 'Guests'; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="special_requests">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" rows="4" placeholder="Any special requests or preferences..."><?php echo htmlspecialchars($special_requests ?? ''); ?></textarea>
                    </div>

                    <div class="price-summary">
                        <h4>Price Summary</h4>
                        <div class="price-details">
                            <div class="price-row">
                                <span>Price per night:</span>
                                <span><?php echo htmlspecialchars($hotel['price']); ?></span>
                            </div>
                            <div class="price-row">
                                <span>Number of nights:</span>
                                <span id="nights-count">-</span>
                            </div>
                            <div class="price-row total">
                                <span>Total:</span>
                                <span id="total-price">-</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="book-btn">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="booking-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>

    <script>
        // Calculate price when dates change
        function calculatePrice() {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            const pricePerNight = <?php echo (float)str_replace(['$', ','], '', $hotel['price']); ?>;
            
            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    document.getElementById('nights-count').textContent = nights;
                    document.getElementById('total-price').textContent = '$' + (nights * pricePerNight).toFixed(2);
                } else {
                    document.getElementById('nights-count').textContent = '-';
                    document.getElementById('total-price').textContent = '-';
                }
            }
        }

        // Set minimum check-out date based on check-in date
        document.getElementById('check_in').addEventListener('change', function() {
            const checkInDate = this.value;
            const checkOutInput = document.getElementById('check_out');
            if (checkInDate) {
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.min = nextDay.toISOString().split('T')[0];
                if (checkOutInput.value && checkOutInput.value <= checkInDate) {
                    checkOutInput.value = nextDay.toISOString().split('T')[0];
                }
            }
            calculatePrice();
        });

        document.getElementById('check_out').addEventListener('change', calculatePrice);

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('searchBtn').addEventListener('click', function() {
                const searchTerm = document.getElementById('searchInput').value.trim();
                if (searchTerm) {
                    window.location.href = 'homepage.php?search=' + encodeURIComponent(searchTerm);
                }
            });

            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.trim();
                    if (searchTerm) {
                        window.location.href = 'homepage.php?search=' + encodeURIComponent(searchTerm);
                    }
                }
            });
        });
    </script>
</body>
</html> 