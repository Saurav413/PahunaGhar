<?php
session_start();
require_once 'user_config.php';

// Only allow admin and super admin users
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'super_admin'])) {
    header('Location: login.php');
    exit;
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    die('Invalid user ID.');
}

// Fetch user info
$stmt = $user_pdo->prepare('SELECT id, name, email, user_type FROM user_register_form WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die('User not found.');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_type = trim($_POST['user_type'] ?? 'user');
    if ($name && $email && in_array($user_type, ['user', 'admin'])) {
        $stmt = $user_pdo->prepare('UPDATE user_register_form SET name = ?, email = ?, user_type = ? WHERE id = ?');
        if ($stmt->execute([$name, $email, $user_type, $user_id])) {
            $success = 'User updated successfully!';
            // Refresh user data
            $stmt = $user_pdo->prepare('SELECT id, name, email, user_type FROM user_register_form WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update user.';
        }
    } else {
        $error = 'Please fill all fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body { background: #f5f8fa; font-family: 'Montserrat', Arial, sans-serif; }
        .edit-user-container { max-width: 500px; margin: 60px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(44,62,80,0.10); padding: 36px 32px; }
        h2 { color: #2563eb; font-size: 2rem; margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #2d3a4b; }
        input, select { width: 100%; padding: 10px; margin-bottom: 18px; border-radius: 8px; border: 1.5px solid #d1d5db; font-size: 1rem; }
        button { background: linear-gradient(135deg,#2563eb,#1e40af); color: #fff; border: none; border-radius: 8px; padding: 12px 0; font-size: 1.1rem; font-weight: 700; width: 100%; cursor: pointer; transition: background 0.2s; }
        button:hover { background: linear-gradient(135deg,#1e40af,#2563eb); }
        .msg-success { color: #10b981; font-weight: 700; margin-bottom: 12px; }
        .msg-error { color: #e74c3c; font-weight: 700; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="edit-user-container">
        <h2>Edit User</h2>
        <?php if ($success): ?><div class="msg-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="msg-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label for="user_type">User Type</label>
            <select id="user_type" name="user_type">
                <option value="user" <?php if ($user['user_type'] === 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($user['user_type'] === 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit">Update User</button>
        </form>
        <a href="admin_users.php" style="display:block;text-align:center;margin-top:18px;color:#2563eb;text-decoration:underline;">&larr; Back to Users</a>
    </div>
</body>
</html> 