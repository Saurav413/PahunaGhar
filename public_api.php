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
    $debug = '';
    try {
        // Get hotels with review count and average rating
        $stmt = $pdo->query("
            SELECT h.*, 
                   COUNT(r.id) as review_count,
                   COALESCE(AVG(r.rating), 0) as avg_rating
            FROM hotels h
            LEFT JOIN reviews r ON h.id = r.hotel_id
            GROUP BY h.id
            ORDER BY h.created_at DESC
        ");
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'hotels' => $hotels, 'debug' => $debug]);
    } catch (Exception $e) {
        $debug = $e->getMessage();
        echo json_encode(['error' => 'Database error: ' . $e->getMessage(), 'debug' => $debug]);
    }
}
?> 