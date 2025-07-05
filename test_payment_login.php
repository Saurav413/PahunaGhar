<?php
session_start();
require_once 'config.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/payment_test.log');

echo "<h2>Payment Gateway Login Test</h2>";

// Test 1: Check if user is logged in to main system
echo "<h3>Step 1: Main System Login Status</h3>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "✅ User is logged in to main system<br>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
} else {
    echo "❌ User is NOT logged in to main system<br>";
    echo "<strong>Solution:</strong> You need to login to the main PahunaGhar system first<br>";
    echo "<a href='login.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Go to Main Login</a><br><br>";
}

// Test 2: Check payment gateway session variables
echo "<h3>Step 2: Payment Gateway Session Variables</h3>";
echo "eSewa ID: " . ($_SESSION['esewa_id'] ?? 'NOT SET') . "<br>";
echo "Khalti ID: " . ($_SESSION['khalti_id'] ?? 'NOT SET') . "<br>";

if (!isset($_SESSION['esewa_id']) && !isset($_SESSION['khalti_id'])) {
    echo "❌ No payment gateway session variables set<br>";
    echo "<strong>Solution:</strong> You need to login to a payment gateway first<br><br>";
}

// Test 3: Test eSewa Login
echo "<h3>Step 3: Test eSewa Login</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_esewa'])) {
    $input_id = $_POST['esewa_id'] ?? '';
    $input_password = $_POST['esewa_password'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM esewa_users WHERE esewa_id = ? AND password = ?");
        $stmt->execute([$input_id, $input_password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['esewa_id'] = $input_id;
            echo "✅ eSewa login successful! Session variable set.<br>";
            echo "eSewa ID: " . $_SESSION['esewa_id'] . "<br>";
        } else {
            echo "❌ eSewa login failed. Invalid credentials.<br>";
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
}

// Test 4: Test Khalti Login
echo "<h3>Step 4: Test Khalti Login</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_khalti'])) {
    $input_id = $_POST['khalti_id'] ?? '';
    $input_password = $_POST['khalti_password'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ? AND password = ?");
        $stmt->execute([$input_id, $input_password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['khalti_id'] = $input_id;
            echo "✅ Khalti login successful! Session variable set.<br>";
            echo "Khalti ID: " . $_SESSION['khalti_id'] . "<br>";
        } else {
            echo "❌ Khalti login failed. Invalid credentials.<br>";
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
}

// Test 5: Test MPIN Verification
echo "<h3>Step 5: Test MPIN Verification</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_mpin'])) {
    $mpin = $_POST['mpin'] ?? '';
    $gateway = $_POST['gateway'] ?? '';
    
    if ($gateway === 'esewa' && isset($_SESSION['esewa_id'])) {
        try {
            $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
            $stmt->execute([$_SESSION['esewa_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && $row['mpin'] === $mpin) {
                echo "✅ eSewa MPIN verification successful!<br>";
            } else {
                echo "❌ eSewa MPIN verification failed.<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    } elseif ($gateway === 'khalti' && isset($_SESSION['khalti_id'])) {
        try {
            $stmt = $pdo->prepare("SELECT mpin FROM khalti_users WHERE khalti_id = ?");
            $stmt->execute([$_SESSION['khalti_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && $row['mpin'] === $mpin) {
                echo "✅ Khalti MPIN verification successful!<br>";
            } else {
                echo "❌ Khalti MPIN verification failed.<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ No payment gateway session variable set for " . $gateway . "<br>";
    }
}

// Display test forms
echo "<h3>Test Forms</h3>";

// eSewa Test Form
echo "<div style='background:#f8f9fa;padding:20px;margin:10px 0;border-radius:5px;'>";
echo "<h4>Test eSewa Login</h4>";
echo "<form method='post'>";
echo "eSewa ID: <input type='text' name='esewa_id' value='9824004077' required><br>";
echo "Password: <input type='password' name='esewa_password' value='password' required><br>";
echo "<input type='submit' name='test_esewa' value='Test eSewa Login' style='background:#28a745;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// Khalti Test Form
echo "<div style='background:#f8f9fa;padding:20px;margin:10px 0;border-radius:5px;'>";
echo "<h4>Test Khalti Login</h4>";
echo "<form method='post'>";
echo "Khalti ID: <input type='text' name='khalti_id' value='9824004077' required><br>";
echo "Password: <input type='password' name='khalti_password' value='password' required><br>";
echo "<input type='submit' name='test_khalti' value='Test Khalti Login' style='background:#6f42c1;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// MPIN Test Form
echo "<div style='background:#f8f9fa;padding:20px;margin:10px 0;border-radius:5px;'>";
echo "<h4>Test MPIN Verification</h4>";
echo "<form method='post'>";
echo "Gateway: <select name='gateway'><option value='esewa'>eSewa</option><option value='khalti'>Khalti</option></select><br>";
echo "MPIN: <input type='password' name='mpin' required><br>";
echo "<input type='submit' name='test_mpin' value='Test MPIN' style='background:#007bff;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// Instructions
echo "<h3>How to Fix Your Login Issue</h3>";
echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;border:1px solid #ffeaa7;'>";
echo "<strong>Step-by-step solution:</strong><br><br>";
echo "1. <strong>Login to main system:</strong> Go to <a href='login.php'>login.php</a> and login with your main account<br>";
echo "2. <strong>Create a booking:</strong> Navigate to a hotel and make a booking<br>";
echo "3. <strong>Go to payment:</strong> In your bookings, click on 'Pay' for a booking<br>";
echo "4. <strong>Choose payment method:</strong> Select eSewa or Khalti<br>";
echo "5. <strong>Login to payment gateway:</strong> Use the test credentials above<br>";
echo "6. <strong>Enter MPIN:</strong> MPIN is only used for payment confirmation, not login<br><br>";
echo "<strong>Test Credentials:</strong><br>";
echo "eSewa: ID=9824004077, Password=password<br>";
echo "Khalti: ID=9824004077, Password=password<br>";
echo "</div>";

// Current session status
echo "<h3>Current Session Status</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
h2, h3, h4 {
    color: #333;
}
input[type="text"], input[type="password"], select {
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 3px;
    width: 200px;
}
</style> 