<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'user_config.php';

$success = '';
$error = '';

// Only allow existing admins to register new admins
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin_role = $_POST['admin_role'] ?? 'admin';

    // Basic validation
    if (!$name || !$email || !$password || !$confirm_password) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($admin_role, ['admin', 'super_admin'])) {
        $error = 'Invalid admin role.';
    } else {
        try {
            // Check if email already exists in admin table
            $stmt = $user_pdo->prepare('SELECT id FROM admin_register_form WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered as admin.';
            } else {
                // Check if email exists in user table
                $stmt = $user_pdo->prepare('SELECT id FROM user_register_form WHERE email = ?');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email already registered as regular user.';
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    // Insert admin
                    $stmt = $user_pdo->prepare('INSERT INTO admin_register_form (name, email, password, admin_role) VALUES (?, ?, ?, ?)');
                    if ($stmt->execute([$name, $email, $hashed_password, $admin_role])) {
                        $success = 'Admin account created successfully!';
                    } else {
                        $error = 'Registration failed: ' . implode(' ', $stmt->errorInfo());
                    }
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
    <title>Admin Registration - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="admin_dashboard.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span> Admin</a>
        </div>
        <div class="navbar-right">
            <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
    </nav>
    <div class="container register-container">
        <h2 class="register-title">Create Admin Account</h2>
        <form class="register-form register-form-styled" method="post" action="">
            <?php if ($error): ?>
                <div class="register-error"><?php echo $error; ?></div>
            <?php elseif ($success): ?>
                <div class="register-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <label for="name">Admin Name</label>
            <input type="text" id="name" name="name" required placeholder="Admin Name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="admin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <label for="admin_role">Admin Role</label>
            <select id="admin_role" name="admin_role" required>
                <option value="admin" selected>Admin</option>
                <option value="super_admin">Super Admin</option>
            </select>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Password">

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">

            <button type="submit" class="register-btn">Create Admin Account</button>
        </form>
        <p class="register-login">Back to <a href="admin_dashboard.php" class="login-link">Dashboard</a></p>
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