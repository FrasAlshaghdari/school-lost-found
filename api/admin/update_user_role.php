<?php
require_once '../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = intval($_POST['user_id']);
$role = trim($_POST['role']);
$admin_password = trim($_POST['admin_password']);

// Validate role
if ($role !== 'admin' && $role !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit();
}

// Prevent admin from changing their own role
if ($user_id === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot change your own role']);
    exit();
}

// Verify admin password
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!password_verify($admin_password, $admin['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    exit();
}

// Update user role
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param("si", $role, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update role']);
}
?>