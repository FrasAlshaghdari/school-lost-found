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
    <title>Manage Claims - Admin Panel</title>
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
                    <a href="manage_items.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-box"></i> Manage Items
                    </a>
                    <a href="manage_claims.php" class="list-group-item list-group-item-action active">
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
                <h2 class="mb-4"><i class="fas fa-clipboard-check"></i> Manage Claims</h2>
                
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-status="pending" href="#">
                            <i class="fas fa-clock"></i> Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="approved" href="#">
                            <i class="fas fa-check-circle"></i> Approved
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="rejected" href="#">
                            <i class="fas fa-times-circle"></i> Rejected
                        </a>
                    </li>
                </ul>
                
                <div id="claimsContainer">
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
            loadClaims(currentStatus);
            
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                loadClaims(currentStatus);
            });
        });

        function loadClaims(status) {
            $('#claimsContainer').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            $.ajax({
                url: '../api/admin/get_claims.php',
                method: 'GET',
                data: { status: status },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayClaims(response.claims);
                    }
                },
                error: function() {
                    $('#claimsContainer').html('<div class="alert alert-danger">Error loading claims</div>');
                }
            });
        }

        function displayClaims(claims) {
            let html = '';
            
            if (claims.length === 0) {
                html = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No claims found</div>';
            } else {
                claims.forEach(function(claim) {
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="../${claim.item_image || 'assets/images/no-image.jpg'}" class="img-fluid rounded" alt="${claim.item_title}">
                                    </div>
                                    <div class="col-md-7">
                                        <h5><i class="fas fa-box"></i> ${claim.item_title}</h5>
                                        <p class="text-muted mb-2">${claim.item_description.substring(0, 100)}...</p>
                                        <hr>
                                        <p><strong><i class="fas fa-user"></i> Claimed by:</strong> ${claim.user_name}</p>
                                        <p><strong><i class="fas fa-envelope"></i> Email:</strong> ${claim.user_email}</p>
                                        <p><strong><i class="fas fa-clipboard-list"></i> Claim Description:</strong></p>
                                        <p class="bg-light p-3 rounded">${claim.description}</p>
                                        <p><strong><i class="fas fa-phone"></i> Contact Info:</strong> ${claim.contact_info}</p>
                                        <p class="text-muted"><small><i class="fas fa-clock"></i> Submitted: ${new Date(claim.created_at).toLocaleString()}</small></p>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <span class="badge bg-${getStatusColor(claim.status)} mb-3">${claim.status.toUpperCase()}</span><br>
                                        ${claim.status === 'pending' ? `
                                            <button class="btn btn-success btn-sm mb-2 w-100" onclick="updateClaimStatus(${claim.id}, 'approved')">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm w-100" onclick="updateClaimStatus(${claim.id}, 'rejected')">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            $('#claimsContainer').html(html);
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'approved': return 'success';
                case 'rejected': return 'danger';
                default: return 'secondary';
            }
        }

        function updateClaimStatus(claimId, status) {
            const action = status === 'approved' ? 'approve' : 'reject';
            if (confirm(`Are you sure you want to ${action} this claim?`)) {
                $.ajax({
                    url: '../api/admin/update_claim_status.php',
                    method: 'POST',
                    data: { claim_id: claimId, status: status },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(`Claim ${status} successfully`);
                            loadClaims(currentStatus);
                        } else {
                            alert('Failed to update claim status');
                        }
                    },
                    error: function() {
                        alert('Error updating claim status');
                    }
                });
            }
        }
    </script>
</body>
</html>