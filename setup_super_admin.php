<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'user_config.php';

$success = '';
$error = '';

// Check if super admin already exists
try {
    $stmt = $user_pdo->prepare('SELECT COUNT(*) as count FROM admin_register_form WHERE admin_role = "super_admin"');
    $stmt->execute();
    $superAdminCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($superAdminCount > 0) {
        $error = 'Super admin account already exists. This script can only be run once.';
    }
} catch (Exception $e) {
    $error = 'Database error: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (!$name || !$email || !$password || !$confirm_password) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        try {
            // Check if email already exists
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
                    // Insert super admin
                    $stmt = $user_pdo->prepare('INSERT INTO admin_register_form (name, email, password, admin_role, is_active) VALUES (?, ?, ?, "super_admin", 1)');
                    if ($stmt->execute([$name, $email, $hashed_password])) {
                        $success = 'Super admin account created successfully! You can now login at super_admin_login.php';
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
    <title>Setup Super Admin - PahunaGhar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="register.css">
    <style>
        .setup-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
        }
        .setup-title {
            text-align: center;
            color: #ff6b6b;
            margin-bottom: 30px;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success-box {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .setup-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .setup-btn:hover {
            background: linear-gradient(135deg, #ee5a24, #ff6b6b);
        }
        .setup-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="homepage.php" class="logo logo-blue">Pahuna<span class="logo-highlight">Ghar</span></a>
        </div>
        <div class="navbar-right">
            <a href="homepage.php" class="nav-link">View Site</a>
        </div>
    </nav>
    
    <div class="setup-container">
        <h2 class="setup-title">Setup Super Admin Account</h2>
        
        <?php if ($error): ?>
            <div class="warning-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-box"><?php echo htmlspecialchars($success); ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="super_admin_login.php" class="setup-btn" style="text-decoration: none; display: inline-block;">Go to Super Admin Login</a>
            </div>
        <?php else: ?>
            <div class="warning-box">
                <strong>⚠️ Important:</strong> This script can only be run once to create the first super admin account. 
                After creating the super admin, you can manage additional admin accounts through the super admin dashboard.
            </div>
            
            <form class="register-form register-form-styled" method="post" action="">
                <label for="name">Super Admin Name</label>
                <input type="text" id="name" name="name" required placeholder="Super Admin Name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="superadmin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Password (min 8 characters)">

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">

                <button type="submit" class="setup-btn">Create Super Admin Account</button>
            </form>
        <?php endif; ?>
    </div>
    
    <footer class="register-footer">
        © 2025 <span class="footer-highlight">PahunaGhar</span>. All rights reserved.
    </footer>
    
    <script>
    // Client-side password match validation
    document.querySelector('.register-form')?.addEventListener('submit', function(e) {
        var pwd = document.getElementById('password').value;
        var cpwd = document.getElementById('confirm_password').value;
        if (pwd !== cpwd) {
            alert('Passwords do not match!');
            e.preventDefault();
        }
        if (pwd.length < 8) {
            alert('Password must be at least 8 characters long!');
            e.preventDefault();
        }
    });
    </script>
</body>
</html> 