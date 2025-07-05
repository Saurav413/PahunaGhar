<?php
session_start();
require_once 'config.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/mpin_modal_test.log');

echo "<h2>MPIN Modal Database Connection Test</h2>";

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

// Test 3: Test eSewa MPIN Modal Connection
echo "<h3>Step 3: Test eSewa MPIN Modal Database Connection</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_esewa_mpin'])) {
    $mpin = $_POST['mpin'] ?? '';
    $esewa_id = $_SESSION['esewa_id'] ?? '';
    
    if ($esewa_id && $mpin) {
        try {
            $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
            $stmt->execute([$esewa_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && $row['mpin'] === $mpin) {
                echo "✅ eSewa MPIN verification successful!<br>";
                echo "eSewa ID: " . $esewa_id . "<br>";
                echo "MPIN: " . $mpin . "<br>";
            } else {
                echo "❌ eSewa MPIN verification failed.<br>";
                if ($row) {
                    echo "Expected MPIN: " . $row['mpin'] . ", Provided MPIN: " . $mpin . "<br>";
                } else {
                    echo "No user found with eSewa ID: " . $esewa_id . "<br>";
                }
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Missing eSewa ID or MPIN<br>";
    }
}

// Test 4: Test Khalti MPIN Modal Connection
echo "<h3>Step 4: Test Khalti MPIN Modal Database Connection</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_khalti_mpin'])) {
    $mpin = $_POST['mpin'] ?? '';
    $khalti_id = $_SESSION['khalti_id'] ?? '';
    
    if ($khalti_id && $mpin) {
        try {
            $stmt = $pdo->prepare("SELECT mpin FROM khalti_users WHERE khalti_id = ?");
            $stmt->execute([$khalti_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && $row['mpin'] === $mpin) {
                echo "✅ Khalti MPIN verification successful!<br>";
                echo "Khalti ID: " . $khalti_id . "<br>";
                echo "MPIN: " . $mpin . "<br>";
            } else {
                echo "❌ Khalti MPIN verification failed.<br>";
                if ($row) {
                    echo "Expected MPIN: " . $row['mpin'] . ", Provided MPIN: " . $mpin . "<br>";
                } else {
                    echo "No user found with Khalti ID: " . $khalti_id . "<br>";
                }
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Missing Khalti ID or MPIN<br>";
    }
}

// Test 5: Simulate AJAX MPIN Check
echo "<h3>Step 5: Simulate AJAX MPIN Check (eSewa)</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_esewa_ajax'])) {
    $mpin = $_POST['mpin'] ?? '';
    $esewa_id = $_SESSION['esewa_id'] ?? '';
    
    if ($esewa_id && $mpin) {
        try {
            $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
            $stmt->execute([$esewa_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && $row['mpin'] === $mpin) {
                echo "✅ AJAX MPIN check would return: success<br>";
            } else {
                echo "❌ AJAX MPIN check would return: fail<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Missing eSewa ID or MPIN<br>";
    }
}

// Display test forms
echo "<h3>Test Forms</h3>";

// eSewa MPIN Test Form
echo "<div style='background:#f8f9fa;padding:20px;margin:10px 0;border-radius:5px;'>";
echo "<h4>Test eSewa MPIN Modal</h4>";
echo "<form method='post'>";
echo "MPIN: <input type='password' name='mpin' placeholder='Enter MPIN' required><br>";
echo "<input type='submit' name='test_esewa_mpin' value='Test eSewa MPIN' style='background:#28a745;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// Khalti MPIN Test Form
echo "<div style='background:#f8f9fa;padding:20px;margin:10px 0;border-radius:5px;'>";
echo "<h4>Test Khalti MPIN Modal</h4>";
echo "<form method='post'>";
echo "MPIN: <input type='password' name='mpin' placeholder='Enter MPIN' required><br>";
echo "<input type='submit' name='test_khalti_mpin' value='Test Khalti MPIN' style='background:#6f42c1;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// AJAX Simulation Form
echo "<div style='background:#f8f9fa;padding:20px;margin:10px 0;border-radius:5px;'>";
echo "<h4>Simulate AJAX MPIN Check (eSewa)</h4>";
echo "<form method='post'>";
echo "MPIN: <input type='password' name='mpin' placeholder='Enter MPIN' required><br>";
echo "<input type='submit' name='simulate_esewa_ajax' value='Simulate AJAX Check' style='background:#007bff;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// Instructions
echo "<h3>How the MPIN Modal Works</h3>";
echo "<div style='background:#e3f2fd;padding:15px;border-radius:5px;border:1px solid #2196f3;'>";
echo "<strong>MPIN Modal Flow:</strong><br><br>";
echo "1. <strong>User clicks 'Pay Via eSewa/Khalti'</strong> → MPIN modal appears<br>";
echo "2. <strong>User enters MPIN</strong> → JavaScript captures the MPIN<br>";
echo "3. <strong>AJAX request sent</strong> → To check_esewa_mpin.php or check_khalti_mpin.php<br>";
echo "4. <strong>Database query</strong> → Fetches MPIN from esewa_users/khalti_users table<br>";
echo "5. <strong>MPIN verification</strong> → Compares input MPIN with stored MPIN<br>";
echo "6. <strong>Response returned</strong> → 'success' or 'fail'<br>";
echo "7. <strong>Payment processed</strong> → If MPIN is correct<br><br>";
echo "<strong>Test Credentials:</strong><br>";
echo "eSewa: ID=9824004077, MPIN=5470<br>";
echo "Khalti: ID=9824004077, MPIN=2020<br>";
echo "</div>";

// Current session status
echo "<h3>Current Session Status</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Database connection test
echo "<h3>Database Connection Test</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM esewa_users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ eSewa users table accessible: " . $result['count'] . " users<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM khalti_users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Khalti users table accessible: " . $result['count'] . " users<br>";
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
}
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
input[type="password"] {
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 3px;
    width: 200px;
}
</style> 