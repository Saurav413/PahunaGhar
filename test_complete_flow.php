<?php
session_start();
require_once 'config.php';

echo "<h2>🧪 Complete Payment Flow Test</h2>";

// Simulate complete login flow
if (isset($_POST['simulate_login'])) {
    // Step 1: Login to main system
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 1;
    
    // Step 2: Login to eSewa
    $_SESSION['esewa_id'] = '9824004077';
    
    echo "<div style='background:#d4edda;padding:15px;border-radius:5px;border:1px solid #c3e6cb;'>";
    echo "✅ <strong>Login Simulation Complete!</strong><br>";
    echo "Main system: Logged in (User ID: 1)<br>";
    echo "eSewa: Logged in (ID: 9824004077)<br>";
    echo "</div>";
}

// Test MPIN verification with session
if (isset($_POST['test_mpin']) && isset($_POST['mpin'])) {
    $mpin = $_POST['mpin'];
    $esewa_id = $_SESSION['esewa_id'] ?? '';
    
    echo "<h3>MPIN Test Results:</h3>";
    
    if (!$esewa_id) {
        echo "❌ <strong>Error:</strong> eSewa ID not set in session<br>";
        echo "You need to login to eSewa first.<br>";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
            $stmt->execute([$esewa_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                echo "✅ Found user with eSewa ID: " . $esewa_id . "<br>";
                echo "Stored MPIN: " . $row['mpin'] . "<br>";
                echo "Input MPIN: " . $mpin . "<br>";
                
                if ($row['mpin'] === $mpin) {
                    echo "✅ <strong>MPIN verification: SUCCESS!</strong><br>";
                    echo "Payment would be processed successfully.<br>";
                } else {
                    echo "❌ <strong>MPIN verification: FAILED</strong><br>";
                    echo "Expected: " . $row['mpin'] . ", Got: " . $mpin . "<br>";
                }
            } else {
                echo "❌ No user found with eSewa ID: " . $esewa_id . "<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
    }
}

// Show current session status
echo "<h3>Current Session Status:</h3>";
echo "<div style='background:#f8f9fa;padding:10px;border-radius:5px;'>";
echo "Main System Login: " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? '✅ YES' : '❌ NO') . "<br>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "eSewa ID: " . ($_SESSION['esewa_id'] ?? 'NOT SET') . "<br>";
echo "</div>";

// Test forms
echo "<h3>Test Forms:</h3>";

// Simulate login
echo "<div style='background:#e3f2fd;padding:15px;border-radius:5px;margin:10px 0;'>";
echo "<h4>Step 1: Simulate Login</h4>";
echo "<form method='post'>";
echo "<input type='submit' name='simulate_login' value='Simulate Complete Login' style='background:#007bff;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";
echo "</div>";

// Test MPIN
if (isset($_SESSION['esewa_id'])) {
    echo "<div style='background:#d4edda;padding:15px;border-radius:5px;margin:10px 0;'>";
    echo "<h4>Step 2: Test MPIN Verification</h4>";
    echo "<form method='post'>";
    echo "<div style='position:relative;display:inline-block;'>";
    echo "Enter MPIN: <input type='password' id='testMpinInput' name='mpin' placeholder='Enter MPIN' required style='padding:8px;margin:5px;padding-right:40px;border:1px solid #ddd;border-radius:3px;'>";
    echo "<button type='button' onclick='toggleTestMpin()' style='position:absolute;right:5px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;'>👁️</button>";
    echo "</div><br>";
    echo "<input type='submit' name='test_mpin' value='Test MPIN' style='background:#28a745;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
    echo "</form>";
    echo "<small>Expected MPIN: 5470</small>";
    echo "<script>";
    echo "function toggleTestMpin() {";
    echo "  var input = document.getElementById('testMpinInput');";
    echo "  var btn = event.target;";
    echo "  if (input.type === 'password') {";
    echo "    input.type = 'text';";
    echo "    btn.textContent = '🙈';";
    echo "  } else {";
    echo "    input.type = 'password';";
    echo "    btn.textContent = '👁️';";
    echo "  }";
    echo "}";
    echo "</script>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;margin:10px 0;'>";
    echo "<h4>Step 2: Test MPIN Verification</h4>";
    echo "❌ You need to login first (Step 1)<br>";
    echo "</div>";
}

// Instructions
echo "<h3>📋 Instructions:</h3>";
echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;border:1px solid #ffeaa7;'>";
echo "<strong>To fix your 'Invalid MPIN' issue:</strong><br><br>";
echo "1. <strong>Click 'Simulate Complete Login'</strong> above<br>";
echo "2. <strong>Enter MPIN: 5470</strong> in the test form<br>";
echo "3. <strong>Verify it shows 'SUCCESS'</strong><br><br>";
echo "<strong>Then in your actual system:</strong><br>";
echo "1. Login to main system: <a href='login.php'>login.php</a><br>";
echo "2. Login to eSewa: <a href='esewa_login.php'>esewa_login.php</a> (ID: 9824004077, Password: password)<br>";
echo "3. Go to payment page and try again<br>";
echo "</div>";

// Real-world test links
echo "<h3>🔗 Real-World Testing:</h3>";
echo "<div style='background:#f8f9fa;padding:15px;border-radius:5px;'>";
echo "<a href='login.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>1. Main Login</a>";
echo "<a href='esewa_login.php' style='background:#28a745;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>2. eSewa Login</a>";
echo "<a href='debug_mpin_issue.php' style='background:#ffc107;color:black;padding:10px;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>3. Debug MPIN</a>";
echo "</div>";
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