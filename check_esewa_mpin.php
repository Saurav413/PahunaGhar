<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/mpin_errors.log');

// Log debugging information
error_log('=== eSewa MPIN Check Debug ===');
error_log('Session eSewa ID: ' . ($_SESSION['esewa_id'] ?? 'NOT SET'));
error_log('Session logged_in: ' . ($_SESSION['logged_in'] ?? 'NOT SET'));
error_log('POST mpin: ' . ($_POST['mpin'] ?? 'NOT SET'));
error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    error_log('User not logged in - returning fail');
    echo 'fail';
    exit;
}

$esewa_id = $_SESSION['esewa_id'] ?? null;
$mpin = $_POST['mpin'] ?? '';
$booking_id = $_POST['booking_id'] ?? null;

error_log('Processing MPIN check - eSewa ID: ' . $esewa_id . ', MPIN: ' . $mpin);

if ($esewa_id && $mpin) {
    try {
        $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
        $stmt->execute([$esewa_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log('Database query result: ' . ($row ? 'User found' : 'User not found'));
        if ($row) {
            error_log('Stored MPIN: ' . $row['mpin'] . ', Input MPIN: ' . $mpin);
            error_log('MPIN match: ' . ($row['mpin'] === $mpin ? 'YES' : 'NO'));
        }
        
        if ($row && $row['mpin'] === $mpin) {
            error_log('MPIN verification SUCCESS');
            if ($booking_id) {
                $_SESSION['can_confirm_booking'] = $booking_id;
            }
            echo 'success';
            exit;
        } else {
            error_log('MPIN verification FAILED');
        }
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }
} else {
    error_log('Missing eSewa ID or MPIN - eSewa ID: ' . ($esewa_id ? 'SET' : 'NOT SET') . ', MPIN: ' . ($mpin ? 'SET' : 'NOT SET'));
}

error_log('Returning fail');
echo 'fail';
?> 