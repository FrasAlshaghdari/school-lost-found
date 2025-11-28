<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT claims.*, items.title as item_title, items.description as item_description, items.image as item_image 
        FROM claims 
        JOIN items ON claims.item_id = items.id 
        WHERE claims.user_id = ? 
        ORDER BY claims.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$claims = [];
while ($row = $result->fetch_assoc()) {
    $claims[] = $row;
}

echo json_encode(['success' => true, 'claims' => $claims]);
?>