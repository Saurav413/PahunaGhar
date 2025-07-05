<?php
session_start();
require_once 'config.php';

// DEBUG: Force set a test user
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // or whatever user ID you want to test
    $_SESSION['logged_in'] = true;
}

echo "<h2>🧪 Test Booking Status Update</h2>";

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
    echo "❌ User not logged in to main system<br>";
    echo "<a href='login.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Go to Main Login</a>";
    echo "</div>";
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

// Get user's bookings
try {
    $stmt = $pdo->prepare("SELECT id, hotel_name, status, total_price FROM bookings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($bookings)) {
        echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;color:#856404;'>";
        echo "⚠️ No bookings found for user " . $user_id . "<br>";
        echo "You need to create a booking first.<br>";
        echo "</div>";
        exit;
    }
    
    echo "<h3>Your Bookings:</h3>";
    echo "<table style='width:100%;border-collapse:collapse;background:#2d323c;border-radius:8px;overflow:hidden;'>";
    echo "<tr style='background:#353945;'>";
    echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>ID</th>";
    echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Hotel</th>";
    echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Status</th>";
    echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Price</th>";
    echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Action</th>";
    echo "</tr>";
    
    foreach ($bookings as $booking) {
        echo "<tr>";
        echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>" . $booking['id'] . "</td>";
        echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>" . $booking['hotel_name'] . "</td>";
        echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>" . $booking['status'] . "</td>";
        echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>$" . $booking['total_price'] . "</td>";
        echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='booking_id' value='" . $booking['id'] . "'>";
        echo "<input type='submit' name='test_update' value='Test Update' style='background:#007bff;color:white;padding:5px 10px;border:none;border-radius:3px;cursor:pointer;'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "</div>";
}

// Test booking update
if (isset($_POST['test_update']) && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    
    echo "<h3>Testing Booking Update:</h3>";
    echo "Booking ID: " . $booking_id . "<br>";
    echo "User ID: " . $user_id . "<br>";
    
    try {
        // Test the old method (with status check)
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ? AND status = 'available'");
        $stmt->execute([$booking_id, $user_id]);
        $oldResult = $stmt->rowCount();
        
        echo "<strong>Old Method (with status check):</strong> " . ($oldResult > 0 ? "✅ Success" : "❌ Failed") . "<br>";
        
        // Test the new method (without status check)
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $user_id]);
        $newResult = $stmt->rowCount();
        
        echo "<strong>New Method (without status check):</strong> " . ($newResult > 0 ? "✅ Success" : "❌ Failed") . "<br>";
        
        if ($newResult > 0) {
            echo "<div style='background:#d4edda;padding:15px;border-radius:5px;color:#155724;'>";
            echo "✅ Booking status updated successfully!<br>";
            echo "The fix is working correctly.<br>";
            echo "</div>";
        } else {
            echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
            echo "❌ Booking update failed.<br>";
            echo "This might be a user ownership issue.<br>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
        echo "❌ Database error: " . $e->getMessage() . "<br>";
        echo "</div>";
    }
}

// Show current session info
echo "<h3>Current Session:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
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
pre {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
</style> 