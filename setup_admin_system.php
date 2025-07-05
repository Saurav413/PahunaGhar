<?php
// Setup script for admin system
require_once 'user_config.php';

echo "<h2>Admin System Setup</h2>";

try {
    // Step 1: Check if admin_register_form table exists
    echo "<h3>Step 1: Checking admin table...</h3>";
    $stmt = $user_pdo->query("SHOW TABLES LIKE 'admin_register_form'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Admin table does not exist. Creating it now...<br>";
        
        // Create the admin table
        $createTableSQL = "
        CREATE TABLE admin_register_form (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            admin_role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            is_active BOOLEAN DEFAULT TRUE
        )";
        
        $user_pdo->exec($createTableSQL);
        echo "✅ Admin table created successfully!<br>";
        
        // Create indexes
        $user_pdo->exec("CREATE INDEX idx_admin_email ON admin_register_form(email)");
        $user_pdo->exec("CREATE INDEX idx_admin_role ON admin_register_form(admin_role)");
        $user_pdo->exec("CREATE INDEX idx_admin_active ON admin_register_form(is_active)");
        echo "✅ Indexes created successfully!<br>";
    } else {
        echo "✅ Admin table already exists.<br>";
    }
    
    // Step 2: Check if default admin exists
    echo "<h3>Step 2: Checking default admin account...</h3>";
    $stmt = $user_pdo->prepare("SELECT id FROM admin_register_form WHERE email = ?");
    $stmt->execute(['admin@pahunaghar.com']);
    if (!$stmt->fetch()) {
        echo "❌ Default admin account does not exist. Creating it now...<br>";
        
        // Create default admin account (password: admin123)
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insertStmt = $user_pdo->prepare("
            INSERT INTO admin_register_form (name, email, password, admin_role) 
            VALUES (?, ?, ?, ?)
        ");
        $insertStmt->execute(['Super Admin', 'admin@pahunaghar.com', $hashed_password, 'super_admin']);
        echo "✅ Default admin account created successfully!<br>";
    } else {
        echo "✅ Default admin account already exists.<br>";
    }
    
    // Step 3: Display admin accounts
    echo "<h3>Step 3: Current admin accounts:</h3>";
    $stmt = $user_pdo->query("SELECT id, name, email, admin_role, is_active, created_at FROM admin_register_form ORDER BY created_at DESC");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "❌ No admin accounts found.<br>";
    } else {
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
    
    // Step 4: Test database connection
    echo "<h3>Step 4: Testing database connection...</h3>";
    $testStmt = $user_pdo->prepare("SELECT COUNT(*) FROM admin_register_form");
    $testStmt->execute();
    $count = $testStmt->fetchColumn();
    echo "✅ Database connection successful. Found $count admin account(s).<br>";
    
    // Step 5: Provide login instructions
    echo "<h3>Step 5: Login Instructions</h3>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Default Admin Credentials:</strong><br>";
    echo "Email: <strong>admin@pahunaghar.com</strong><br>";
    echo "Password: <strong>admin123</strong><br><br>";
    echo "<strong>Login URL:</strong> <a href='admin_login.php'>admin_login.php</a><br><br>";
    echo "<strong>Important:</strong> Change the default password after first login!";
    echo "</div>";
    
    echo "<h3>✅ Admin System Setup Complete!</h3>";
    echo "<p>You can now login to the admin system using the credentials above.</p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Setup Failed</h3>";
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