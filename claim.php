<?php 
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM items WHERE id = ? AND status = 'approved'");
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
    <title>Claim Item - School Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-hand-paper"></i> Claim Item</h2>
        
        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow">
                    <img src="<?php echo $item['image'] ?: 'assets/images/no-image.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="card-body">
                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                        <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($item['category']); ?></span>
                        <p><strong>Location Found:</strong> <?php echo htmlspecialchars($item['location_found']); ?></p>
                        <p><strong>Date Found:</strong> <?php echo date('F j, Y', strtotime($item['date_found'])); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="mb-4">Submit Claim Request</h3>
                        <div id="alertMessage"></div>
                        
                        <form id="claimForm">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> Please provide specific details that prove this item belongs to you.
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-clipboard-list"></i> Describe the item and provide proof of ownership *</label>
                                <textarea name="description" class="form-control" rows="6" placeholder="Please provide specific details about the item that only the owner would know:&#10;- Unique features or markings&#10;- Brand, model, or serial number&#10;- Contents (if applicable)&#10;- Where and when you lost it&#10;- Any other identifying information" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-phone"></i> Contact Information *</label>
                                <input type="text" name="contact_info" class="form-control" placeholder="Phone number or email where we can reach you" required>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Your claim will be reviewed by an administrator. You will be contacted if your claim is approved.
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit Claim Request
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
        $('#claimForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');
            
            $.ajax({
                url: 'api/submit_claim.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#alertMessage').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> Claim submitted successfully! You can track its status in "My Claims".</div>');
                        $('#claimForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = 'my_claims.php';
                        }, 2000);
                    } else {
                        $('#alertMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + response.message + '</div>');
                        submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Claim Request');
                    }
                },
                error: function() {
                    $('#alertMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.</div>');
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Claim Request');
                }
            });
        });
    </script>
</body>
</html>