<?php
// Database configuration
$host = 'localhost';
$dbname = 'pahunaghar';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Update Test User Passwords</h2>";
    
    // Update eSewa test user password
    echo "<h3>Updating eSewa Test User</h3>";
    $stmt = $pdo->prepare("UPDATE esewa_users SET password = ? WHERE esewa_id = ?");
    $stmt->execute(['password', '9824004077']);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ eSewa test user password updated to 'password'<br>";
    } else {
        echo "❌ eSewa test user not found or password already set<br>";
    }
    
    // Update Khalti test user password
    echo "<h3>Updating Khalti Test User</h3>";
    $stmt = $pdo->prepare("UPDATE khalti_users SET password = ? WHERE khalti_id = ?");
    $stmt->execute(['password', '9824004077']);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Khalti test user password updated to 'password'<br>";
    } else {
        echo "❌ Khalti test user not found or password already set<br>";
    }
    
    // Display current test data
    echo "<h3>Current Test Data</h3>";
    
    echo "<h4>eSewa Users:</h4>";
    $stmt = $pdo->query("SELECT esewa_id, password, mpin FROM esewa_users");
    $esewaUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($esewaUsers as $user) {
        echo "- ID: " . $user['esewa_id'] . ", Password: " . $user['password'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    echo "<h4>Khalti Users:</h4>";
    $stmt = $pdo->query("SELECT khalti_id, password, mpin FROM khalti_users");
    $khaltiUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($khaltiUsers as $user) {
        echo "- ID: " . $user['khalti_id'] . ", Password: " . $user['password'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    // Test the new authentication
    echo "<h3>Testing New Authentication</h3>";
    
    // Test eSewa authentication
    $stmt = $pdo->prepare("SELECT * FROM esewa_users WHERE esewa_id = ? AND password = ? AND mpin = ?");
    $stmt->execute(['9824004077', 'password', '5470']);
    $esewaUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($esewaUser) {
        echo "✅ eSewa authentication test: SUCCESS<br>";
    } else {
        echo "❌ eSewa authentication test: FAILED<br>";
    }
    
    // Test Khalti authentication
    $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ? AND password = ? AND mpin = ?");
    $stmt->execute(['9824004077', 'password', '2020']);
    $khaltiUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($khaltiUser) {
        echo "✅ Khalti authentication test: SUCCESS<br>";
    } else {
        echo "❌ Khalti authentication test: FAILED<br>";
    }
    
    echo "<h3>Update Complete!</h3>";
    echo "<div style='background:#d4edda;padding:15px;border-radius:5px;border:1px solid #c3e6cb;'>";
    echo "<strong>✅ Test user passwords updated successfully!</strong><br><br>";
    echo "<strong>New Test Credentials:</strong><br>";
    echo "eSewa: ID=9824004077, Password=password, MPIN=5470<br>";
    echo "Khalti: ID=9824004077, Password=password, MPIN=2020<br><br>";
    echo "<strong>Next Steps:</strong><br>";
    echo "1. <a href='login.php'>Login to main system</a><br>";
    echo "2. <a href='test_payment_login.php'>Test payment system</a><br>";
    echo "3. Create a booking and try payment with new credentials<br>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Update failed: " . $e->getMessage();
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
</style> 