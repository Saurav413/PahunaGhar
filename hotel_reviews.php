<?php
session_start();
require_once 'config.php';

$hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hotel = null;
$reviews = [];
$error = '';

if ($hotel_id > 0) {
    try {
        // Get hotel details
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->execute([$hotel_id]);
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$hotel) {
            header('Location: homepage.php');
            exit;
        }
        
        // Get reviews for this hotel
        $stmt = $pdo->prepare("
            SELECT r.*, u.name as user_name 
            FROM reviews r 
            JOIN user_register_form u ON r.user_id = u.id 
            WHERE r.hotel_id = ? 
            ORDER BY r.review_date DESC
        ");
        $stmt->execute([$hotel_id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate average rating
        $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $error = 'Error loading reviews: ' . $e->getMessage();
    }
} else {
    header('Location: homepage.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - <?php echo htmlspecialchars($hotel['name']); ?> - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .reviews-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            padding: 32px;
        }
        .hotel-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0eafc;
        }
        .hotel-header h1 {
            color: #2d3a4b;
            margin-bottom: 10px;
        }
        .hotel-header p {
            color: #7f8c8d;
            margin: 5px 0;
        }
        .stats-section {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8fbfd;
            border-radius: 10px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #3498db;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .reviews-list {
            margin-top: 30px;
        }
        .review-item {
            border: 1px solid #e0eafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafbfc;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .reviewer-name {
            font-weight: 600;
            color: #2d3a4b;
        }
        .review-rating {
            color: #f39c12;
            font-weight: 600;
        }
        .review-date {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .review-comment {
            color: #34495e;
            line-height: 1.6;
        }
        .no-reviews {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 40px;
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
        .write-review-btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .write-review-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
    </style>
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

    <div class="container">
        <div class="reviews-container">
            <a href="homepage.php" class="back-link">← Back to Hotels</a>
            
            <div class="hotel-header">
                <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
                <p>📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
                <p><?php echo htmlspecialchars($hotel['description']); ?></p>
            </div>

            <div class="stats-section">
                <div class="stat-item">
                    <div class="stat-value"><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?></div>
                    <div class="stat-label">Average Rating</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['total_reviews'] ?? 0; ?></div>
                    <div class="stat-label">Total Reviews</div>
                </div>
            </div>

            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <div style="text-align: center;">
                    <a href="submit_review.php?hotel_id=<?php echo $hotel_id; ?>" class="write-review-btn">Write a Review</a>
                </div>
            <?php endif; ?>

            <div class="reviews-list">
                <h2>Customer Reviews</h2>
                
                <?php if (empty($reviews)): ?>
                    <div class="no-reviews">
                        <p>No reviews yet for this hotel.</p>
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <p>Be the first to share your experience!</p>
                        <?php else: ?>
                            <p><a href="login.php">Login</a> to write a review.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div>
                                    <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                    <div class="review-date"><?php echo date('F j, Y', strtotime($review['review_date'])); ?></div>
                                </div>
                                <div class="review-rating">⭐ <?php echo htmlspecialchars($review['rating']); ?>/5</div>
                            </div>
                            <div class="review-comment">
                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="booking-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 