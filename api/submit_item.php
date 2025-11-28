<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$category = trim($_POST['category']);
$location_found = trim($_POST['location_found']);
$date_found = trim($_POST['date_found']);
$user_id = $_SESSION['user_id'];

$image = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 5242880) { // 5MB max
        $newname = uniqid() . '.' . $ext;
        $upload_dir = '../uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $newname)) {
            $image = 'uploads/' . $newname;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file format or size too large']);
        exit();
    }
}

$stmt = $conn->prepare("INSERT INTO items (title, description, category, location_found, date_found, image, submitted_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $title, $description, $category, $location_found, $date_found, $image, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Item submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit item']);
}
?>