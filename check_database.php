<?php
// Database connectivity and table check script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connectivity Check</h2>";

// Test user_config.php
echo "<h3>Testing user_config.php:</h3>";
try {
    require 'user_config.php';
    echo "✓ user_config.php loaded successfully<br>";
    echo "✓ user_pdo connection: " . (isset($user_pdo) ? 'Available' : 'Not available') . "<br>";
    
    if (isset($user_pdo)) {
        // Test database connection
        $stmt = $user_pdo->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✓ Connected to database: " . $result['db_name'] . "<br>";
        
        // Check if user_register_form table exists
        $stmt = $user_pdo->query("SHOW TABLES LIKE 'user_register_form'");
        $tableExists = $stmt->rowCount() > 0;
        echo "✓ user_register_form table: " . ($tableExists ? 'Exists' : 'Missing') . "<br>";
        
        if ($tableExists) {
            $stmt = $user_pdo->query("SELECT COUNT(*) as count FROM user_register_form");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✓ Number of users: " . $result['count'] . "<br>";
        }
        
        // Check if password_resets table exists
        $stmt = $user_pdo->query("SHOW TABLES LIKE 'password_resets'");
        $tableExists = $stmt->rowCount() > 0;
        echo "✓ password_resets table: " . ($tableExists ? 'Exists' : 'Missing') . "<br>";
        
        if (!$tableExists) {
            echo "<p style='color: red;'>❌ password_resets table is missing! Creating it now...</p>";
            
            // Create the password_resets table
            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL,
                    token VARCHAR(64) NOT NULL UNIQUE,
                    expires_at DATETIME NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_email (email),
                    INDEX idx_token (token),
                    INDEX idx_expires (expires_at)
                )
            ";
            
            try {
                $user_pdo->exec($createTableSQL);
                echo "<p style='color: green;'>✓ password_resets table created successfully!</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>❌ Error creating password_resets table: " . $e->getMessage() . "</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ user_pdo connection not available</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test config.php
echo "<h3>Testing config.php:</h3>";
try {
    require 'config.php';
    echo "✓ config.php loaded successfully<br>";
    echo "✓ pdo connection: " . (isset($pdo) ? 'Available' : 'Not available') . "<br>";
    echo "✓ conn connection: " . (isset($conn) ? 'Available' : 'Not available') . "<br>";
    
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✓ Connected to database: " . $result['db_name'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='forgot_password.php'>← Back to Forgot Password</a></p>";
echo "<p><a href='forgot_password_debug.php'>← Debug Version</a></p>";
?> 