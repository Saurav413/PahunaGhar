<?php
// Simple test script to check image upload functionality
require_once 'config.php';

echo "<h1>Image Upload Test</h1>";

// Check if uploads directory exists
$uploadDir = __DIR__ . '/uploads/hotels/';
echo "<p>Upload directory: " . $uploadDir . "</p>";
echo "<p>Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</p>";

// List files in uploads directory
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    echo "<p>Files in uploads directory:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

// Check database for hotels with uploaded images
try {
    $stmt = $pdo->query("SELECT id, name, image_url FROM hotels WHERE image_url LIKE 'uploads/%'");
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Hotels with uploaded images:</h2>";
    if (count($hotels) > 0) {
        echo "<ul>";
        foreach ($hotels as $hotel) {
            $imagePath = __DIR__ . '/' . $hotel['image_url'];
            $fileExists = file_exists($imagePath);
            echo "<li>";
            echo "Hotel: " . htmlspecialchars($hotel['name']) . "<br>";
            echo "Image URL: " . htmlspecialchars($hotel['image_url']) . "<br>";
            echo "File exists: " . ($fileExists ? 'Yes' : 'No') . "<br>";
            if ($fileExists) {
                echo "<img src='" . htmlspecialchars($hotel['image_url']) . "' style='max-width: 200px; max-height: 150px;'><br>";
            }
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hotels with uploaded images found.</p>";
    }
} catch (Exception $e) {
    echo "<p>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 