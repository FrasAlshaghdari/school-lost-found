<?php
require_once '../config.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : 'approved';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

$sql = "SELECT items.*, users.full_name as submitted_by_name 
        FROM items 
        LEFT JOIN users ON items.submitted_by = users.id 
        WHERE 1=1";

$params = [];
$types = '';

if ($status) {
    $sql .= " AND items.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($keyword) {
    $sql .= " AND (items.title LIKE ? OR items.description LIKE ?)";
    $keywordParam = '%' . $keyword . '%';
    $params[] = $keywordParam;
    $params[] = $keywordParam;
    $types .= 'ss';
}

if ($category) {
    $sql .= " AND items.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($date) {
    $sql .= " AND items.date_found = ?";
    $params[] = $date;
    $types .= 's';
}

$sql .= " ORDER BY items.created_at DESC LIMIT ?";
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'items' => $items]);
?>