<?php
session_start();
require_once 'config.php';

echo "<h2>🔍 Booking Status Debug</h2>";

// Check current session
echo "<h3>Current Session Status:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
    echo "❌ User not logged in to main system<br>";
    echo "You need to login to the main PahunaGhar system first.<br>";
    echo "<a href='login.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Go to Main Login</a>";
    echo "</div>";
    exit;
}

// Get booking ID from URL or POST
$booking_id = $_GET['booking_id'] ?? $_POST['booking_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

echo "<h3>Booking Information:</h3>";
echo "User ID: " . $user_id . "<br>";
echo "Booking ID: " . $booking_id . "<br>";

// Check booking details
if ($booking_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            echo "<div style='background:#d4edda;padding:15px;border-radius:5px;color:#155724;'>";
            echo "✅ Booking found!<br>";
            echo "Booking ID: " . $booking['id'] . "<br>";
            echo "User ID: " . $booking['user_id'] . "<br>";
            echo "Current Status: <strong>" . $booking['status'] . "</strong><br>";
            echo "Hotel: " . $booking['hotel_name'] . "<br>";
            echo "Check-in: " . $booking['check_in_date'] . "<br>";
            echo "Check-out: " . $booking['check_out_date'] . "<br>";
            echo "Total Price: $" . $booking['total_price'] . "<br>";
            echo "</div>";
            
            // Check if user owns this booking
            if ($booking['user_id'] == $user_id) {
                echo "<div style='background:#d1ecf1;padding:15px;border-radius:5px;color:#0c5460;'>";
                echo "✅ User owns this booking<br>";
                echo "</div>";
            } else {
                echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
                echo "❌ User does not own this booking<br>";
                echo "Booking user_id: " . $booking['user_id'] . "<br>";
                echo "Session user_id: " . $user_id . "<br>";
                echo "</div>";
            }
            
            // Test the update query
            echo "<h3>Testing Update Query:</h3>";
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ? AND status = 'available'");
            $stmt->execute([$booking_id, $user_id]);
            $rowCount = $stmt->rowCount();
            
            echo "Rows affected: " . $rowCount . "<br>";
            
            if ($rowCount > 0) {
                echo "<div style='background:#d4edda;padding:15px;border-radius:5px;color:#155724;'>";
                echo "✅ Update successful!<br>";
                echo "</div>";
            } else {
                echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
                echo "❌ Update failed!<br>";
                echo "Possible reasons:<br>";
                echo "1. Status is not 'available' (current: " . $booking['status'] . ")<br>";
                echo "2. User ID mismatch<br>";
                echo "3. Booking ID not found<br>";
                echo "</div>";
                
                // Show what the query is looking for
                echo "<h4>Query Conditions:</h4>";
                echo "WHERE id = " . $booking_id . " AND user_id = " . $user_id . " AND status = 'available'<br>";
                echo "Current booking status: " . $booking['status'] . "<br>";
            }
            
        } else {
            echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
            echo "❌ No booking found with ID: " . $booking_id . "<br>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
        echo "❌ Database error: " . $e->getMessage() . "<br>";
        echo "</div>";
    }
} else {
    echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;color:#856404;'>";
    echo "⚠️ No booking ID provided<br>";
    echo "Add ?booking_id=X to the URL to test a specific booking<br>";
    echo "</div>";
}

// Show all bookings for this user
echo "<h3>All Bookings for User " . $user_id . ":</h3>";
try {
    $stmt = $pdo->prepare("SELECT id, hotel_name, status, total_price FROM bookings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($bookings) {
        echo "<table style='width:100%;border-collapse:collapse;background:#2d323c;border-radius:8px;overflow:hidden;'>";
        echo "<tr style='background:#353945;'>";
        echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>ID</th>";
        echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Hotel</th>";
        echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Status</th>";
        echo "<th style='padding:12px;text-align:left;border-bottom:1px solid #4b5563;'>Price</th>";
        echo "</tr>";
        
        foreach ($bookings as $booking) {
            echo "<tr>";
            echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>" . $booking['id'] . "</td>";
            echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>" . $booking['hotel_name'] . "</td>";
            echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>" . $booking['status'] . "</td>";
            echo "<td style='padding:12px;border-bottom:1px solid #4b5563;'>$" . $booking['total_price'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
        echo "❌ No bookings found for user " . $user_id . "<br>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "</div>";
}

// Fix button
echo "<h3>🔧 Quick Fix:</h3>";
echo "<form method='post'>";
echo "<input type='submit' name='fix_booking_status' value='Fix Booking Status (Remove Status Check)' style='background:#dc3545;color:white;padding:10px;border:none;border-radius:5px;cursor:pointer;'>";
echo "</form>";

// Handle fix
if (isset($_POST['fix_booking_status']) && $booking_id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $user_id]);
        $rowCount = $stmt->rowCount();
        
        if ($rowCount > 0) {
            echo "<div style='background:#d4edda;padding:15px;border-radius:5px;color:#155724;'>";
            echo "✅ Booking status updated to 'confirmed'!<br>";
            echo "Refresh the page to see the changes.<br>";
            echo "</div>";
        } else {
            echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
            echo "❌ Failed to update booking status.<br>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;'>";
        echo "❌ Database error: " . $e->getMessage() . "<br>";
        echo "</div>";
    }
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
h2, h3, h4 {
    color: #333;
}
pre {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
table {
    margin-top: 10px;
}
</style> 