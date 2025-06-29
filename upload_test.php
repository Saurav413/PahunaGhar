<?php
// Simple upload test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>File Upload Test</h1>";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h2>Upload Results:</h2>";
    
    $file = $_FILES['test_file'];
    echo "<p>File name: " . htmlspecialchars($file['name']) . "</p>";
    echo "<p>File size: " . $file['size'] . " bytes</p>";
    echo "<p>File type: " . htmlspecialchars($file['type']) . "</p>";
    echo "<p>Upload error code: " . $file['error'] . "</p>";
    
    // Check upload error
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            echo "<p style='color: green;'>Upload successful!</p>";
            break;
        case UPLOAD_ERR_INI_SIZE:
            echo "<p style='color: red;'>Error: File exceeds upload_max_filesize</p>";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            echo "<p style='color: red;'>Error: File exceeds MAX_FILE_SIZE</p>";
            break;
        case UPLOAD_ERR_PARTIAL:
            echo "<p style='color: red;'>Error: File was only partially uploaded</p>";
            break;
        case UPLOAD_ERR_NO_FILE:
            echo "<p style='color: red;'>Error: No file was uploaded</p>";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            echo "<p style='color: red;'>Error: Missing temporary folder</p>";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            echo "<p style='color: red;'>Error: Failed to write file to disk</p>";
            break;
        case UPLOAD_ERR_EXTENSION:
            echo "<p style='color: red;'>Error: A PHP extension stopped the file upload</p>";
            break;
        default:
            echo "<p style='color: red;'>Error: Unknown upload error</p>";
    }
    
    // Try to move the file
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/hotels/';
        $newFileName = 'test_' . uniqid() . '_' . basename($file['name']);
        $destPath = $uploadDir . $newFileName;
        
        echo "<p>Upload directory: " . $uploadDir . "</p>";
        echo "<p>Destination path: " . $destPath . "</p>";
        echo "<p>Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "</p>";
        echo "<p>Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</p>";
        
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            echo "<p style='color: green;'>File moved successfully to: " . $destPath . "</p>";
        } else {
            echo "<p style='color: red;'>Failed to move uploaded file</p>";
            echo "<p>PHP error: " . error_get_last()['message'] . "</p>";
        }
    }
}

// Display PHP upload settings
echo "<h2>PHP Upload Settings:</h2>";
echo "<p>file_uploads: " . (ini_get('file_uploads') ? 'On' : 'Off') . "</p>";
echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
echo "<p>upload_tmp_dir: " . ini_get('upload_tmp_dir') . "</p>";

// Check uploads directory
$uploadDir = __DIR__ . '/uploads/hotels/';
echo "<h2>Uploads Directory:</h2>";
echo "<p>Path: " . $uploadDir . "</p>";
echo "<p>Exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
?>

<form method="POST" enctype="multipart/form-data">
    <h2>Test File Upload:</h2>
    <input type="file" name="test_file" accept="image/*" required>
    <br><br>
    <input type="submit" value="Upload Test File">
</form> 