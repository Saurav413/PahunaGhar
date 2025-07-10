<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'user_config.php'; // Use the correct config file

$msg = '';
$msg_type = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    try {
        $stmt = $user_pdo->prepare("SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($reset) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPassword = $_POST['password'];
                if (strlen($newPassword) < 6) {
                    $msg = "Password must be at least 6 characters.";
                    $msg_type = 'error';
                } else {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    // Update user password
                    $stmt2 = $user_pdo->prepare("UPDATE user_register_form SET password=? WHERE email=?");
                    $stmt2->execute([$hashed, $reset['email']]);
                    // Delete token
                    $stmt3 = $user_pdo->prepare("DELETE FROM password_resets WHERE email=?");
                    $stmt3->execute([$reset['email']]);
                    $msg = "Password reset successful! <a href='login.php'>Login</a>";
                    $msg_type = 'success';
                }
            }
        } else {
            $msg = "Invalid or expired token.";
            $msg_type = 'error';
        }
    } catch (PDOException $e) {
        $msg = "Database error: " . $e->getMessage();
        $msg_type = 'error';
    }
} else {
    $msg = "No token provided.";
    $msg_type = 'error';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container login-container">
        <h2 class="login-title">Reset Password</h2>
        <?php if (isset($reset) && $reset && !$msg): ?>
        <form method="post" class="login-form-styled">
            <label for="password">New Password:</label>
            <input type="password" name="password" required>
            <button type="submit" class="login-btn">Reset Password</button>
        </form>
        <?php endif; ?>
        <?php if ($msg): ?>
            <div class="<?php echo $msg_type === 'error' ? 'login-error' : ($msg_type === 'success' ? 'login-success' : ''); ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <div class="login-register">
            <a href="login.php" class="register-link">Back to Login</a>
        </div>
    </div>
</body>
</html>