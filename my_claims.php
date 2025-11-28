<?php 
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Claims - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-clipboard-check"></i> My Claim Requests</h2>
        
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5><i class="fas fa-info-circle"></i> Claim Status Guide</h5>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <span class="badge bg-warning"><i class="fas fa-clock"></i> PENDING</span>
                        <p class="small mt-2">Your claim is under review</p>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> APPROVED</span>
                        <p class="small mt-2">Your claim has been approved! Contact info provided</p>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> REJECTED</span>
                        <p class="small mt-2">Your claim was not approved</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="claimsContainer">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadClaims();
        });

        function loadClaims() {
            $.ajax({
                url: 'api/get_my_claims.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayClaims(response.claims);
                    }
                },
                error: function() {
                    $('#claimsContainer').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error loading claims. Please refresh the page.</div>');
                }
            });
        }

        function displayClaims(claims) {
            let html = '';
            
            if (claims.length === 0) {
                html = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> You have not submitted any claim requests yet. <a href="search.php">Search for items</a> to get started.</div>';
            } else {
                claims.forEach(function(claim) {
                    let statusClass = '';
                    let statusIcon = '';
                    let statusText = '';
                    
                    switch(claim.status) {
                        case 'pending':
                            statusClass = 'warning';
                            statusIcon = 'clock';
                            statusText = 'Under Review';
                            break;
                        case 'approved':
                            statusClass = 'success';
                            statusIcon = 'check-circle';
                            statusText = 'Approved - Please collect your item';
                            break;
                        case 'rejected':
                            statusClass = 'danger';
                            statusIcon = 'times-circle';
                            statusText = 'Not Approved';
                            break;
                    }
                    
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="${claim.item_image || 'assets/images/no-image.jpg'}" class="img-fluid rounded" alt="${claim.item_title}">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5>${claim.item_title}</h5>
                                            <span class="badge bg-${statusClass}"><i class="fas fa-${statusIcon}"></i> ${claim.status.toUpperCase()}</span>
                                        </div>
                                        <p class="text-muted">${claim.item_description.substring(0, 150)}...</p>
                                        <hr>
                                        <p><strong><i class="fas fa-clipboard-list"></i> Your Claim Description:</strong></p>
                                        <p class="text-muted">${claim.description}</p>
                                        <p><strong><i class="fas fa-phone"></i> Contact Info:</strong> ${claim.contact_info}</p>
                                        <p><strong><i class="fas fa-clock"></i> Submitted:</strong> ${new Date(claim.created_at).toLocaleString()}</p>
                                        <div class="alert alert-${statusClass} mb-0">
                                            <i class="fas fa-${statusIcon}"></i> <strong>${statusText}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            $('#claimsContainer').html(html);
        }
    </script>
</body>
</html>