<?php
$current_path = $_SERVER['PHP_SELF'];
$isAdminPage = (strpos($current_path, '/admin/') !== false);
$base = $isAdminPage ? '../' : '';
?>

<footer class="bg-dark text-white py-4 mt-auto">
    <div class="container">
        <div class="row">
            <!-- Column 1: About -->
            <div class="col-md-4 mb-3">
                <h5 class="text-white">
                    <i class="fas fa-box-open me-2"></i>
                    School Lost &amp; Found
                </h5>
                <p class="footer-text">
                    Helping our school community reunite with lost belongings.
                </p>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-md-4 mb-3">
                <h5 class="text-white">Quick Links</h5>
                <ul class="list-unstyled">
                    <li>
                        <a href="<?php echo $base; ?>search.php" class="footer-link">
                            Search Items
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>report.php" class="footer-link">
                            Report Found Item
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>login.php" class="footer-link">
                            Login
                        </a>
                    </li>
                    <li>
                        <a href="https://zhs.pasco.k12.fl.us/" target="_blank" class="footer-link">
                            Zephyrhills High School
                        </a>
                    </li>
                    <li>
                        <a href="https://www.pasco.k12.fl.us/" target="_blank" class="footer-link">
                            Pasco County Schools
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Contact -->
            <div class="col-md-4 mb-3">
                <h5 class="text-white">Contact Us</h5>
                <p class="footer-text">
                    <i class="fas fa-envelope me-2"></i>
                    <a href="mailto:Fras.alshaghdarii@gmail.com" class="footer-link">
                        Fras.alshaghdarii@gmail.com
                    </a><br>

                    <i class="fas fa-envelope-open me-2"></i>
                    <a href="<?php echo $base; ?>contact.php" class="footer-link">
                        Contact Form
                    </a><br>

                    <i class="fas fa-phone me-2"></i>
                    <a href="tel:+18134597866" class="footer-link">
                        (813) 459-7866
                    </a>
                </p>
            </div>
        </div>

        <hr class="footer-divider">

        <p class="text-center mb-1 small">
            &copy; <?php echo date('Y'); ?> School Lost &amp; Found. All rights reserved.
        </p>
        <p class="text-center mb-0 small">
            Developed by <strong>Fras Alshaghdari</strong>
        </p>
    </div>
</footer>
