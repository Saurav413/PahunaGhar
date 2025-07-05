<?php
session_start();
require_once 'config.php';

// Only allow admin users
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// Handle delete review
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([$review_id]);
        $message = 'Review deleted successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting review: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Fetch all reviews with hotel and user info
try {
    $stmt = $pdo->query('
        SELECT r.*, h.name AS hotel_name, u.name AS user_name
        FROM reviews r
        JOIN hotels h ON r.hotel_id = h.id
        JOIN user_register_form u ON r.user_id = u.id
        ORDER BY r.review_date DESC
    ');
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $reviews = [];
    $message = 'Error loading reviews: ' . $e->getMessage();
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .admin-navbar {
            width: 100%;
            background: #1a2332;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            min-height: 54px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.04);
        }
        .admin-navbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-navbar-link {
            color: #fff;
            font-weight: 500;
            font-size: 1.08rem;
            padding: 8px 22px;
            border-radius: 8px;
            text-decoration: none;
            margin: 0 2px;
            transition: background 0.18s, color 0.18s;
        }
        .admin-navbar-link.active, .admin-navbar-link:focus {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
        }
        .admin-navbar-link:hover:not(.active) {
            background: #222e44;
            color: #fff;
        }
        .admin-navbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .admin-navbar-right .welcome-user {
            color: #fff;
            font-size: 1.01rem;
            margin-right: 8px;
        }
        .admin-header-box {
            background: #2563eb;
            border-radius: 32px;
            margin: 36px auto 32px auto;
            max-width: 1200px;
            padding: 48px 24px 36px 24px;
            text-align: center;
            color: #fff;
            box-shadow: 0 4px 32px rgba(44, 62, 80, 0.10);
        }
        .admin-header-box h1 {
            font-size: 2.6rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .admin-header-box p {
            font-size: 1.18rem;
            font-weight: 400;
            margin: 0;
        }
        @media (max-width: 900px) {
            .admin-header-box { padding: 24px 2vw 18px 2vw; }
            .admin-header-box h1 { font-size: 1.5rem; }
        }
        .admin-reviews-container {
            max-width: 1200px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(44, 62, 80, 0.10);
            padding: 36px 32px 32px 32px;
        }
        .admin-reviews-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .admin-reviews-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2563eb;
            margin: 0;
        }
        .admin-reviews-header .total-count {
            background: #2563eb;
            color: #fff;
            border-radius: 20px;
            padding: 8px 22px;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        .reviews-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }
        .reviews-table th, .reviews-table td {
            padding: 16px 12px;
            text-align: left;
        }
        .reviews-table th {
            background: #f5f8fa;
            color: #2d3a4b;
            font-weight: 700;
            font-size: 1.08rem;
            border-bottom: 2px solid #e0eafc;
        }
        .reviews-table tr {
            background: #f8fbfd;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.04);
            transition: box-shadow 0.2s, background 0.2s;
        }
        .reviews-table tr:hover {
            background: #eaf3fa;
            box-shadow: 0 4px 16px rgba(44,62,80,0.10);
        }
        .reviews-table td {
            color: #34495e;
            font-size: 1rem;
            border-bottom: none;
            vertical-align: top;
        }
        .rating-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(243,156,18,0.08);
        }
        .rating-badge[data-rating^="4"], .rating-badge[data-rating^="5"] {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }
        .rating-badge[data-rating^="1"], .rating-badge[data-rating^="2"] {
            background: linear-gradient(135deg, #e74c3c, #e67e22);
        }
        .review-comment {
            max-width: 340px;
            max-height: 80px;
            overflow-y: auto;
            background: #fff;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.98rem;
            color: #2d3a4b;
            box-shadow: 0 1px 4px rgba(44,62,80,0.04);
        }
        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #e67e22);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 2px 8px rgba(231,76,60,0.08);
        }
        .delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #e67e22);
            transform: translateY(-2px) scale(1.04);
        }
        @media (max-width: 900px) {
            .admin-reviews-container { padding: 16px 2vw; }
            .reviews-table th, .reviews-table td { padding: 10px 4px; font-size: 0.95rem; }
        }
        @media (max-width: 600px) {
            .admin-reviews-header { flex-direction: column; gap: 10px; align-items: flex-start; }
            .admin-reviews-header h1 { font-size: 1.3rem; }
            .reviews-table th, .reviews-table td { font-size: 0.85rem; }
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="admin-navbar-left">
            <a href="admin_dashboard.php" class="admin-navbar-link">Dashboard</a>
            <a href="admin_users.php" class="admin-navbar-link">Users</a>
            <a href="admin_bookings.php" class="admin-navbar-link">Bookings</a>
            <a href="admin_reviews.php" class="admin-navbar-link active">Reviews</a>
            <a href="admin_hotels.php" class="admin-navbar-link">Hotels</a>
            <a href="admin_lets_chat.php" class="admin-navbar-link">Let's Chat</a>
        </div>
        <div class="admin-navbar-right">
            <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Admin)</span>
            <a href="homepage.php" class="admin-navbar-link">View Site</a>
            <a href="logout.php" class="admin-navbar-link">Logout</a>
        </div>
    </nav>
    <div class="admin-header-box">
        <h1>Manage Reviews</h1>
        <p>View and moderate hotel reviews</p>
    </div>
    <div class="admin-reviews-container">
        <div class="admin-reviews-header">
            <span class="total-count">Total: <?php echo count($reviews); ?></span>
        </div>
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <table class="reviews-table">
            <thead>
                <tr>
                    <th>Hotel</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr><td colspan="6" style="text-align:center;">No reviews found.</td></tr>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['hotel_name']); ?></td>
                            <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                            <td><span class="rating-badge" data-rating="<?php echo (int)$review['rating']; ?>">⭐ <?php echo htmlspecialchars($review['rating']); ?>/5</span></td>
                            <td>
                                <div class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></div>
                                <?php if (!empty($review['image'])): ?>
                                    <div style="margin-top:8px;">
                                        <img src="uploads/review_images/<?php echo htmlspecialchars($review['image']); ?>" alt="Review Image" style="max-width:120px;max-height:90px;border-radius:8px;box-shadow:0 2px 8px rgba(44,62,80,0.10);cursor:pointer;" onclick="showImageModal(this.src)">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($review['review_date'])); ?></td>
                            <td>
                                <a href="admin_reviews.php?delete=<?php echo $review['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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
</body>
</html> 