<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo 'fail';
    exit;
}

$khalti_id = $_SESSION['khalti_id'] ?? null;
$mpin = $_POST['mpin'] ?? '';

if ($khalti_id && $mpin) {
    $stmt = $pdo->prepare("SELECT mpin FROM user_mpin WHERE user_type = 'khalti' AND user_id = ?");
    $stmt->execute([$khalti_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['mpin'] === $mpin) {
        echo 'success';
        exit;
    }
}
echo 'fail'; 