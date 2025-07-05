<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/mpin_errors.log');

// Log debugging information
error_log('=== Khalti MPIN Check Debug ===');
error_log('Session Khalti ID: ' . ($_SESSION['khalti_id'] ?? 'NOT SET'));
error_log('Session logged_in: ' . ($_SESSION['logged_in'] ?? 'NOT SET'));
error_log('POST mpin: ' . ($_POST['mpin'] ?? 'NOT SET'));
error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    error_log('User not logged in - returning fail');
    echo 'fail';
    exit;
}

$khalti_id = $_SESSION['khalti_id'] ?? null;
$mpin = $_POST['mpin'] ?? '';

error_log('Processing MPIN check - Khalti ID: ' . $khalti_id . ', MPIN: ' . $mpin);

if ($khalti_id && $mpin) {
    try {
        $stmt = $pdo->prepare("SELECT mpin FROM khalti_users WHERE khalti_id = ?");
        $stmt->execute([$khalti_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log('Database query result: ' . ($row ? 'User found' : 'User not found'));
        if ($row) {
            error_log('Stored MPIN: ' . $row['mpin'] . ', Input MPIN: ' . $mpin);
            error_log('MPIN match: ' . ($row['mpin'] === $mpin ? 'YES' : 'NO'));
        }
        
        if ($row && $row['mpin'] === $mpin) {
            error_log('MPIN verification SUCCESS');
            echo 'success';
            exit;
        } else {
            error_log('MPIN verification FAILED');
        }
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }
} else {
    error_log('Missing Khalti ID or MPIN - Khalti ID: ' . ($khalti_id ? 'SET' : 'NOT SET') . ', MPIN: ' . ($mpin ? 'SET' : 'NOT SET'));
}

error_log('Returning fail');
echo 'fail'; 