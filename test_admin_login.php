<?php
// Test script for admin login functionality
require_once 'user_config.php';

echo "<h2>Admin Login Test</h2>";

try {
    // Test 1: Check if admin table exists
    echo "<h3>Test 1: Database Table Check</h3>";
    $stmt = $user_pdo->query("SHOW TABLES LIKE 'admin_register_form'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Admin table exists<br>";
    } else {
        echo "❌ Admin table does not exist. Run setup_admin_system.php first.<br>";
        exit;
    }
    
    // Test 2: Check if admin accounts exist
    echo "<h3>Test 2: Admin Accounts Check</h3>";
    $stmt = $user_pdo->query("SELECT COUNT(*) FROM admin_register_form");
    $count = $stmt->fetchColumn();
    echo "Found $count admin account(s)<br>";
    
    if ($count == 0) {
        echo "❌ No admin accounts found. Run setup_admin_system.php to create default admin.<br>";
        exit;
    }
    
    // Test 3: Check default admin account
    echo "<h3>Test 3: Default Admin Account Check</h3>";
    $stmt = $user_pdo->prepare("SELECT id, name, email, admin_role, is_active FROM admin_register_form WHERE email = ?");
    $stmt->execute(['admin@pahunaghar.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Default admin account found<br>";
        echo "Name: " . htmlspecialchars($admin['name']) . "<br>";
        echo "Email: " . htmlspecialchars($admin['email']) . "<br>";
        echo "Role: " . htmlspecialchars($admin['admin_role']) . "<br>";
        echo "Status: " . ($admin['is_active'] ? 'Active' : 'Inactive') . "<br>";
    } else {
        echo "❌ Default admin account not found<br>";
    }
    
    // Test 4: Test password verification
    echo "<h3>Test 4: Password Verification Test</h3>";
    if ($admin) {
        $test_password = 'admin123';
        $stmt = $user_pdo->prepare("SELECT password FROM admin_register_form WHERE email = ?");
        $stmt->execute(['admin@pahunaghar.com']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && password_verify($test_password, $result['password'])) {
            echo "✅ Password verification successful<br>";
        } else {
            echo "❌ Password verification failed<br>";
            echo "This might be because the password hash is incorrect.<br>";
        }
    }
    
    // Test 5: Test login process simulation
    echo "<h3>Test 5: Login Process Simulation</h3>";
    if ($admin && $admin['is_active']) {
        echo "✅ Admin account is active and can login<br>";
        echo "✅ Login process should work correctly<br>";
    } else {
        echo "❌ Admin account is inactive or not found<br>";
    }
    
    // Test 6: Display all admin accounts
    echo "<h3>Test 6: All Admin Accounts</h3>";
    $stmt = $user_pdo->query("SELECT id, name, email, admin_role, is_active, created_at FROM admin_register_form ORDER BY created_at DESC");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($admins)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['id'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['name']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['admin_role']) . "</td>";
            echo "<td>" . ($admin['is_active'] ? 'Active' : 'Inactive') . "</td>";
            echo "<td>" . $admin['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test 7: Provide troubleshooting steps
    echo "<h3>Test 7: Troubleshooting Steps</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #ffeaa7;'>";
    echo "<strong>If you still can't login:</strong><br>";
    echo "1. Make sure you're using the correct URL: <a href='admin_login.php'>admin_login.php</a><br>";
    echo "2. Check that you're using the correct credentials:<br>";
    echo "   - Email: admin@pahunaghar.com<br>";
    echo "   - Password: admin123<br>";
    echo "3. Clear your browser cache and cookies<br>";
    echo "4. Check if your database connection is working<br>";
    echo "5. Verify that the admin_register_form table exists and has data<br>";
    echo "</div>";
    
    echo "<h3>✅ Admin Login Test Complete!</h3>";
    
} catch (Exception $e) {
    echo "<h3>❌ Test Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
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
table {
    background: white;
    margin: 10px 0;
}
th, td {
    padding: 8px;
    text-align: left;
}
th {
    background: #f0f0f0;
}
</style> 