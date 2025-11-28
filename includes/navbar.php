<?php
// Determine the base path based on current location
$current_path = $_SERVER['PHP_SELF'];
$isAdminPage = (strpos($current_path, '/admin/') !== false);
$base = $isAdminPage ? '../' : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand text-white" href="<?php echo $base; ?>index.php">
            <i class="fas fa-box-open me-2"></i>School Lost & Found
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base; ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo $base; ?>search.php">Search Items</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo $base; ?>report.php">Report Item</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo $base; ?>my_claims.php">My Claims</a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo $base; ?>admin/dashboard.php">Admin Panel</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base; ?>profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo $base; ?>login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary  ms-2 text-white" href="<?php echo $base; ?>register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>