<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/mpin_errors.log');

error_log('=== Update Booking Status Debug ===');
error_log('Session user_id: ' . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log('Session can_confirm_booking: ' . ($_SESSION['can_confirm_booking'] ?? 'NOT SET'));
error_log('Session logged_in: ' . ($_SESSION['logged_in'] ?? 'NOT SET'));
error_log('POST booking_id: ' . ($_POST['booking_id'] ?? 'NOT SET'));

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    error_log('User not logged in - returning fail');
    echo 'fail';
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;

// Only allow if session flag matches booking_id
if (!isset($_SESSION['can_confirm_booking']) || $_SESSION['can_confirm_booking'] != $booking_id) {
    error_log('Session flag can_confirm_booking missing or does not match booking_id - returning fail');
    echo 'fail';
    exit;
}

if ($booking_id > 0 && $user_id > 0) {
    // First check if the booking exists and belongs to the user
    $stmt = $pdo->prepare("SELECT id, status FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($booking) {
        error_log('Booking found. Current status: ' . $booking['status']);
        // Update the booking status to confirmed
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $user_id]);
        unset($_SESSION['can_confirm_booking']); // Clear the flag after use
        error_log('Update query rowCount: ' . $stmt->rowCount());
        if ($stmt->rowCount() > 0) {
            error_log('Booking status updated to confirmed - SUCCESS');
            echo 'success';
            exit;
        } else {
            error_log('Booking status not updated (possibly already confirmed) - returning fail');
        }
    } else {
        error_log('Booking not found or does not belong to user - returning fail');
    }
}
error_log('Returning fail');
echo 'fail'; 