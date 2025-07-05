<?php
// Live debugging script for MPIN verification
session_start();
require_once 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Live MPIN Debug - Real-time Monitoring</h2>";

// Log all session data
echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Log all POST data
echo "<h3>POST Data Received:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Log all GET data
echo "<h3>GET Data Received:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

echo "<h3>Request Type:</h3>";
echo "Is AJAX: " . ($isAjax ? 'YES' : 'NO') . "<br>";
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET') . "<br>";

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $test_stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection: SUCCESS<br>";
} catch (Exception $e) {
    echo "❌ Database connection: FAILED - " . $e->getMessage() . "<br>";
}

// Check if user is logged in
echo "<h3>Authentication Status:</h3>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "✅ User is logged in<br>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
} else {
    echo "❌ User is NOT logged in<br>";
}

// Check payment gateway session variables
echo "<h3>Payment Gateway Session Variables:</h3>";
echo "eSewa ID: " . ($_SESSION['esewa_id'] ?? 'NOT SET') . "<br>";
echo "Khalti ID: " . ($_SESSION['khalti_id'] ?? 'NOT SET') . "<br>";

// If MPIN is provided, test verification
if (isset($_POST['mpin']) && !empty($_POST['mpin'])) {
    $mpin = $_POST['mpin'];
    echo "<h3>MPIN Verification Test:</h3>";
    echo "Input MPIN: " . $mpin . "<br>";
    
    // Test eSewa MPIN
    if (isset($_SESSION['esewa_id'])) {
        echo "<h4>Testing eSewa MPIN:</h4>";
        $esewa_id = $_SESSION['esewa_id'];
        echo "eSewa ID: $esewa_id<br>";
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM esewa_users WHERE esewa_id = ?");
            $stmt->execute([$esewa_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "✅ eSewa user found in database<br>";
                echo "Stored MPIN: " . $user['mpin'] . "<br>";
                echo "Input MPIN: " . $mpin . "<br>";
                echo "MPIN Match: " . ($user['mpin'] === $mpin ? 'YES' : 'NO') . "<br>";
                
                if ($user['mpin'] === $mpin) {
                    echo "✅ eSewa MPIN verification: SUCCESS<br>";
                } else {
                    echo "❌ eSewa MPIN verification: FAILED<br>";
                }
            } else {
                echo "❌ eSewa user not found in database<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    }
    
    // Test Khalti MPIN
    if (isset($_SESSION['khalti_id'])) {
        echo "<h4>Testing Khalti MPIN:</h4>";
        $khalti_id = $_SESSION['khalti_id'];
        echo "Khalti ID: $khalti_id<br>";
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ?");
            $stmt->execute([$khalti_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "✅ Khalti user found in database<br>";
                echo "Stored MPIN: " . $user['mpin'] . "<br>";
                echo "Input MPIN: " . $mpin . "<br>";
                echo "MPIN Match: " . ($user['mpin'] === $mpin ? 'YES' : 'NO') . "<br>";
                
                if ($user['mpin'] === $mpin) {
                    echo "✅ Khalti MPIN verification: SUCCESS<br>";
                } else {
                    echo "❌ Khalti MPIN verification: FAILED<br>";
                }
            } else {
                echo "❌ Khalti user not found in database<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    }
}

// Show all available test data
echo "<h3>Available Test Data:</h3>";
try {
    echo "<h4>eSewa Users:</h4>";
    $stmt = $pdo->query("SELECT * FROM esewa_users");
    $esewa_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($esewa_users as $user) {
        echo "- ID: " . $user['esewa_id'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    echo "<h4>Khalti Users:</h4>";
    $stmt = $pdo->query("SELECT * FROM khalti_users");
    $khalti_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($khalti_users as $user) {
        echo "- ID: " . $user['khalti_id'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching test data: " . $e->getMessage() . "<br>";
}

// Instructions for testing
echo "<h3>How to Test:</h3>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #2196f3;'>";
echo "<strong>Testing Instructions:</strong><br><br>";
echo "1. First, login to the main system<br>";
echo "2. Go to a booking and try to pay<br>";
echo "3. Choose eSewa or Khalti payment method<br>";
echo "4. Login with the payment gateway credentials<br>";
echo "5. Try to enter MPIN in the payment modal<br>";
echo "6. Check this debug page to see what's happening<br>";
echo "<br>";
echo "<strong>Test Credentials:</strong><br>";
echo "eSewa: ID=9745869500, Password=1111, MPIN=5470<br>";
echo "Khalti: ID=9824004077, Password=1111, MPIN=2020<br>";
echo "</div>";

// Add a form to test MPIN directly
echo "<h3>Direct MPIN Test:</h3>";
echo "<form method='post' style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
echo "Enter MPIN: <input type='password' name='mpin' required>";
echo "<input type='submit' value='Test MPIN'>";
echo "</form>";
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
pre {
    background: #fff;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
    overflow-x: auto;
}
</style> 