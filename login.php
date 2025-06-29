<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'user_config.php';

$error = '';
$success = '';
$redirect = $_GET['redirect'] ?? '';

// Check for registration success message from session
if (isset($_SESSION['registration_success'])) {
    $success = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']); // Clear the message after displaying
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        try {
            // Check if user exists and verify password
            $stmt = $user_pdo->prepare('SELECT id, name, email, password, user_type FROM register_form WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Restrict admin login to only the main admin account
                if ($user['user_type'] === 'admin' && strtolower($user['email']) !== 'admin@pahunaghar.com') {
                    $error = 'Only the main admin account can log in as admin.';
                } else {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['logged_in'] = true;
                    
                    $success = 'Login successful! Welcome back, ' . $user['name'] . '.';
                    
                    // Redirect based on user type and redirect parameter
                    if ($user['user_type'] === 'admin') {
                        header('Location: admin_dashboard.php');
                    } else {
                        // Check if there's a redirect parameter
                        if ($redirect === 'booking' && isset($_GET['hotel_id'])) {
                            header('Location: booking.php?id=' . $_GET['hotel_id']);
                        } else {
                            header('Location: homepage.php');
                        }
                    }
                    exit;
                }
            } else {
                if (!$user) {
                    $error = 'Incorrect email. Please try again.';
                } else {
                    $error = 'Password verification failed.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span></a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <?php if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin'): ?>
                    <a href="PahunaGhar/user_bookings.php" class="nav-link">My Bookings</a>
                <?php endif; ?>
                <a href="lets_chat.php" class="nav-link">Let's Chat</a>
            <?php endif; ?>
        </div>
        <div class="navbar-center">
            <div class="search-bar">
                <input type="text" placeholder="Search destinations, hotels, or prices...">
                <button>Search</button>
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
    <div class="container login-container">
        <h2 class="login-title">Login to Your Account</h2>
        <form class="login-form login-form-styled" method="post" action="">
            <?php if ($error): ?>
                <div class="login-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="login-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Password">

            <button type="submit" class="login-btn">Login</button>
        </form>
        <p class="login-register">Don't have an account? <a href="register.php" class="register-link">Register</a></p>
    </div>
    <footer class="login-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 