<?php
require_once '../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$item_id = intval($_POST['item_id']);
$status = trim($_POST['status']);

$stmt = $conn->prepare("UPDATE items SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $item_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>