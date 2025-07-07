<?php
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$to = 'sauravshah1194@gmail.com'; // <-- Replace with your email address for testing
$name = 'Test User';
$subject = 'PHPMailer Test - PahunaGhar';
$body = "This is a test email sent from PHPMailer on your PahunaGhar server.\nIf you receive this, your SMTP settings are working!";

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'pahunaghar76@gmail.com'; // Your Gmail address
    $mail->Password = 'ecgk wujk owbs orpr';     // Your Gmail app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('pahunaghar76@gmail.com', 'PahunaGhar');
    $mail->addAddress($to, $name);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();
    echo '<div style="color:green;font-weight:bold;">Test email sent successfully to ' . htmlspecialchars($to) . '!</div>';
} catch (Exception $e) {
    echo '<div style="color:red;font-weight:bold;">Mailer Error: ' . htmlspecialchars($mail->ErrorInfo) . '</div>';
} 