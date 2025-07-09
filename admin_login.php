<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'user_config.php';
// Redirect super admin to their dashboard if logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'super_admin') {
    header('Location: super_admin_dashboard.php');
    exit;
}

$error = '';
$success = '';

// Check for registration success message from session
if (isset($_SESSION['admin_registration_success'])) {
    $success = $_SESSION['admin_registration_success'];
    unset($_SESSION['admin_registration_success']); // Clear the message after displaying
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        try {
            // Check if admin exists and verify password
            $stmt = $user_pdo->prepare('SELECT id, name, email, password, admin_role, is_active FROM admin_register_form WHERE email = ? AND admin_role = "admin"');
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug information (remove this in production)
            if (isset($_GET['debug']) && $_GET['debug'] === '1') {
                echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc;'>";
                echo "<strong>Debug Info:</strong><br>";
                echo "Email entered: " . htmlspecialchars($email) . "<br>";
                echo "Admin found: " . ($admin ? 'Yes' : 'No') . "<br>";
                if ($admin) {
                    echo "Admin ID: " . $admin['id'] . "<br>";
                    echo "Admin Name: " . htmlspecialchars($admin['name']) . "<br>";
                    echo "Admin Role: " . htmlspecialchars($admin['admin_role']) . "<br>";
                    echo "Admin Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
                }
                echo "</div>";
            }
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Check if admin account is active
                if (!$admin['is_active']) {
                    $error = 'Admin account is deactivated. Please contact super admin.';
                } else {
                    // Update last login time
                    $updateStmt = $user_pdo->prepare('UPDATE admin_register_form SET last_login = CURRENT_TIMESTAMP WHERE id = ?');
                    $updateStmt->execute([$admin['id']]);
                    
                    // Login successful
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_role'] = $admin['admin_role'];
                    $_SESSION['user_id'] = $admin['id']; // For compatibility
                    $_SESSION['user_name'] = $admin['name']; // For compatibility
                    $_SESSION['user_email'] = $admin['email']; // For compatibility
                    $_SESSION['user_type'] = 'admin'; // For compatibility
                    $_SESSION['logged_in'] = true;
                    
                    $success = 'Login successful! Welcome back, ' . $admin['name'] . '.';
                    
                    // Redirect to admin dashboard
                    header('Location: admin_dashboard.php');
                    exit;
                }
            } else {
                if (!$admin) {
                    $error = 'Admin account not found. Please check your email.';
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
    <title>Admin Login - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span> Admin</a>
        </div>
        <div class="navbar-right">
            <a href="super_admin_login.php" class="nav-link">Super Admin</a>
            <a href="login.php" class="nav-link">User Login</a>
            <a href="homepage.php" class="nav-link">View Site</a>
        </div>
    </nav>
    <div class="container login-container">
        <h2 class="login-title">Admin Login</h2>
        <form class="login-form login-form-styled" method="post" action="">
            <?php if ($error): ?>
                <div class="login-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="login-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <label for="email">Admin Email</label>
            <input type="email" id="email" name="email" required placeholder="admin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Password">

            <button type="submit" class="login-btn">Admin Login</button>
        </form>
        <div style="text-align:center; margin-top:12px; margin-bottom:12px;">
            <span style="color:#888; font-size:1.1em;">Super Admin?</span>
            <a href="super_admin_login.php" style="color:#2563eb; font-size:1.1em; text-decoration:underline; margin-left:4px;">Use Super Admin Login</a>
        </div>
        
        <p class="login-register">Need help? <a href="homepage.php" class="register-link">Contact Support</a></p>
    </div>
    <footer class="login-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
</body>
</html> 