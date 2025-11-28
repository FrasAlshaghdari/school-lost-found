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
    <title>Manage Items - Admin Panel</title>
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
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_items.php" class="list-group-item list-group-item-action active">
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
                <h2 class="mb-4"><i class="fas fa-box"></i> Manage Items</h2>
                
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-status="pending" href="#">
                            <i class="fas fa-clock"></i> Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="approved" href="#">
                            <i class="fas fa-check"></i> Approved
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="claimed" href="#">
                            <i class="fas fa-handshake"></i> Claimed
                        </a>
                    </li>
                </ul>
                
                <div id="itemsContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
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
        let currentStatus = 'pending';
        
        $(document).ready(function() {
            loadItems(currentStatus);
            
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                loadItems(currentStatus);
            });
        });

        function loadItems(status) {
            $('#itemsContainer').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            $.ajax({
                url: '../api/get_items.php',
                method: 'GET',
                data: { status: status },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayItems(response.items);
                    }
                },
                error: function() {
                    $('#itemsContainer').html('<div class="alert alert-danger">Error loading items</div>');
                }
            });
        }

        function displayItems(items) {
            let html = '';
            
            if (items.length === 0) {
                html = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No items found</div>';
            } else {
                html = '<div class="table-responsive"><table class="table table-striped table-hover align-middle"><thead class="table-dark"><tr><th>Image</th><th>Title</th><th>Category</th><th>Location</th><th>Date Found</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
                
                items.forEach(function(item) {
                    html += `
                        <tr>
                            <td><img src="../${item.image || 'assets/images/no-image.jpg'}" width="60" class="rounded"></td>
                            <td><strong>${item.title}</strong><br><small class="text-muted">${item.description.substring(0, 50)}...</small></td>
                            <td><span class="badge bg-primary">${item.category}</span></td>
                            <td>${item.location_found}</td>
                            <td>${item.date_found}</td>
                            <td><span class="badge bg-${getStatusColor(item.status)}">${item.status.toUpperCase()}</span></td>
                            <td>
                                ${item.status === 'pending' ? `<button class="btn btn-sm btn-success mb-1" onclick="updateItemStatus(${item.id}, 'approved')"><i class="fas fa-check"></i> Approve</button>` : ''}
                                ${item.status === 'approved' ? `<button class="btn btn-sm btn-warning mb-1" onclick="updateItemStatus(${item.id}, 'claimed')"><i class="fas fa-handshake"></i> Mark Claimed</button>` : ''}
                                <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id})"><i class="fas fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
            }
            
            $('#itemsContainer').html(html);
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'approved': return 'success';
                case 'claimed': return 'info';
                default: return 'secondary';
            }
        }

        function updateItemStatus(itemId, status) {
            if (confirm('Are you sure you want to update this item status?')) {
                $.ajax({
                    url: '../api/admin/update_item_status.php',
                    method: 'POST',
                    data: { item_id: itemId, status: status },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Item status updated successfully');
                            loadItems(currentStatus);
                        } else {
                            alert('Failed to update item status');
                        }
                    },
                    error: function() {
                        alert('Error updating item status');
                    }
                });
            }
        }

        function deleteItem(itemId) {
            if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                $.ajax({
                    url: '../api/admin/delete_item.php',
                    method: 'POST',
                    data: { item_id: itemId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Item deleted successfully');
                            loadItems(currentStatus);
                        } else {
                            alert('Failed to delete item');
                        }
                    },
                    error: function() {
                        alert('Error deleting item');
                    }
                });
            }
        }
    </script>
</body>
</html>