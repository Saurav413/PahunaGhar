<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($searchTerm) || strlen($searchTerm) < 2) {
        echo json_encode(['suggestions' => []]);
        exit;
    }
    
    try {
        // Get suggestions from hotel names, locations, and prices
        $query = "SELECT DISTINCT 
                    name as suggestion, 
                    'hotel' as type,
                    name as display_text
                  FROM hotels 
                  WHERE name LIKE :search 
                  UNION
                  SELECT DISTINCT 
                    location as suggestion, 
                    'location' as type,
                    location as display_text
                  FROM hotels 
                  WHERE location LIKE :search 
                  UNION
                  SELECT DISTINCT 
                    price as suggestion, 
                    'price' as type,
                    CONCAT('Price: ', price) as display_text
                  FROM hotels 
                  WHERE price LIKE :search
                  ORDER BY suggestion
                  LIMIT 10";
        
        $stmt = $pdo->prepare($query);
        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':search', $searchParam);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['suggestions' => $results]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?> 