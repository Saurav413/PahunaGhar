<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect without specifying database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Payment Gateway Tables Setup</h2>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'pahunaghar'");
    $dbExists = $stmt->rowCount() > 0;
    
    if (!$dbExists) {
        echo "❌ Database 'pahunaghar' does not exist. Creating it...<br>";
        $pdo->exec("CREATE DATABASE pahunaghar");
        echo "✅ Database 'pahunaghar' created successfully.<br>";
    } else {
        echo "✅ Database 'pahunaghar' exists.<br>";
    }
    
    // Use the database
    $pdo->exec("USE pahunaghar");
    
    // Create esewa_users table
    echo "<h3>Setting up eSewa Users Table</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS esewa_users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        esewa_id VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        mpin VARCHAR(20)
    )";
    $pdo->exec($sql);
    echo "✅ eSewa users table created/verified.<br>";
    
    // Create khalti_users table
    echo "<h3>Setting up Khalti Users Table</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS khalti_users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        khalti_id VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        mpin VARCHAR(20)
    )";
    $pdo->exec($sql);
    echo "✅ Khalti users table created/verified.<br>";
    
    // Check if test data exists
    echo "<h3>Checking Test Data</h3>";
    
    // Check eSewa test data
    $stmt = $pdo->prepare("SELECT * FROM esewa_users WHERE esewa_id = ?");
    $stmt->execute(['9824004077']);
    $esewaUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$esewaUser) {
        echo "❌ eSewa test user not found. Creating...<br>";
        $stmt = $pdo->prepare("INSERT INTO esewa_users (esewa_id, password, mpin) VALUES (?, ?, ?)");
        $stmt->execute(['9824004077', '1111', '5470']);
        echo "✅ eSewa test user created.<br>";
    } else {
        echo "✅ eSewa test user exists.<br>";
    }
    
    // Check Khalti test data
    $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ?");
    $stmt->execute(['9824004077']);
    $khaltiUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$khaltiUser) {
        echo "❌ Khalti test user not found. Creating...<br>";
        $stmt = $pdo->prepare("INSERT INTO khalti_users (khalti_id, password, mpin) VALUES (?, ?, ?)");
        $stmt->execute(['9824004077', '1111', '2020']);
        echo "✅ Khalti test user created.<br>";
    } else {
        echo "✅ Khalti test user exists.<br>";
    }
    
    // Display current data
    echo "<h3>Current Test Data</h3>";
    
    echo "<h4>eSewa Users:</h4>";
    $stmt = $pdo->query("SELECT * FROM esewa_users");
    $esewaUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($esewaUsers as $user) {
        echo "- ID: " . $user['esewa_id'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    echo "<h4>Khalti Users:</h4>";
    $stmt = $pdo->query("SELECT * FROM khalti_users");
    $khaltiUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($khaltiUsers as $user) {
        echo "- ID: " . $user['khalti_id'] . ", MPIN: " . $user['mpin'] . "<br>";
    }
    
    // Test database connection with the correct config
    echo "<h3>Testing Database Connection</h3>";
    try {
        $testPdo = new PDO("mysql:host=$host;dbname=pahunaghar", $username, $password);
        $testPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Database connection test successful.<br>";
        
        // Test a simple query
        $stmt = $testPdo->query("SELECT COUNT(*) as count FROM esewa_users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ eSewa users count: " . $result['count'] . "<br>";
        
        $stmt = $testPdo->query("SELECT COUNT(*) as count FROM khalti_users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Khalti users count: " . $result['count'] . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Database connection test failed: " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>Setup Complete!</h3>";
    echo "<div style='background:#d4edda;padding:15px;border-radius:5px;border:1px solid #c3e6cb;'>";
    echo "<strong>✅ Payment gateway tables are ready!</strong><br><br>";
    echo "<strong>Test Credentials:</strong><br>";
    echo "eSewa: ID=9824004077, MPIN=5470<br>";
    echo "Khalti: ID=9824004077, MPIN=2020<br><br>";
    echo "<strong>Next Steps:</strong><br>";
    echo "1. <a href='login.php'>Login to main system</a><br>";
    echo "2. <a href='test_payment_login.php'>Test payment system</a><br>";
    echo "3. Create a booking and try payment<br>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Setup failed: " . $e->getMessage();
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