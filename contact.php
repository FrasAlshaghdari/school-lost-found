<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - School Lost & Found</title>
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
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4"><i class="fas fa-envelope"></i> Contact Admin</h2>
                        <p class="text-center text-muted mb-4">Have a question or need help? Send a message to the administrators.</p>
                        
                        <div id="alertMessage"></div>
                        
                        <form id="contactForm">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user"></i> Your Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope"></i> Your Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="your.email@example.com" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-phone"></i> Phone Number (Optional)</label>
                                <input type="tel" name="phone" class="form-control" placeholder="(123) 456-7890">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-tag"></i> Subject *</label>
                                <select name="subject" class="form-select" required>
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Lost Item Question">Lost Item Question</option>
                                    <option value="Found Item Question">Found Item Question</option>
                                    <option value="Claim Issue">Claim Issue</option>
                                    <option value="Technical Problem">Technical Problem</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-comment"></i> Message *</label>
                                <textarea name="message" class="form-control" rows="6" placeholder="Type your message here..." required></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> We'll respond to your message within 24-48 hours.
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow mt-4">
                    <div class="card-body">
                        <h5><i class="fas fa-info-circle"></i> Other Ways to Reach Us</h5>
                        <hr>
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:Fras.alshaghdarii@gmail.com">Fras.alshaghdarii@gmail.com</a></p>
                        <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <a href="tel:+18134597866">(813) 459-7866</a></p>
                        <p><i class="fas fa-school"></i> <strong>School:</strong> <a href="https://zhs.pasco.k12.fl.us/" target="_blank">Zephyrhills High School</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
            
            $.ajax({
                url: 'api/submit_contact.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#alertMessage').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> Message sent successfully! We\'ll get back to you soon.</div>');
                        $('#contactForm')[0].reset();
                    } else {
                        $('#alertMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#alertMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.</div>');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Message');
                }
            });
        });
    </script>
</body>
</html>