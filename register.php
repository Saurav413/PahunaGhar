<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'user_config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (!$name || !$email || !$password || !$confirm_password) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Check if email already exists
            $stmt = $user_pdo->prepare('SELECT id FROM register_form WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered.';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Insert user with default user_type 'customer'
                $stmt = $user_pdo->prepare('INSERT INTO register_form (name, email, password, user_type) VALUES (?, ?, ?, ?)');
                if ($stmt->execute([$name, $email, $hashed_password, 'customer'])) {
                    // Set success message in session and redirect to login page
                    $_SESSION['registration_success'] = 'Registration successful! Please login with your new account.';
                    header('Location: login.php');
                    exit();
                } else {
                    $error = 'Registration failed: ' . implode(' ', $stmt->errorInfo());
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
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="register.css">
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
    <div class="container register-container">
        <h2 class="register-title">Create an Account</h2>
        <form class="register-form register-form-styled" method="post" action="">
            <?php if ($error): ?>
                <div class="register-error"><?php echo $error; ?></div>
            <?php elseif ($success): ?>
                <div class="register-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required placeholder="Your Name">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Password">

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">

            <button type="submit" class="register-btn">Register</button>
        </form>
        <p class="register-login">Already have an account? <a href="login.php" class="login-link">Login</a></p>
    </div>
    <footer class="register-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
    <script>
    // Client-side password match validation
    document.querySelector('.register-form').addEventListener('submit', function(e) {
        var pwd = document.getElementById('password').value;
        var cpwd = document.getElementById('confirm_password').value;
        if (pwd !== cpwd) {
            alert('Passwords do not match!');
            e.preventDefault();
        }
    });
    </script>
</body>
</html> 