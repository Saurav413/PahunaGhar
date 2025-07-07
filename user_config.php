<?php
// Database configuration for PahunaGhar
$host = 'localhost';
$dbname = 'PahunaGhar';
$username = 'root';
$password = '';

try {
    $user_pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $user_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo = $user_pdo;
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?> 