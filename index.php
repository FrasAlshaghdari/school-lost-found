<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Lost Something?<br>We're Here to Help!</h1>
                    <p class="lead mb-4">Our school's lost and found system makes it easy to report and search for lost items. Join our community in reuniting people with their belongings.</p>
                    <div class="d-flex gap-3">
                        <a href="search.php" class="btn btn-primary btn-lg"><i class="fas fa-search me-2"></i>Search Items</a>
                        <a href="report.php" class="btn btn-outline-light btn-lg"><i class="fas fa-plus me-2"></i>Report Found Item</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-box-open" style="font-size: 200px; color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="icon-box mb-3">
                        <i class="fas fa-search fa-3x text-primary"></i>
                    </div>
                    <h3>Search Items</h3>
                    <p>Browse through all found items and filter by category, location, or date to find what you're looking for.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="icon-box mb-3">
                        <i class="fas fa-clipboard-list fa-3x text-primary"></i>
                    </div>
                    <h3>Report Found Items</h3>
                    <p>Found something? Report it here with details and photos to help the owner identify their item.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="icon-box mb-3">
                        <i class="fas fa-handshake fa-3x text-primary"></i>
                    </div>
                    <h3>Claim Your Item</h3>
                    <p>Submit a claim request with verification details, and our team will help you get your item back.</p>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h2 class="text-center mb-4">Recently Found Items</h2>
            <div id="recentItems" class="row g-4">
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            loadRecentItems();
        });

        function loadRecentItems() {
            $.ajax({
                url: 'api/get_items.php',
                method: 'GET',
                data: { limit: 6, status: 'approved' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayItems(response.items);
                    }
                },
                error: function() {
                    $('#recentItems').html('<div class="col-12 text-center"><p class="text-danger">Error loading items. Please refresh the page.</p></div>');
                }
            });
        }

        function displayItems(items) {
            let html = '';
            if (items.length === 0) {
                html = '<div class="col-12 text-center"><p>No items found yet. Be the first to report a found item!</p></div>';
            } else {
                items.forEach(function(item) {
                    html += `
                        <div class="col-md-4">
                            <div class="item-card">
                                <img src="${item.image || 'assets/images/no-image.jpg'}" alt="${item.title}">
                                <div class="item-card-body">
                                    <span class="badge bg-primary">${item.category}</span>
                                    <h5 class="mt-2">${item.title}</h5>
                                    <p class="text-muted small"><i class="fas fa-map-marker-alt"></i> ${item.location_found}</p>
                                    <p class="text-muted small"><i class="fas fa-calendar"></i> ${item.date_found}</p>
                                    <a href="item_detail.php?id=${item.id}" class="btn btn-sm btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            $('#recentItems').html(html);
        }
    </script>
</body>
</html>