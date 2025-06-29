<?php
session_start();
require_once 'user_config.php';
require_once 'config.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$isAdmin = true; // Now properly authenticated

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_recent_hotels':
        getRecentHotels();
        break;
    case 'delete_hotel':
        deleteHotel();
        break;
    case 'add_hotel':
        addHotel();
        break;
    case 'update_hotel':
        updateHotel();
        break;
    case 'get_hotels':
        getHotels();
        break;
    case 'get_users':
        getUsers();
        break;
    case 'get_recent_users':
        getRecentUsers();
        break;
    case 'get_recent_bookings':
        getRecentBookings();
        break;
    case 'get_recent_reviews':
        getRecentReviews();
        break;
    case 'delete_user':
        deleteUser();
        break;
    case 'update_user_type':
        updateUserType();
        break;
    case 'get_all_contacts':
        getAllContacts();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getRecentHotels() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT id, name, location FROM hotels ORDER BY created_at DESC LIMIT 5");
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'hotels' => $hotels]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteHotel() {
    global $pdo;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid request method']);
        return;
    }
    
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        echo json_encode(['error' => 'Hotel ID is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Hotel deleted successfully']);
        } else {
            echo json_encode(['error' => 'Failed to delete hotel']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function addHotel() {
    global $pdo;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid request method']);
        return;
    }
    
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $rating = $_POST['rating'] ?? 0;
    $image_url = $_POST['image_url'] ?? '';
    
    if (empty($name) || empty($location) || empty($price)) {
        echo json_encode(['error' => 'Name, location, and price are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO hotels (name, location, description, price, rating, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $location, $description, $price, $rating, $image_url]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Hotel added successfully', 'id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['error' => 'Failed to add hotel']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateHotel() {
    global $pdo;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid request method']);
        return;
    }
    
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $rating = $_POST['rating'] ?? 0;
    $image_url = $_POST['image_url'] ?? '';
    
    if (empty($id) || empty($name) || empty($location) || empty($price)) {
        echo json_encode(['error' => 'ID, name, location, and price are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE hotels SET name = ?, location = ?, description = ?, price = ?, rating = ?, image_url = ? WHERE id = ?");
        $result = $stmt->execute([$name, $location, $description, $price, $rating, $image_url, $id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Hotel updated successfully']);
        } else {
            echo json_encode(['error' => 'Failed to update hotel']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getHotels() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC");
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'hotels' => $hotels]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getUsers() {
    global $pdo;
    
    try {
        // Check if users table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() == 0) {
            echo json_encode(['success' => true, 'users' => []]);
            return;
        }
        
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'users' => $users]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentUsers() {
    global $user_pdo;
    
    try {
        $stmt = $user_pdo->query("SELECT id, name, email, user_type FROM register_form ORDER BY id DESC LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'users' => $users]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentBookings() {
    global $user_pdo;
    
    try {
        // Check if bookings table exists
        $stmt = $user_pdo->query("SHOW TABLES LIKE 'bookings'");
        if ($stmt->rowCount() == 0) {
            echo json_encode(['success' => true, 'bookings' => []]);
            return;
        }
        
        $stmt = $user_pdo->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'bookings' => $bookings]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentReviews() {
    global $user_pdo;
    
    try {
        // Check if reviews table exists
        $stmt = $user_pdo->query("SHOW TABLES LIKE 'reviews'");
        if ($stmt->rowCount() == 0) {
            echo json_encode(['success' => true, 'reviews' => []]);
            return;
        }
        
        $stmt = $user_pdo->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 5");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'reviews' => $reviews]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteUser() {
    global $user_pdo;
    $userId = intval($_POST['user_id'] ?? 0);
    $currentAdminId = $_SESSION['user_id'];
    if ($userId === $currentAdminId) {
        echo json_encode(['success' => false, 'error' => 'You cannot delete your own account.']);
        return;
    }
    try {
        $stmt = $user_pdo->prepare('DELETE FROM register_form WHERE id = ?');
        $stmt->execute([$userId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateUserType() {
    global $user_pdo;
    $userId = intval($_POST['user_id'] ?? 0);
    $userType = $_POST['user_type'] ?? '';
    if (!in_array($userType, ['admin', 'customer'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid user type.']);
        return;
    }
    try {
        $stmt = $user_pdo->prepare('UPDATE register_form SET user_type = ? WHERE id = ?');
        $stmt->execute([$userType, $userId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getAllContacts() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM contact ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'contacts' => $contacts]);
}
?> 