<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// Handle delete action
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contact WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = 'Let\'s Chat message deleted successfully.';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting Let\'s Chat message.';
        $messageType = 'error';
    }
}

// Get all contact messages
try {
    $stmt = $pdo->query("SELECT * FROM contact ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $contacts = [];
    $message = 'Error loading Let\'s Chat messages.';
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Let's Chat - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_lets_chat.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-nav-content">
            <div>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_users.php">Users</a>
                <a href="admin_bookings.php">Bookings</a>
                <a href="admin_reviews.php">Reviews</a>
                <a href="admin_hotels.php">Hotels</a>
                <a href="admin_lets_chat.php" class="active">Let's Chat</a>
            </div>
            <div>
                <a href="homepage.php">View Site</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Let's Chat Messages</h1>
            <p>View and manage Let's Chat form submissions</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="contacts-section">
            <h2 class="section-title">Let's Chat Messages (<?php echo count($contacts); ?>)</h2>
            
            <?php if (empty($contacts)): ?>
                <div class="no-data">
                    <p>No Let's Chat messages found.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td>
                                    <div class="contact-info">
                                        <div class="contact-name"><?php echo htmlspecialchars($contact['name']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-email"><?php echo htmlspecialchars($contact['email']); ?></div>
                                </td>
                                <td class="message-cell">
                                    <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
                                </td>
                                <td>
                                    <div class="contact-date">
                                        <?php echo date('M j, Y g:i A', strtotime($contact['created_at'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <form method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this Let\'s Chat message?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $contact['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 