<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$item_id = intval($_POST['item_id']);
$description = trim($_POST['description']);
$contact_info = trim($_POST['contact_info']);
$user_id = $_SESSION['user_id'];

// Check if user already claimed this item
$check = $conn->prepare("SELECT id FROM claims WHERE item_id = ? AND user_id = ?");
$check->bind_param("ii", $item_id, $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already submitted a claim for this item']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO claims (item_id, user_id, description, contact_info) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $item_id, $user_id, $description, $contact_info);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Claim submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit claim']);
}
?>