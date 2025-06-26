<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_hotels':
        getPublicHotels();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getPublicHotels() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC");
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'hotels' => $hotels]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?> 