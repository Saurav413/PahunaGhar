<?php
// Debug script for MPIN verification issues
require_once 'config.php';

echo "<h2>🔍 MPIN Issue Debug</h2>";

// Check current session
echo "<h3>Current Session Status:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if user is logged in to main system
echo "<h3>Main System Login Status:</h3>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "✅ Logged in to main system<br>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
} else {
    echo "❌ NOT logged in to main system<br>";
}

// Check payment gateway session
echo "<h3>Payment Gateway Session:</h3>";
echo "eSewa ID: " . ($_SESSION['esewa_id'] ?? 'NOT SET') . "<br>";
echo "Khalti ID: " . ($_SESSION['khalti_id'] ?? 'NOT SET') . "<br>";

// Check database data
echo "<h3>Database Data:</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM esewa_users");
    $esewa_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>eSewa Users:</strong><br>";
    foreach ($esewa_users as $user) {
        echo "ID: " . $user['esewa_id'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    $stmt = $pdo->query("SELECT * FROM khalti_users");
    $khalti_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>Khalti Users:</strong><br>";
    foreach ($khalti_users as $user) {
        echo "ID: " . $user['khalti_id'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test MPIN verification directly
echo "<h3>Direct MPIN Test:</h3>";
$test_esewa_id = "9824004077";
$test_mpin = "5470";

try {
    $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
    $stmt->execute([$test_esewa_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "✅ Found user with eSewa ID: " . $test_esewa_id . "<br>";
        echo "Stored MPIN: " . $row['mpin'] . "<br>";
        echo "Test MPIN: " . $test_mpin . "<br>";
        
        if ($row['mpin'] === $test_mpin) {
            echo "✅ MPIN match: SUCCESS<br>";
        } else {
            echo "❌ MPIN match: FAILED<br>";
            echo "Expected: " . $row['mpin'] . ", Got: " . $test_mpin . "<br>";
        }
    } else {
        echo "❌ No user found with eSewa ID: " . $test_esewa_id . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Check error logs
echo "<h3>Error Logs:</h3>";
$log_file = __DIR__ . '/mpin_errors.log';
if (file_exists($log_file)) {
    echo "<strong>Recent MPIN error logs:</strong><br>";
    $logs = file_get_contents($log_file);
    $lines = explode("\n", $logs);
    $recent_lines = array_slice($lines, -10); // Last 10 lines
    foreach ($recent_lines as $line) {
        if (trim($line)) {
            echo htmlspecialchars($line) . "<br>";
        }
    }
} else {
    echo "No error log file found.<br>";
}

// Provide solutions
echo "<h3>🔧 Solutions:</h3>";
echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;border:1px solid #ffeaa7;'>";

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "<strong>Step 1: Login to Main System</strong><br>";
    echo "You need to login to the main PahunaGhar system first.<br>";
    echo "<a href='login.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Go to Main Login</a><br><br>";
}

if (!isset($_SESSION['esewa_id'])) {
    echo "<strong>Step 2: Login to eSewa</strong><br>";
    echo "After logging into main system, you need to login to eSewa.<br>";
    echo "<a href='esewa_login.php' style='background:#28a745;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Go to eSewa Login</a><br><br>";
}

echo "<strong>Test Credentials:</strong><br>";
echo "Main System: Any valid user<br>";
echo "eSewa: ID=9824004077, Password=password<br>";
echo "MPIN: 5470<br><br>";

echo "<strong>Complete Flow:</strong><br>";
echo "1. Login to main system (login.php)<br>";
echo "2. Login to eSewa (esewa_login.php)<br>";
echo "3. Go to payment page<br>";
echo "4. Click 'Pay Via eSewa'<br>";
echo "5. Enter MPIN: 5470<br>";
echo "</div>";

// Quick fix button
echo "<h3>🚀 Quick Fix:</h3>";
echo "<form method='post'>";
echo "<input type='submit' name='set_test_session' value='Set Test Session (for debugging)' style='background:#dc3545;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";

// Handle quick fix
if (isset($_POST['set_test_session'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['esewa_id'] = '9824004077';
    echo "<script>alert('Test session set! Refresh the page to see changes.');</script>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
h2, h3 {
    color: #333;
}
pre {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
</style> 