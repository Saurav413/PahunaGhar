<?php
require_once 'config.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('create_reviews_table.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Reviews table setup completed successfully!";
    echo "<br><a href='user_bookings.php'>Go to My Bookings</a>";
    
} catch (PDOException $e) {
    echo "Error setting up reviews table: " . $e->getMessage();
}
?> 