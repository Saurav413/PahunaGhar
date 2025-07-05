<?php
// Database configuration
$host = 'localhost';
$dbname = 'pahunaghar';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Fix Khalti User Data</h2>";
    
    // First, let's see what Khalti users exist
    echo "<h3>Current Khalti Users:</h3>";
    $stmt = $pdo->query("SELECT * FROM khalti_users");
    $khaltiUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($khaltiUsers as $user) {
        echo "- ID: " . $user['khalti_id'] . ", Password: " . $user['password'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    // Update the existing Khalti user to have the correct credentials
    echo "<h3>Updating Khalti User Credentials</h3>";
    
    // Update the user with ID 9745869500 to have the correct credentials
    $stmt = $pdo->prepare("UPDATE khalti_users SET khalti_id = ?, password = ?, mpin = ? WHERE khalti_id = ?");
    $stmt->execute(['9824004077', 'password', '2020', '9745869500']);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Khalti user updated successfully<br>";
    } else {
        echo "❌ Khalti user update failed<br>";
    }
    
    // Also, let's create a new user if needed
    $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ?");
    $stmt->execute(['9824004077']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "Creating new Khalti user with correct credentials...<br>";
        $stmt = $pdo->prepare("INSERT INTO khalti_users (khalti_id, password, mpin) VALUES (?, ?, ?)");
        $stmt->execute(['9824004077', 'password', '2020']);
        echo "✅ New Khalti user created<br>";
    }
    
    // Display updated data
    echo "<h3>Updated Test Data</h3>";
    
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
    
    // Test the authentication
    echo "<h3>Testing Authentication</h3>";
    
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
    
    echo "<h3>Fix Complete!</h3>";
    echo "<div style='background:#d4edda;padding:15px;border-radius:5px;border:1px solid #c3e6cb;'>";
    echo "<strong>✅ Khalti user data fixed successfully!</strong><br><br>";
    echo "<strong>Final Test Credentials:</strong><br>";
    echo "eSewa: ID=9824004077, Password=password, MPIN=5470<br>";
    echo "Khalti: ID=9824004077, Password=password, MPIN=2020<br><br>";
    echo "<strong>Next Steps:</strong><br>";
    echo "1. <a href='login.php'>Login to main system</a><br>";
    echo "2. <a href='test_payment_login.php'>Test payment system</a><br>";
    echo "3. Create a booking and try payment with new credentials<br>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Fix failed: " . $e->getMessage();
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