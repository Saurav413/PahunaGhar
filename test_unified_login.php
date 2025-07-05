<?php
// Test script for unified login system
require_once 'user_config.php';

echo "<h2>Unified Login System Test</h2>";

try {
    // Test 1: Check admin account
    echo "<h3>Test 1: Admin Account Check</h3>";
    $stmt = $user_pdo->prepare("SELECT id, name, email, admin_role, is_active FROM admin_register_form WHERE email = ?");
    $stmt->execute(['admin@pahunaghar.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin account found:<br>";
        echo "- Name: " . htmlspecialchars($admin['name']) . "<br>";
        echo "- Email: " . htmlspecialchars($admin['email']) . "<br>";
        echo "- Role: " . htmlspecialchars($admin['admin_role']) . "<br>";
        echo "- Status: " . ($admin['is_active'] ? 'Active' : 'Inactive') . "<br>";
        
        // Test password
        $stmt = $user_pdo->prepare("SELECT password FROM admin_register_form WHERE email = ?");
        $stmt->execute(['admin@pahunaghar.com']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && password_verify('admin123', $result['password'])) {
            echo "✅ Admin password verification: SUCCESS<br>";
        } else {
            echo "❌ Admin password verification: FAILED<br>";
        }
    } else {
        echo "❌ Admin account not found<br>";
    }
    
    // Test 2: Check user accounts
    echo "<h3>Test 2: User Accounts Check</h3>";
    $stmt = $user_pdo->query("SELECT COUNT(*) FROM user_register_form");
    $userCount = $stmt->fetchColumn();
    echo "Found $userCount user account(s)<br>";
    
    if ($userCount > 0) {
        // Get a sample user
        $stmt = $user_pdo->query("SELECT id, name, email, user_type FROM user_register_form LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✅ Sample user account:<br>";
            echo "- Name: " . htmlspecialchars($user['name']) . "<br>";
            echo "- Email: " . htmlspecialchars($user['email']) . "<br>";
            echo "- Type: " . htmlspecialchars($user['user_type']) . "<br>";
        }
    }
    
    // Test 3: Test login logic simulation
    echo "<h3>Test 3: Login Logic Simulation</h3>";
    
    // Simulate admin login
    $testEmail = 'admin@pahunaghar.com';
    $testPassword = 'admin123';
    
    // Check admin first
    $stmt = $user_pdo->prepare('SELECT id, name, email, password, admin_role, is_active FROM admin_register_form WHERE email = ?');
    $stmt->execute([$testEmail]);
    $testAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testAdmin && password_verify($testPassword, $testAdmin['password'])) {
        if ($testAdmin['is_active']) {
            echo "✅ Admin login simulation: SUCCESS<br>";
            echo "Would redirect to: admin_dashboard.php<br>";
        } else {
            echo "❌ Admin login simulation: FAILED (account inactive)<br>";
        }
    } else {
        // Check user
        $stmt = $user_pdo->prepare('SELECT id, name, email, password, user_type FROM user_register_form WHERE email = ?');
        $stmt->execute([$testEmail]);
        $testUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($testUser && password_verify($testPassword, $testUser['password'])) {
            echo "✅ User login simulation: SUCCESS<br>";
            if ($testUser['user_type'] === 'admin') {
                echo "Would redirect to: admin_dashboard.php<br>";
            } else {
                echo "Would redirect to: homepage.php<br>";
            }
        } else {
            echo "❌ Login simulation: FAILED (no account found or wrong password)<br>";
        }
    }
    
    // Test 4: Check for email conflicts
    echo "<h3>Test 4: Email Conflict Check</h3>";
    
    // Check if admin email exists in user table
    $stmt = $user_pdo->prepare("SELECT COUNT(*) FROM user_register_form WHERE email = ?");
    $stmt->execute(['admin@pahunaghar.com']);
    $userConflict = $stmt->fetchColumn();
    
    if ($userConflict > 0) {
        echo "⚠️ WARNING: Admin email exists in user table. This may cause conflicts.<br>";
    } else {
        echo "✅ No email conflicts found.<br>";
    }
    
    // Test 5: Display login instructions
    echo "<h3>Test 5: Login Instructions</h3>";
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #bbdefb;'>";
    echo "<strong>Unified Login System Ready!</strong><br><br>";
    echo "<strong>Admin Login:</strong><br>";
    echo "URL: <a href='login.php'>login.php</a><br>";
    echo "Email: admin@pahunaghar.com<br>";
    echo "Password: admin123<br><br>";
    echo "<strong>User Login:</strong><br>";
    echo "Use any registered user email and password<br><br>";
    echo "<strong>Note:</strong> The system will automatically detect if you're an admin or user and redirect accordingly.";
    echo "</div>";
    
    echo "<h3>✅ Unified Login Test Complete!</h3>";
    
} catch (Exception $e) {
    echo "<h3>❌ Test Failed</h3>";
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