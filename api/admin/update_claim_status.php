<?php
require_once '../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$claim_id = intval($_POST['claim_id']);
$status = trim($_POST['status']);

$stmt = $conn->prepare("UPDATE claims SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $claim_id);

if ($stmt->execute()) {
    // If approved, mark item as claimed
    if ($status === 'approved') {
        $conn->query("UPDATE items SET status = 'claimed' WHERE id = (SELECT item_id FROM claims WHERE id = $claim_id)");
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>