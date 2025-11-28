<?php 
require_once 'config.php';

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT items.*, users.full_name as submitted_by_name FROM items LEFT JOIN users ON items.submitted_by = users.id WHERE items.id = ? AND items.status = 'approved'");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: search.php');
    exit();
}

$item = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['title']); ?> - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container my-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="search.php">Search</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($item['title']); ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow">
                    <img src="<?php echo $item['image'] ?: 'assets/images/no-image.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>" style="max-height: 400px; object-fit: cover;">
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                        <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($item['category']); ?></span>
                        
                        <hr>
                        
                        <h5><i class="fas fa-info-circle"></i> Item Details</h5>
                        <div class="mb-3">
                            <p><strong><i class="fas fa-align-left"></i> Description:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                        </div>
                        
                        <p><strong><i class="fas fa-map-marker-alt"></i> Location Found:</strong> <?php echo htmlspecialchars($item['location_found']); ?></p>
                        <p><strong><i class="fas fa-calendar"></i> Date Found:</strong> <?php echo date('F j, Y', strtotime($item['date_found'])); ?></p>
                        <p><strong><i class="fas fa-user"></i> Reported By:</strong> <?php echo htmlspecialchars($item['submitted_by_name']); ?></p>
                        <p><strong><i class="fas fa-clock"></i> Posted:</strong> <?php echo date('F j, Y g:i A', strtotime($item['created_at'])); ?></p>
                        
                        <hr>
                        
                        <?php if ($item['status'] !== 'claimed'): ?>
                            <?php if (isLoggedIn()): ?>
                                <a href="claim.php?id=<?php echo $item['id']; ?>" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-hand-paper"></i> Claim This Item
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-sign-in-alt"></i> Login to Claim
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> This item has been claimed
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>