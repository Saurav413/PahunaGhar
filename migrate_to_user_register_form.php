<?php
// Migration script to copy data from register_form to user_register_form
require_once 'user_config.php';

echo "Starting migration from register_form to user_register_form...\n";

try {
    // First, check if user_register_form table exists, if not create it
    $stmt = $user_pdo->query("SHOW TABLES LIKE 'user_register_form'");
    if ($stmt->rowCount() == 0) {
        echo "Creating user_register_form table...\n";
        
        // Create the table
        $createTableSQL = "
        CREATE TABLE user_register_form (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            user_type ENUM('customer', 'admin') DEFAULT 'customer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $user_pdo->exec($createTableSQL);
        echo "user_register_form table created successfully.\n";
    }
    
    // Check if register_form table exists
    $stmt = $user_pdo->query("SHOW TABLES LIKE 'register_form'");
    if ($stmt->rowCount() == 0) {
        echo "register_form table does not exist. Nothing to migrate.\n";
        exit;
    }
    
    // Get all data from register_form
    $stmt = $user_pdo->query("SELECT * FROM register_form");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($users) . " users to migrate.\n";
    
    // Copy data to user_register_form
    $insertStmt = $user_pdo->prepare("
        INSERT INTO user_register_form (id, name, email, password, user_type, created_at) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name), 
        password = VALUES(password), 
        user_type = VALUES(user_type), 
        created_at = VALUES(created_at)
    ");
    
    $migratedCount = 0;
    foreach ($users as $user) {
        try {
            $insertStmt->execute([
                $user['id'],
                $user['name'],
                $user['email'],
                $user['password'],
                $user['user_type'],
                $user['created_at']
            ]);
            $migratedCount++;
            echo "Migrated user: " . $user['email'] . "\n";
        } catch (PDOException $e) {
            echo "Error migrating user " . $user['email'] . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "Migration completed. Successfully migrated $migratedCount users.\n";
    
    // Verify the migration
    $stmt = $user_pdo->query("SELECT COUNT(*) FROM user_register_form");
    $newCount = $stmt->fetchColumn();
    echo "Total users in user_register_form: $newCount\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?> 