<!-- forgot_password.php -->
<?php
require 'config.php'; // Your DB connection

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if ($email) {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM user_register_form WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            // Store token
            $stmt2 = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt2->execute([$email, $token, $expires]);
            // Send email (use PHPMailer for production)
            require 'PHPMailer-6.10.0/src/PHPMailer.php';
            require 'PHPMailer-6.10.0/src/SMTP.php';
            require 'PHPMailer-6.10.0/src/Exception.php';
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;
            
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration (update with your SMTP server details)
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com'; // SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com'; // SMTP username
                $mail->Password = 'your_email_password'; // SMTP password
                $mail->SMTPSecure = 'tls'; // or 'ssl'
                $mail->Port = 587; // or 465 for SSL
                $mail->setFrom('your_email@example.com', 'PahunaGhar');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
                $resetLink = "http://{$_SERVER['HTTP_HOST']}/reset_password.php?token=$token";
                $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password. If you did not request this, please ignore this email.";
                $mail->AltBody = "Copy and paste this link in your browser to reset your password: $resetLink";
                $mail->send();
            } catch (Exception $e) {
                // Optionally log error: $mail->ErrorInfo
            }
        }
        $msg = "If your email is registered, a reset link has been sent.";
    } else {
        $msg = "Please enter your email address.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container login-container">
        <h2>Forgot Password</h2>
        <form method="post">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>