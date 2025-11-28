<?php 
require_once '../config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_items.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-box"></i> Manage Items
                    </a>
                    <a href="manage_claims.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-clipboard-check"></i> Manage Claims
                    </a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="contact_messages.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope"></i> Contact Messages
                    </a>
                </div>
            </div>
            
            <div class="col-md-10">
                <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                
                <div class="row g-4" id="statsCards">
                    <div class="col-12 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-white">
                                <h5><i class="fas fa-clock"></i> Pending Items</h5>
                            </div>
                            <div class="card-body" id="pendingItems">
                                <div class="text-center">
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-info text-white">
                                <h5><i class="fas fa-bell"></i> Recent Claims</h5>
                            </div>
                            <div class="card-body" id="recentClaims">
                                <div class="text-center">
                                    <div class="spinner-border text-info" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadStats();
            loadPendingItems();
            loadRecentClaims();
        });

        function loadStats() {
            $.ajax({
                url: '../api/admin/get_stats.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const stats = response.stats;
                        let html = `
                            <div class="col-md-3">
                                <div class="card bg-primary text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-box fa-3x mb-2"></i>
                                        <h3>${stats.total_items}</h3>
                                        <p class="mb-0">Total Items</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-3x mb-2"></i>
                                        <h3>${stats.pending_items}</h3>
                                        <p class="mb-0">Pending Items</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-3x mb-2"></i>
                                        <h3>${stats.claimed_items}</h3>
                                        <p class="mb-0">Claimed Items</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white shadow">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clipboard-list fa-3x mb-2"></i>
                                        <h3>${stats.total_claims}</h3>
                                        <p class="mb-0">Total Claims</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#statsCards').html(html);
                    }
                }
            });
        }

        function loadPendingItems() {
            $.ajax({
                url: '../api/get_items.php',
                method: 'GET',
                data: { status: 'pending', limit: 5 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.items.length === 0) {
                            html = '<p class="text-muted text-center">No pending items</p>';
                        } else {
                            html = '<ul class="list-group">';
                            response.items.forEach(function(item) {
                                html += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-box me-2"></i>${item.title}</span>
                                        <a href="manage_items.php" class="btn btn-sm btn-warning">Review</a>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                        }
                        $('#pendingItems').html(html);
                    }
                }
            });
        }

        function loadRecentClaims() {
            $.ajax({
                url: '../api/admin/get_claims.php',
                method: 'GET',
                data: { limit: 5 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.claims.length === 0) {
                            html = '<p class="text-muted text-center">No recent claims</p>';
                        } else {
                            html = '<ul class="list-group">';
                            response.claims.forEach(function(claim) {
                                html += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-clipboard-list me-2"></i>${claim.item_title} - ${claim.user_name}</span>
                                        <a href="manage_claims.php" class="btn btn-sm btn-info">Review</a>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                        }
                        $('#recentClaims').html(html);
                    }
                }
            });
        }
    </script>
</body>
</html>