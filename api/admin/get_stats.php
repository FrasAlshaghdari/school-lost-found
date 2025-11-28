<?php
require_once '../../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$stats = [];

// Total items
$result = $conn->query("SELECT COUNT(*) as count FROM items");
$stats['total_items'] = $result->fetch_assoc()['count'];

// Pending items
$result = $conn->query("SELECT COUNT(*) as count FROM items WHERE status = 'pending'");
$stats['pending_items'] = $result->fetch_assoc()['count'];

// Claimed items
$result = $conn->query("SELECT COUNT(*) as count FROM items WHERE status = 'claimed'");
$stats['claimed_items'] = $result->fetch_assoc()['count'];

// Total claims
$result = $conn->query("SELECT COUNT(*) as count FROM claims");
$stats['total_claims'] = $result->fetch_assoc()['count'];

echo json_encode(['success' => true, 'stats' => $stats]);
?>