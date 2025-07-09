<?php
require 'config.php';

$msg = '';
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($reset) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'];
            if (strlen($newPassword) < 6) {
                $msg = "Password must be at least 6 characters.";
            } else {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                // Update user password
                $stmt2 = $conn->prepare("UPDATE user_register_form SET password=? WHERE email=?");
                $stmt2->execute([$hashed, $reset['email']]);
                // Delete token
                $stmt3 = $conn->prepare("DELETE FROM password_resets WHERE email=?");
                $stmt3->execute([$reset['email']]);
                $msg = "Password reset successful! <a href='login.php'>Login</a>";
            }
        }
    } else {
        $msg = "Invalid or expired token.";
    }
} else {
    $msg = "No token provided.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container login-container">
        <h2>Reset Password</h2>
        <?php if (isset($reset) && $reset && !$msg): ?>
        <form method="post">
            <label for="password">New Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
        <?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>