<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'user_config.php';

$error = '';
$success = '';

// Check for registration success message from session
if (isset($_SESSION['super_admin_registration_success'])) {
    $success = $_SESSION['super_admin_registration_success'];
    unset($_SESSION['super_admin_registration_success']); // Clear the message after displaying
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        try {
            // Check if super admin exists and verify password
            $stmt = $user_pdo->prepare('SELECT id, name, email, password, admin_role, is_active FROM admin_register_form WHERE email = ? AND admin_role = "super_admin"');
            $stmt->execute([$email]);
            $super_admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($super_admin && password_verify($password, $super_admin['password'])) {
                // Check if super admin account is active
                if (!$super_admin['is_active']) {
                    $error = 'Super admin account is deactivated.';
                } else {
                    // Update last login time
                    $updateStmt = $user_pdo->prepare('UPDATE admin_register_form SET last_login = CURRENT_TIMESTAMP WHERE id = ?');
                    $updateStmt->execute([$super_admin['id']]);
                    
                    // Login successful
                    $_SESSION['admin_id'] = $super_admin['id'];
                    $_SESSION['admin_name'] = $super_admin['name'];
                    $_SESSION['admin_email'] = $super_admin['email'];
                    $_SESSION['admin_role'] = $super_admin['admin_role'];
                    $_SESSION['user_id'] = $super_admin['id']; // For compatibility
                    $_SESSION['user_name'] = $super_admin['name']; // For compatibility
                    $_SESSION['user_email'] = $super_admin['email']; // For compatibility
                    $_SESSION['user_type'] = 'super_admin'; // For compatibility
                    $_SESSION['logged_in'] = true;
                    
                    $success = 'Super Admin login successful! Welcome back, ' . $super_admin['name'] . '.';
                    
                    // Redirect to super admin dashboard
                    header('Location: super_admin_dashboard.php');
                    exit;
                }
            } else {
                if (!$super_admin) {
                    $error = 'Super admin account not found. Please check your email.';
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
    <title>Super Admin Login - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        .super-admin-badge {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }
        .login-title {
            color: #ff6b6b;
        }
        .login-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        }
        .login-btn:hover {
            background: linear-gradient(135deg, #ee5a24, #ff6b6b);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span> <span class="super-admin-badge">Super Admin</span></a>
        </div>
        <div class="navbar-right">
            <a href="admin_login.php" class="nav-link">Admin Login</a>
            <a href="login.php" class="nav-link">User Login</a>
            <a href="homepage.php" class="nav-link">View Site</a>
        </div>
    </nav>
    <div class="container login-container">
        <h2 class="login-title">Super Admin Login</h2>
        <form class="login-form login-form-styled" method="post" action="">
            <?php if ($error): ?>
                <div class="login-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="login-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <label for="email">Super Admin Email</label>
            <input type="email" id="email" name="email" required placeholder="superadmin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Password">

            <button type="submit" class="login-btn">Super Admin Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0eafc;">
            <p style="margin-bottom: 15px; color: #6b7280; font-size: 0.9rem;">Need regular admin access?</p>
            <a href="admin_login.php" class="login-btn" style="background: linear-gradient(135deg, #3498db, #2980b9); text-decoration: none; display: inline-block; padding: 12px 24px; border-radius: 8px; color: white; font-weight: 600; transition: transform 0.2s;">Admin Login</a>
        </div>
        
        <p class="login-register">Need help? <a href="homepage.php" class="register-link">Contact Support</a></p>
    </div>
    <footer class="login-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 