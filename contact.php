<?php
session_start();
require_once 'config.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message_text)) {
        $message = 'Please fill in all fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message_text]);
            
            $message = 'Thank you for your message! We will get back to you soon.';
            $messageType = 'success';
            
            // Clear form data after successful submission
            $name = $email = $message_text = '';
        } catch (PDOException $e) {
            $message = 'Sorry, there was an error sending your message. Please try again.';
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
    <title>Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span></a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="#" class="nav-link">Listing</a>
            <?php endif; ?>
            <a href="contact.php" class="nav-link">Contact</a>
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
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                    <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container contact-container">
        <h2 class="contact-title">Contact Us</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form class="contact-form" method="post" action="contact.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required placeholder="Your Name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required placeholder="Your message..."><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
            </div>

            <button type="submit">Send Message</button>
        </form>
        <p class="contact-info">We'll get back to you as soon as possible.</p>
    </div>
    <footer class="contact-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>

    <script>
        // Search functionality for contact page
        document.addEventListener('DOMContentLoaded', function() {
            // Search button click
            document.getElementById('searchBtn').addEventListener('click', function() {
                const searchTerm = document.getElementById('searchInput').value.trim();
                if (searchTerm) {
                    // Redirect to homepage with search
                    window.location.href = 'homepage.php?search=' + encodeURIComponent(searchTerm);
                }
            });

            // Search on Enter key
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.trim();
                    if (searchTerm) {
                        // Redirect to homepage with search
                        window.location.href = 'homepage.php?search=' + encodeURIComponent(searchTerm);
                    }
                }
            });
        });
    </script>
</body>
</html> 