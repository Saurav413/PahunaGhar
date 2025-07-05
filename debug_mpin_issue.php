<?php
// Debug script for MPIN verification issues
require_once 'config.php';

echo "<h2>MPIN Verification Debug</h2>";

try {
    // Check what tables exist
    echo "<h3>Step 1: Checking Database Tables</h3>";
    
    $tables = ['esewa_users', 'khalti_users', 'user_mpin'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
            
            // Show table structure
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Columns in $table:<br>";
            foreach ($columns as $column) {
                echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
            }
            
            // Show sample data
            $stmt = $pdo->query("SELECT * FROM $table LIMIT 3");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($data)) {
                echo "Sample data in $table:<br>";
                foreach ($data as $row) {
                    echo "- " . json_encode($row) . "<br>";
                }
            } else {
                echo "No data in $table<br>";
            }
        } else {
            echo "❌ Table '$table' does not exist<br>";
        }
        echo "<br>";
    }
    
    // Check session variables
    echo "<h3>Step 2: Session Variables Check</h3>";
    session_start();
    echo "Session variables:<br>";
    echo "- logged_in: " . ($_SESSION['logged_in'] ?? 'NOT SET') . "<br>";
    echo "- user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
    echo "- esewa_id: " . ($_SESSION['esewa_id'] ?? 'NOT SET') . "<br>";
    echo "- khalti_id: " . ($_SESSION['khalti_id'] ?? 'NOT SET') . "<br>";
    
    // Test eSewa MPIN verification
    echo "<h3>Step 3: eSewa MPIN Verification Test</h3>";
    if (isset($_SESSION['esewa_id'])) {
        $esewa_id = $_SESSION['esewa_id'];
        echo "Testing eSewa ID: $esewa_id<br>";
        
        // Check if esewa_users table exists and has data
        $stmt = $pdo->query("SHOW TABLES LIKE 'esewa_users'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT * FROM esewa_users WHERE esewa_id = ?");
            $stmt->execute([$esewa_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "✅ eSewa user found:<br>";
                echo "- ID: " . $user['esewa_id'] . "<br>";
                echo "- MPIN: " . $user['mpin'] . "<br>";
                echo "- Password: " . $user['password'] . "<br>";
            } else {
                echo "❌ No eSewa user found with ID: $esewa_id<br>";
            }
        } else {
            echo "❌ esewa_users table does not exist<br>";
        }
    } else {
        echo "❌ esewa_id not set in session<br>";
    }
    
    // Test Khalti MPIN verification
    echo "<h3>Step 4: Khalti MPIN Verification Test</h3>";
    if (isset($_SESSION['khalti_id'])) {
        $khalti_id = $_SESSION['khalti_id'];
        echo "Testing Khalti ID: $khalti_id<br>";
        
        // Check if khalti_users table exists and has data
        $stmt = $pdo->query("SHOW TABLES LIKE 'khalti_users'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT * FROM khalti_users WHERE khalti_id = ?");
            $stmt->execute([$khalti_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "✅ Khalti user found:<br>";
                echo "- ID: " . $user['khalti_id'] . "<br>";
                echo "- MPIN: " . $user['mpin'] . "<br>";
                echo "- Password: " . $user['password'] . "<br>";
            } else {
                echo "❌ No Khalti user found with ID: $khalti_id<br>";
            }
        } else {
            echo "❌ khalti_users table does not exist<br>";
        }
        
        // Also check user_mpin table
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_mpin'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT * FROM user_mpin WHERE user_type = 'khalti' AND user_id = ?");
            $stmt->execute([$khalti_id]);
            $mpin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($mpin) {
                echo "✅ Khalti MPIN found in user_mpin table:<br>";
                echo "- User ID: " . $mpin['user_id'] . "<br>";
                echo "- User Type: " . $mpin['user_type'] . "<br>";
                echo "- MPIN: " . $mpin['mpin'] . "<br>";
            } else {
                echo "❌ No Khalti MPIN found in user_mpin table<br>";
            }
        } else {
            echo "❌ user_mpin table does not exist<br>";
        }
    } else {
        echo "❌ khalti_id not set in session<br>";
    }
    
    // Provide solutions
    echo "<h3>Step 5: Solutions</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #ffeaa7;'>";
    echo "<strong>Common Issues and Solutions:</strong><br><br>";
    echo "1. <strong>Missing Tables:</strong> Create the required tables<br>";
    echo "2. <strong>Session Variables:</strong> Make sure esewa_id/khalti_id are set during login<br>";
    echo "3. <strong>Data Mismatch:</strong> Check if MPIN data exists in the correct table<br>";
    echo "4. <strong>Table Structure:</strong> Verify table columns match the code expectations<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h3>❌ Debug Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
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