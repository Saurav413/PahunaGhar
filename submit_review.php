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

// Validate that the booking is confirmed before allowing review
$booking_confirmed = false;
if ($booking_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT status FROM bookings WHERE id = ? AND user_id = ? AND hotel_id = ?");
        $stmt->execute([$booking_id, $_SESSION['user_id'], $hotel_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking && strtolower(trim($booking['status'])) === 'confirmed') {
            $booking_confirmed = true;
        } else {
            // If booking is not confirmed, redirect back to bookings
            header('Location: user_bookings.php?error=booking_not_confirmed');
            exit;
        }
    } catch (PDOException $e) {
        // If there's an error checking the booking, redirect back
        header('Location: user_bookings.php?error=booking_validation_error');
        exit;
    }
} else {
    // If no booking_id provided, redirect back
    header('Location: user_bookings.php?error=invalid_booking');
    exit;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (float)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    $image_filename = $existing_review['image'] ?? null;
    $remove_image = isset($_POST['remove_image']);
    $new_image_uploaded = false;
    // Handle image upload
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['review_image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['review_image']['name'], PATHINFO_EXTENSION);
            $image_filename_new = 'review_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $target_path = __DIR__ . '/uploads/review_images/' . $image_filename_new;
            if (!is_dir(__DIR__ . '/uploads/review_images/')) {
                mkdir(__DIR__ . '/uploads/review_images/', 0777, true);
            }
            if (move_uploaded_file($_FILES['review_image']['tmp_name'], $target_path)) {
                // Remove old image if exists
                if (!empty($image_filename) && file_exists(__DIR__ . '/uploads/review_images/' . $image_filename)) {
                    unlink(__DIR__ . '/uploads/review_images/' . $image_filename);
                }
                $image_filename = $image_filename_new;
                $new_image_uploaded = true;
            }
        } else {
            $message = 'Only JPG, PNG, and GIF images are allowed.';
            $messageType = 'error';
        }
    }
    // Handle image removal
    if ($remove_image && !empty($image_filename)) {
        if (file_exists(__DIR__ . '/uploads/review_images/' . $image_filename)) {
            unlink(__DIR__ . '/uploads/review_images/' . $image_filename);
        }
        $image_filename = null;
    }
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
    } elseif ($messageType !== 'error') {
        try {
            if ($existing_review) {
                // Update existing review
                $sql = "UPDATE reviews SET rating = ?, comment = ?, review_date = CURRENT_TIMESTAMP, image = ? WHERE user_id = ? AND hotel_id = ?";
                $params = [$rating, $comment, $image_filename, $_SESSION['user_id'], $hotel_id];
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Review updated successfully!';
            } else {
                // Insert new review
                $sql = "INSERT INTO reviews (user_id, hotel_id, rating, comment, image) VALUES (?, ?, ?, ?, ?)";
                $params = [$_SESSION['user_id'], $hotel_id, $rating, $comment, $image_filename];
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/booking.css">
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

            <form method="post" enctype="multipart/form-data">
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

                <?php if (!empty($existing_review['image']) && file_exists(__DIR__ . '/uploads/review_images/' . $existing_review['image'])): ?>
                    <div class="image-preview-section" style="margin-bottom:12px;">
                        <label>Current Image:</label><br>
                        <img src="uploads/review_images/<?php echo htmlspecialchars($existing_review['image']); ?>" alt="Current Review Image" style="max-width:120px;max-height:90px;border-radius:8px;box-shadow:0 2px 8px rgba(44,62,80,0.10);margin-bottom:6px;cursor:pointer;" onclick="showImageModal(this.src)">
                        <br>
                        <label><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
                    </div>
                <?php endif; ?>

                <div class="image-upload-section" style="margin-bottom:22px;">
                    <label for="review_image">Upload an Image (optional):</label>
                    <input type="file" id="review_image" name="review_image" accept="image/*" style="padding:8px 0;">
                </div>

                <button type="submit" class="submit-btn">
                    <?php echo $existing_review ? 'Update Review' : 'Submit Review'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;">
        <span style="position:absolute;top:30px;right:50px;font-size:2.5rem;color:#fff;cursor:pointer;z-index:10001;" onclick="closeImageModal()">&times;</span>
        <img id="modalImg" src="" style="max-width:90vw;max-height:80vh;border-radius:12px;box-shadow:0 4px 32px rgba(44,62,80,0.25);border:6px solid #fff;z-index:10000;">
    </div>
    <script>
    function showImageModal(src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }
    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
        document.getElementById('modalImg').src = '';
    }
    // Optional: Close modal on background click
    window.addEventListener('click', function(e) {
        var modal = document.getElementById('imageModal');
        if (e.target === modal) closeImageModal();
    });
    </script>

    <footer class="booking-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 