<?php
session_start();
require_once 'user_config.php';

// Only allow admin and super admin users
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !in_array($_SESSION['user_type'], ['admin', 'super_admin'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_hotels':
        getHotels();
        break;
    case 'add_hotel':
        addHotel();
        break;
    case 'update_hotel':
        updateHotel();
        break;
    case 'delete_hotel':
        deleteHotel();
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
    case 'get_recent_hotels':
        getRecentHotels();
        break;
    case 'update_user_type':
        updateUserType();
        break;
    case 'delete_user':
        deleteUser();
        break;
    case 'get_all_contacts':
        getAllContacts();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
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

function addHotel() {
    global $pdo;
    try {
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        
        if (empty($name) || empty($location) || empty($price)) {
            echo json_encode(['error' => 'Required fields are missing']);
            return;
        }
        
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/hotels/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['hotel_image']['name'], PATHINFO_EXTENSION);
            $file_name = 'hotel_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['hotel_image']['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO hotels (name, location, description, price, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $location, $description, $price, $image_url]);
        
        echo json_encode(['success' => true, 'message' => 'Hotel added successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateHotel() {
    global $pdo;
    try {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        
        if (empty($id) || empty($name) || empty($location) || empty($price)) {
            echo json_encode(['error' => 'Required fields are missing']);
            return;
        }
        
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/hotels/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['hotel_image']['name'], PATHINFO_EXTENSION);
            $file_name = 'hotel_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['hotel_image']['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            }
        }
        
        if (!empty($image_url)) {
            $stmt = $pdo->prepare("UPDATE hotels SET name = ?, location = ?, description = ?, price = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $location, $description, $price, $image_url, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE hotels SET name = ?, location = ?, description = ?, price = ? WHERE id = ?");
            $stmt->execute([$name, $location, $description, $price, $id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Hotel updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteHotel() {
    global $pdo;
    try {
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo json_encode(['error' => 'Hotel ID is required']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Hotel deleted successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentUsers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM user_register_form ORDER BY created_at DESC LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'users' => $users]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentBookings() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT b.*, u.name as user_name, h.name as hotel_name 
            FROM bookings b 
            JOIN user_register_form u ON b.user_id = u.id 
            JOIN hotels h ON b.hotel_id = h.id 
            ORDER BY b.created_at DESC 
            LIMIT 5
        ");
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'bookings' => $bookings]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentReviews() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT r.*, u.name as user_name, h.name as hotel_name 
            FROM reviews r 
            JOIN user_register_form u ON r.user_id = u.id 
            JOIN hotels h ON r.hotel_id = h.id 
            ORDER BY r.review_date DESC 
            LIMIT 5
        ");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'reviews' => $reviews]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getRecentHotels() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC LIMIT 5");
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'hotels' => $hotels]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateUserType() {
    global $pdo;
    try {
        $user_id = $_POST['user_id'] ?? '';
        $new_type = $_POST['new_type'] ?? '';
        
        if (empty($user_id) || empty($new_type)) {
            echo json_encode(['error' => 'User ID and new type are required']);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE user_register_form SET user_type = ? WHERE id = ?");
        $stmt->execute([$new_type, $user_id]);
        
        echo json_encode(['success' => true, 'message' => 'User type updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteUser() {
    global $pdo;
    try {
        $user_id = $_POST['user_id'] ?? '';
        
        if (empty($user_id)) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM user_register_form WHERE id = ?");
        $stmt->execute([$user_id]);
        
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getAllContacts() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM contact ORDER BY created_at DESC");
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'contacts' => $contacts]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?> 