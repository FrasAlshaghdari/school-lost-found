<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Items - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-search"></i> Search Lost Items</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <form id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" id="searchKeyword" class="form-control" placeholder="Search by keyword...">
                        </div>
                        <div class="col-md-3">
                            <select id="searchCategory" class="form-select">
                                <option value="">All Categories</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Books">Books</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Accessories">Accessories</option>
                                <option value="Sports Equipment">Sports Equipment</option>
                                <option value="Keys">Keys</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="searchDate" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="searchResults" class="row g-4">
            <div class="col-12 text-center">
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
            loadItems();
            
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadItems();
            });
        });

        function loadItems() {
            const keyword = $('#searchKeyword').val();
            const category = $('#searchCategory').val();
            const date = $('#searchDate').val();
            
            $('#searchResults').html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            $.ajax({
                url: 'api/get_items.php',
                method: 'GET',
                data: {
                    keyword: keyword,
                    category: category,
                    date: date,
                    status: 'approved'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayItems(response.items);
                    }
                },
                error: function() {
                    $('#searchResults').html('<div class="col-12 text-center"><p class="text-danger">Error loading items. Please try again.</p></div>');
                }
            });
        }

        function displayItems(items) {
            let html = '';
            if (items.length === 0) {
                html = '<div class="col-12 text-center"><div class="alert alert-info"><i class="fas fa-info-circle"></i> No items found matching your search criteria.</div></div>';
            } else {
                items.forEach(function(item) {
                    html += `
                        <div class="col-md-4">
                            <div class="item-card">
                                <img src="${item.image || 'assets/images/no-image.jpg'}" alt="${item.title}">
                                <div class="item-card-body">
                                    <span class="badge bg-primary">${item.category}</span>
                                    <h5 class="mt-2">${item.title}</h5>
                                    <p class="text-muted">${item.description.substring(0, 100)}${item.description.length > 100 ? '...' : ''}</p>
                                    <p class="text-muted small"><i class="fas fa-map-marker-alt"></i> ${item.location_found}</p>
                                    <p class="text-muted small"><i class="fas fa-calendar"></i> ${item.date_found}</p>
                                    <a href="item_detail.php?id=${item.id}" class="btn btn-sm btn-primary">View Details</a>
                                    ${item.status !== 'claimed' ? '<a href="claim.php?id=' + item.id + '" class="btn btn-sm btn-outline-primary">Claim This Item</a>' : '<span class="badge bg-success mt-2">Claimed</span>'}
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            $('#searchResults').html(html);
        }
    </script>
</body>
</html>