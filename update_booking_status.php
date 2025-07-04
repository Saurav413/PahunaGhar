<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo 'fail';
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;

if ($booking_id > 0 && $user_id > 0) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ? AND status = 'available'");
    $stmt->execute([$booking_id, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo 'success';
        exit;
    }
}
echo 'fail'; 