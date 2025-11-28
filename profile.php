<?php 
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $student_id = trim($_POST['student_id']);
    
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, student_id = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $student_id, $user_id);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Profile updated successfully!</div>';
        $_SESSION['full_name'] = $full_name;
        $user['full_name'] = $full_name;
        $user['email'] = $email;
        $user['student_id'] = $student_id;
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Failed to update profile</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                            <h2 class="mt-3">My Profile</h2>
                        </div>
                        <?php echo $message; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user"></i> Username</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-id-card"></i> Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-id-badge"></i> Student ID</label>
                                <input type="text" name="student_id" class="form-control" value="<?php echo htmlspecialchars($user['student_id']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user-tag"></i> Role</label>
                                <input type="text" class="form-control" value="<?php echo ucfirst(htmlspecialchars($user['role'])); ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-calendar-plus"></i> Member Since</label>
                                <input type="text" class="form-control" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" disabled>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>