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
    <title>Report Found Item - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="mb-4"><i class="fas fa-plus-circle"></i> Report a Found Item</h2>
                        <div id="alertMessage"></div>
                        
                        <form id="reportForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-tag"></i> Item Title *</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g., Black iPhone 13" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-list"></i> Category *</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Books">Books</option>
                                    <option value="Clothing">Clothing</option>
                                    <option value="Accessories">Accessories</option>
                                    <option value="Sports Equipment">Sports Equipment</option>
                                    <option value="Keys">Keys</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-align-left"></i> Description *</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Provide detailed description of the item..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-map-marker-alt"></i> Location Found *</label>
                                <input type="text" name="location_found" class="form-control" placeholder="e.g., Library, Cafeteria, Gym" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-calendar"></i> Date Found *</label>
                                <input type="date" name="date_found" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-image"></i> Upload Photo (Optional)</label>
                                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                                <small class="text-muted">Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Your submission will be reviewed by an administrator before being published.
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#imageInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5242880) {
                    alert('File size must be less than 5MB');
                    $(this).val('');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html('<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;">');
                }
                reader.readAsDataURL(file);
            }
        });
        
        $('#reportForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = $('#submitBtn');
            
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');
            
            $.ajax({
                url: 'api/submit_item.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#alertMessage').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> Item reported successfully! It will be reviewed by an administrator.</div>');
                        $('#reportForm')[0].reset();
                        $('#imagePreview').html('');
                        setTimeout(function() {
                            window.location.href = 'search.php';
                        }, 2000);
                    } else {
                        $('#alertMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#alertMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.</div>');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Report');
                }
            });
        });
    </script>
</body>
</html>