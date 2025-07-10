<!-- forgot_password.php -->
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'user_config.php'; // Use the correct config file

// Check if email config exists, if not, use fallback mode
$email_config_exists = file_exists('email_config.php');
if ($email_config_exists) {
    require 'email_config.php'; // Email configuration
}

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if ($email) {
        try {
            // Check if user exists
            $stmt = $user_pdo->prepare("SELECT * FROM user_register_form WHERE email=?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+8 hours'));
                
                // Store token
                $stmt2 = $user_pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                $stmt2->execute([$email, $token, $expires]);
                
                                // Check if email configuration is available and properly configured
                if ($email_config_exists && file_exists('PHPMailer-6.10.0/src/PHPMailer.php') && 
                    strpos(SMTP_USERNAME, 'your_gmail') === false && strpos(SMTP_PASSWORD, 'your_app_password') === false) {
                    
                    // Send email using PHPMailer
                    require 'PHPMailer-6.10.0/src/PHPMailer.php';
                    require 'PHPMailer-6.10.0/src/SMTP.php';
                    require 'PHPMailer-6.10.0/src/Exception.php';
                    
                    // Use PHPMailer classes
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    try {
                        // SMTP configuration for Gmail
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = SMTP_AUTH;
                        $mail->Username = SMTP_USERNAME;
                        $mail->Password = SMTP_PASSWORD;
                        $mail->SMTPSecure = SMTP_SECURE;
                        $mail->Port = SMTP_PORT;
                        $mail->setFrom(FROM_EMAIL, FROM_NAME);
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset - PahunaGhar';
                        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/PahunaGhar-main";
                        $resetLink = "$base_url/reset_password.php?token=$token";
                        $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                                <h2 style='color: #333;'>Password Reset Request</h2>
                                <p>Hello,</p>
                                <p>You have requested to reset your password for your PahunaGhar account.</p>
                                <p>Click the button below to reset your password:</p>
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='$resetLink' style='background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
                                </div>
                                <p>Or copy and paste this link in your browser:</p>
                                <p style='word-break: break-all; color: #666;'>$resetLink</p>
                                <p><strong>This link will expire in 1 hour.</strong></p>
                                <p>If you did not request this password reset, please ignore this email.</p>
                                <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                                <p style='color: #666; font-size: 12px;'>This is an automated message from PahunaGhar. Please do not reply to this email.</p>
                            </div>
                        ";
                        $mail->AltBody = "Password Reset - PahunaGhar\n\nYou have requested to reset your password. Click the following link to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour. If you did not request this, please ignore this email.";
                        
                        $mail->send();
                        $msg = "Reset link has been sent to your email address.";
                        $msg_type = 'success';
                    } catch (\PHPMailer\PHPMailer\Exception $e) {
                        // Log the error for debugging
                        error_log("PHPMailer Error: " . $e->getMessage());
                        
                        // Fallback to showing the link directly
                        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/PahunaGhar-main";
                        $resetLink = "$base_url/reset_password.php?token=$token";
                        $msg = "Email sending failed, but here's your reset link:<br><br>";
                        $msg .= "<strong>Reset Link:</strong> <a href='$resetLink' target='_blank'>$resetLink</a><br><br>";
                        $msg .= "<em>Error: " . $e->getMessage() . "<br>Please configure email settings properly or use the link above.</em>";
                        $msg_type = 'success';
                    }
                } else {
                    // Fallback mode - show reset link directly
                    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/PahunaGhar-main";
                    $resetLink = "$base_url/reset_password.php?token=$token";
                    $msg = "Password reset link generated successfully!<br><br>";
                    $msg .= "<strong>Reset Link:</strong> <a href='$resetLink' target='_blank'>$resetLink</a><br><br>";
                    if (!$email_config_exists) {
                        $msg .= "<em>Email configuration not found. <a href='email_test.php'>Configure email settings</a> to receive reset links via email.</em>";
                    } else {
                        $msg .= "<em>Email configuration incomplete. <a href='email_test.php'>Configure Gmail settings</a> to receive reset links via email.</em>";
                    }
                    $msg_type = 'success';
                }
            } else {
                $msg = "If your email is registered, a reset link has been sent.";
                $msg_type = 'info';
            }
        } catch (PDOException $e) {
            $msg = "Database error: " . $e->getMessage();
            $msg_type = 'error';
        }
    } else {
        $msg = "Please enter your email address.";
        $msg_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container login-container">
        <h2 class="login-title">Forgot Password</h2>
        <form method="post" class="login-form-styled">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" required>
            <button type="submit" class="login-btn">Send Reset Link</button>
        </form>
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