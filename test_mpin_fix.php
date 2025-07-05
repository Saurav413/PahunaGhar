<?php
// Test script to verify MPIN verification fixes
session_start();
require_once 'config.php';

echo "<h2>MPIN Verification Test</h2>";

// Test data from debug output
$test_esewa_id = "9745869500";
$test_esewa_mpin = "5470";
$test_khalti_id = "9824004077";
$test_khalti_mpin = "2020";

echo "<h3>Step 1: Testing eSewa MPIN Verification</h3>";

// Simulate eSewa login
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['esewa_id'] = $test_esewa_id;

echo "Session variables set:<br>";
echo "- logged_in: " . ($_SESSION['logged_in'] ?? 'NOT SET') . "<br>";
echo "- user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "- esewa_id: " . ($_SESSION['esewa_id'] ?? 'NOT SET') . "<br><br>";

// Test eSewa MPIN verification
$esewa_id = $_SESSION['esewa_id'] ?? null;
$mpin = $test_esewa_mpin;

if ($esewa_id && $mpin) {
    $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
    $stmt->execute([$esewa_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "✅ eSewa user found in database<br>";
        echo "- Stored MPIN: " . $row['mpin'] . "<br>";
        echo "- Input MPIN: " . $mpin . "<br>";
        
        if ($row['mpin'] === $mpin) {
            echo "✅ eSewa MPIN verification: SUCCESS<br>";
        } else {
            echo "❌ eSewa MPIN verification: FAILED (MPIN mismatch)<br>";
        }
    } else {
        echo "❌ eSewa user not found in database<br>";
    }
} else {
    echo "❌ Missing eSewa ID or MPIN<br>";
}

echo "<br><h3>Step 2: Testing Khalti MPIN Verification</h3>";

// Simulate Khalti login
$_SESSION['khalti_id'] = $test_khalti_id;

echo "Session variables set:<br>";
echo "- khalti_id: " . ($_SESSION['khalti_id'] ?? 'NOT SET') . "<br><br>";

// Test Khalti MPIN verification
$khalti_id = $_SESSION['khalti_id'] ?? null;
$mpin = $test_khalti_mpin;

if ($khalti_id && $mpin) {
    $stmt = $pdo->prepare("SELECT mpin FROM khalti_users WHERE khalti_id = ?");
    $stmt->execute([$khalti_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "✅ Khalti user found in database<br>";
        echo "- Stored MPIN: " . $row['mpin'] . "<br>";
        echo "- Input MPIN: " . $mpin . "<br>";
        
        if ($row['mpin'] === $mpin) {
            echo "✅ Khalti MPIN verification: SUCCESS<br>";
        } else {
            echo "❌ Khalti MPIN verification: FAILED (MPIN mismatch)<br>";
        }
    } else {
        echo "❌ Khalti user not found in database<br>";
    }
} else {
    echo "❌ Missing Khalti ID or MPIN<br>";
}

echo "<br><h3>Step 3: Summary</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
echo "<strong>Fixes Applied:</strong><br><br>";
echo "1. ✅ Fixed eSewa login to set \$_SESSION['esewa_id']<br>";
echo "2. ✅ Fixed Khalti MPIN verification to use 'khalti_users' table instead of 'user_mpin'<br>";
echo "3. ✅ Cleaned up check_esewa_mpin.php file<br>";
echo "<br>";
echo "<strong>Test Results:</strong><br>";
echo "- eSewa MPIN verification should now work correctly<br>";
echo "- Khalti MPIN verification should now work correctly<br>";
echo "</div>";

// Clear session for security
session_destroy();
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
</style> 