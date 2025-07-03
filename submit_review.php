<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?redirect=submit_review');
    exit;
}

$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
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
            header('Location: user_bookings.php');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: user_bookings.php');
        exit;
    }
} else {
    header('Location: user_bookings.php');
    exit;
}

// Check if user has already reviewed this hotel
$existing_review = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE user_id = ? AND hotel_id = ?");
    $stmt->execute([$_SESSION['user_id'], $hotel_id]);
    $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Reviews table might not exist yet, continue
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (float)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    
    // Basic validation
    if ($rating < 1 || $rating > 5) {
        $message = 'Please select a rating between 1 and 5.';
        $messageType = 'error';
    } elseif (empty($comment)) {
        $message = 'Please provide a comment for your review.';
        $messageType = 'error';
    } elseif (strlen($comment) < 10) {
        $message = 'Review comment must be at least 10 characters long.';
        $messageType = 'error';
    } else {
        try {
            if ($existing_review) {
                // Update existing review
                $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, review_date = CURRENT_TIMESTAMP WHERE user_id = ? AND hotel_id = ?");
                $stmt->execute([$rating, $comment, $_SESSION['user_id'], $hotel_id]);
                $message = 'Review updated successfully!';
            } else {
                // Insert new review
                $stmt = $pdo->prepare("INSERT INTO reviews (user_id, hotel_id, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $hotel_id, $rating, $comment]);
                $message = 'Review submitted successfully!';
            }
            $messageType = 'success';
            
            // Redirect back to bookings after a short delay
            header('Refresh: 2; URL=user_bookings.php');
        } catch (PDOException $e) {
            $message = 'Sorry, there was an error processing your review: ' . $e->getMessage();
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
    <title>Review <?php echo htmlspecialchars($hotel['name']); ?> - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="booking.css">
    <style>
        .review-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            padding: 32px;
        }
        .hotel-info {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0eafc;
        }
        .hotel-info h2 {
            color: #2d3a4b;
            margin-bottom: 10px;
        }
        .hotel-info p {
            color: #7f8c8d;
            margin: 5px 0;
        }
        .rating-section {
            margin-bottom: 25px;
        }
        .rating-section label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2d3a4b;
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 5px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #f39c12;
        }
        .comment-section {
            margin-bottom: 25px;
        }
        .comment-section label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2d3a4b;
        }
        .comment-section textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 2px solid #e0eafc;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            resize: vertical;
        }
        .comment-section textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
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

    <div class="container">
        <div class="review-container">
            <a href="user_bookings.php" class="back-link">← Back to My Bookings</a>
            
            <div class="hotel-info">
                <h2><?php echo htmlspecialchars($hotel['name']); ?></h2>
                <p>📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
                <p>Current Rating: ⭐ <?php echo htmlspecialchars($hotel['rating']); ?>/5</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="rating-section">
                    <label for="rating">Your Rating:</label>
                    <div class="star-rating">
                        <input type="radio" name="rating" value="5" id="star5" <?php echo ($existing_review && $existing_review['rating'] == 5) ? 'checked' : ''; ?>>
                        <label for="star5">★</label>
                        <input type="radio" name="rating" value="4" id="star4" <?php echo ($existing_review && $existing_review['rating'] == 4) ? 'checked' : ''; ?>>
                        <label for="star4">★</label>
                        <input type="radio" name="rating" value="3" id="star3" <?php echo ($existing_review && $existing_review['rating'] == 3) ? 'checked' : ''; ?>>
                        <label for="star3">★</label>
                        <input type="radio" name="rating" value="2" id="star2" <?php echo ($existing_review && $existing_review['rating'] == 2) ? 'checked' : ''; ?>>
                        <label for="star2">★</label>
                        <input type="radio" name="rating" value="1" id="star1" <?php echo ($existing_review && $existing_review['rating'] == 1) ? 'checked' : ''; ?>>
                        <label for="star1">★</label>
                    </div>
                </div>

                <div class="comment-section">
                    <label for="comment">Your Review:</label>
                    <textarea id="comment" name="comment" placeholder="Share your experience with this hotel..." required><?php echo htmlspecialchars($existing_review['comment'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <?php echo $existing_review ? 'Update Review' : 'Submit Review'; ?>
                </button>
            </form>
        </div>
    </div>

    <footer class="booking-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 