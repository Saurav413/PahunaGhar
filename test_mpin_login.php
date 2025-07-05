<?php
// Test script for MPIN-based login system
require_once 'config.php';

echo "<h2>MPIN-Based Login System Test</h2>";

// Test credentials
$test_esewa_id = "9745869500";
$test_esewa_mpin = "5470";
$test_khalti_id = "9824004077";
$test_khalti_mpin = "2020";

echo "<h3>Step 1: Testing eSewa MPIN Login</h3>";
echo "Test eSewa ID: $test_esewa_id<br>";
echo "Test eSewa MPIN: $test_esewa_mpin<br><br>";

try {
    // Test eSewa login with MPIN
    $stmt = $pdo->prepare("SELECT * FROM esewa_users WHERE esewa_id = ? AND mpin = ?");
    $stmt->execute([$test_esewa_id, $test_esewa_mpin]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ eSewa MPIN login: SUCCESS<br>";
        echo "- User found in database<br>";
        echo "- ID: " . $user['esewa_id'] . "<br>";
        echo "- MPIN: " . $user['mpin'] . "<br>";
        echo "- Password: " . $user['password'] . "<br>";
    } else {
        echo "❌ eSewa MPIN login: FAILED<br>";
        echo "- User not found or MPIN incorrect<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Step 2: Testing Khalti MPIN Login</h3>";
echo "Test Khalti ID: $test_khalti_id<br>";
echo "Test Khalti MPIN: $test_khalti_mpin<br><br>";

try {
    // Test Khalti login with MPIN
    $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ? AND mpin = ?");
    $stmt->execute([$test_khalti_id, $test_khalti_mpin]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Khalti MPIN login: SUCCESS<br>";
        echo "- User found in database<br>";
        echo "- ID: " . $user['khalti_id'] . "<br>";
        echo "- MPIN: " . $user['mpin'] . "<br>";
        echo "- Password: " . $user['password'] . "<br>";
    } else {
        echo "❌ Khalti MPIN login: FAILED<br>";
        echo "- User not found or MPIN incorrect<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Step 3: Testing Wrong MPIN</h3>";
echo "Testing with wrong MPIN to ensure security...<br><br>";

try {
    // Test with wrong MPIN
    $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ? AND mpin = ?");
    $stmt->execute([$test_khalti_id, "9999"]); // Wrong MPIN
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "❌ Security issue: Wrong MPIN was accepted<br>";
    } else {
        echo "✅ Security test: Wrong MPIN correctly rejected<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Step 4: Summary of Changes</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
echo "<strong>MPIN-Based Login System Implemented:</strong><br><br>";
echo "1. ✅ eSewa login now uses MPIN instead of password<br>";
echo "2. ✅ Khalti login now uses MPIN instead of password<br>";
echo "3. ✅ Form fields updated to use MPIN<br>";
echo "4. ✅ Database queries updated to check MPIN column<br>";
echo "5. ✅ Error messages updated to reflect MPIN usage<br>";
echo "<br>";
echo "<strong>How it works:</strong><br>";
echo "- User enters Khalti ID and MPIN<br>";
echo "- System checks khalti_users table for matching ID and MPIN<br>";
echo "- If match found, user is logged in and redirected to payment page<br>";
echo "- If no match, error message is shown<br>";
echo "</div>";

echo "<br><h3>Step 5: Test Instructions</h3>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #2196f3;'>";
echo "<strong>To test the new system:</strong><br><br>";
echo "1. Go to a booking and choose Khalti payment<br>";
echo "2. On the Khalti login page, enter:<br>";
echo "   - Khalti ID: $test_khalti_id<br>";
echo "   - MPIN: $test_khalti_mpin<br>";
echo "3. Click LOGIN<br>";
echo "4. You should be redirected to the payment page<br>";
echo "5. Try entering the MPIN again in the payment modal<br>";
echo "</div>";
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