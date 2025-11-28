<?php
require_once '../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$status = isset($_GET['status']) ? trim($_GET['status']) : 'pending';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

$sql = "SELECT claims.*, items.title as item_title, items.description as item_description, items.image as item_image, 
        users.full_name as user_name, users.email as user_email 
        FROM claims 
        JOIN items ON claims.item_id = items.id 
        JOIN users ON claims.user_id = users.id 
        WHERE claims.status = ? 
        ORDER BY claims.created_at DESC 
        LIMIT ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $limit);
$stmt->execute();
$result = $stmt->get_result();

$claims = [];
while ($row = $result->fetch_assoc()) {
    $claims[] = $row;
}

echo json_encode(['success' => true, 'claims' => $claims]);
?>