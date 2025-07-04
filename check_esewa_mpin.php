<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

error_log('Session eSewa ID: ' . ($_SESSION['esewa_id'] ?? 'NOT SET'));

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo 'fail';
    exit;
}

$esewa_id = $_SESSION['esewa_id'] ?? null;
$mpin = $_POST['mpin'] ?? '';

if ($esewa_id && $mpin) {
    $stmt = $pdo->prepare("SELECT mpin FROM esewa_users WHERE esewa_id = ?");
    $stmt->execute([$esewa_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['mpin'] === $mpin) {
        echo 'success';
        exit;
    }
}
echo 'fail';

?>
<p>Logged in as eSewa ID: <?php echo htmlspecialchars($_SESSION['esewa_id']); ?></p> 