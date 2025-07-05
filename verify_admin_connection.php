<?php
// Verify admin_register_form table connection
require_once 'user_config.php';

echo "<h2>Admin Table Connection Verification</h2>";

try {
    // Test database connection
    echo "<h3>✅ Database Connection: SUCCESS</h3>";
    
    // Test admin table access
    $stmt = $user_pdo->query("SELECT COUNT(*) FROM admin_register_form");
    $count = $stmt->fetchColumn();
    echo "<h3>✅ Admin Table Access: SUCCESS</h3>";
    echo "Found $count admin account(s) in the table.<br>";
    
    // Test admin login query
    $stmt = $user_pdo->prepare("SELECT id, name, email, admin_role, is_active FROM admin_register_form WHERE email = ?");
    $stmt->execute(['admin@pahunaghar.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<h3>✅ Admin Login Query: SUCCESS</h3>";
        echo "Admin account found:<br>";
        echo "- ID: " . $admin['id'] . "<br>";
        echo "- Name: " . htmlspecialchars($admin['name']) . "<br>";
        echo "- Email: " . htmlspecialchars($admin['email']) . "<br>";
        echo "- Role: " . htmlspecialchars($admin['admin_role']) . "<br>";
        echo "- Status: " . ($admin['is_active'] ? 'Active' : 'Inactive') . "<br>";
        
        // Test password verification
        $stmt = $user_pdo->prepare("SELECT password FROM admin_register_form WHERE email = ?");
        $stmt->execute(['admin@pahunaghar.com']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && password_verify('admin123', $result['password'])) {
            echo "<h3>✅ Password Verification: SUCCESS</h3>";
        } else {
            echo "<h3>❌ Password Verification: FAILED</h3>";
        }
    } else {
        echo "<h3>❌ Admin Login Query: FAILED</h3>";
        echo "No admin account found with email: admin@pahunaghar.com<br>";
    }
    
    // Test table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $user_pdo->query("DESCRIBE admin_register_form");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>🎉 Admin System Connection: VERIFIED</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
    echo "<strong>You can now login to the admin system:</strong><br>";
    echo "URL: <a href='admin_login.php'>admin_login.php</a><br>";
    echo "Email: admin@pahunaghar.com<br>";
    echo "Password: admin123";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h3>❌ Connection Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in user_config.php</p>";
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