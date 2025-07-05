<?php
// Fix script for admin_register_form table structure
require_once 'user_config.php';

echo "<h2>Fixing Admin Table Structure</h2>";

try {
    // Step 1: Check current table structure
    echo "<h3>Step 1: Checking current table structure...</h3>";
    $stmt = $user_pdo->query("DESCRIBE admin_register_form");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current columns:<br>";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
    }
    
    // Step 2: Add missing columns
    echo "<h3>Step 2: Adding missing columns...</h3>";
    
    // Check if admin_role column exists
    $hasAdminRole = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'admin_role') {
            $hasAdminRole = true;
            break;
        }
    }
    
    if (!$hasAdminRole) {
        echo "❌ admin_role column missing. Adding it...<br>";
        $user_pdo->exec("ALTER TABLE admin_register_form ADD COLUMN admin_role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin' AFTER password");
        echo "✅ admin_role column added successfully!<br>";
    } else {
        echo "✅ admin_role column already exists.<br>";
    }
    
    // Check if last_login column exists
    $hasLastLogin = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'last_login') {
            $hasLastLogin = true;
            break;
        }
    }
    
    if (!$hasLastLogin) {
        echo "❌ last_login column missing. Adding it...<br>";
        $user_pdo->exec("ALTER TABLE admin_register_form ADD COLUMN last_login TIMESTAMP NULL AFTER created_at");
        echo "✅ last_login column added successfully!<br>";
    } else {
        echo "✅ last_login column already exists.<br>";
    }
    
    // Check if is_active column exists
    $hasIsActive = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'is_active') {
            $hasIsActive = true;
            break;
        }
    }
    
    if (!$hasIsActive) {
        echo "❌ is_active column missing. Adding it...<br>";
        $user_pdo->exec("ALTER TABLE admin_register_form ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER last_login");
        echo "✅ is_active column added successfully!<br>";
    } else {
        echo "✅ is_active column already exists.<br>";
    }
    
    // Step 3: Update existing admin accounts
    echo "<h3>Step 3: Updating existing admin accounts...</h3>";
    
    // Check if there are any admin accounts
    $stmt = $user_pdo->query("SELECT COUNT(*) FROM admin_register_form");
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo "Found $count admin account(s). Updating them...<br>";
        
        // Update admin_role for existing accounts
        $user_pdo->exec("UPDATE admin_register_form SET admin_role = 'super_admin' WHERE admin_role IS NULL OR admin_role = ''");
        echo "✅ Updated admin roles for existing accounts.<br>";
        
        // Update is_active for existing accounts
        $user_pdo->exec("UPDATE admin_register_form SET is_active = TRUE WHERE is_active IS NULL");
        echo "✅ Updated is_active status for existing accounts.<br>";
    } else {
        echo "No admin accounts found. Creating default admin...<br>";
        
        // Create default admin account
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insertStmt = $user_pdo->prepare("
            INSERT INTO admin_register_form (name, email, password, admin_role, is_active) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertStmt->execute(['Super Admin', 'admin@pahunaghar.com', $hashed_password, 'super_admin', TRUE]);
        echo "✅ Default admin account created successfully!<br>";
    }
    
    // Step 4: Create indexes if they don't exist
    echo "<h3>Step 4: Creating indexes...</h3>";
    
    try {
        $user_pdo->exec("CREATE INDEX idx_admin_email ON admin_register_form(email)");
        echo "✅ Email index created.<br>";
    } catch (Exception $e) {
        echo "ℹ️ Email index already exists.<br>";
    }
    
    try {
        $user_pdo->exec("CREATE INDEX idx_admin_role ON admin_register_form(admin_role)");
        echo "✅ Role index created.<br>";
    } catch (Exception $e) {
        echo "ℹ️ Role index already exists.<br>";
    }
    
    try {
        $user_pdo->exec("CREATE INDEX idx_admin_active ON admin_register_form(is_active)");
        echo "✅ Active status index created.<br>";
    } catch (Exception $e) {
        echo "ℹ️ Active status index already exists.<br>";
    }
    
    // Step 5: Display final table structure
    echo "<h3>Step 5: Final table structure:</h3>";
    $stmt = $user_pdo->query("DESCRIBE admin_register_form");
    $finalColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($finalColumns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 6: Display admin accounts
    echo "<h3>Step 6: Current admin accounts:</h3>";
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
    } else {
        echo "❌ No admin accounts found.<br>";
    }
    
    echo "<h3>✅ Admin Table Fix Complete!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
    echo "<strong>Login Credentials:</strong><br>";
    echo "Email: <strong>admin@pahunaghar.com</strong><br>";
    echo "Password: <strong>admin123</strong><br><br>";
    echo "<strong>Login URL:</strong> <a href='admin_login.php'>admin_login.php</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h3>❌ Fix Failed</h3>";
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