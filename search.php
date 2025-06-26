<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
    
    if (empty($searchTerm)) {
        echo json_encode(['error' => 'Search term is required']);
        exit;
    }
    
    try {
        // Search in hotel name, location, and price range
        $query = "SELECT * FROM hotels WHERE 
                  name LIKE :search OR 
                  location LIKE :search OR 
                  description LIKE :search OR
                  price LIKE :search OR
                  CAST(REPLACE(REPLACE(price, '$', ''), '/night', '') AS DECIMAL(10,2)) = :price";
        
        $stmt = $pdo->prepare($query);
        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':search', $searchParam);
        
        // Try to parse price if search term looks like a number
        $price = null;
        if (is_numeric($searchTerm)) {
            $price = floatval($searchTerm);
        }
        $stmt->bindParam(':price', $price);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'hotels' => $results]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?> 