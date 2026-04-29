<?php
require_once __DIR__ . '/functions.php';
$cartCount = getCartCount();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' | ' : ''; ?>iTradeZA – Buy &amp; Sell in South Africa</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>/">
            <i class="bi bi-shop"></i> iTradeZA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <!-- Search -->
            <form class="d-flex mx-auto my-2 my-lg-0" action="<?php echo SITE_URL; ?>/products/browse.php" method="GET" style="max-width:450px;width:100%;">
                <input class="form-control me-2" type="search" name="q" placeholder="Search products..." aria-label="Search"
                       value="<?php echo isset($_GET['q']) ? sanitize($_GET['q']) : ''; ?>">
                <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <!-- Nav Links -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/products/browse.php"><i class="bi bi-grid"></i> Browse</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <?php if (getUserRole() === 'seller' || getUserRole() === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/products/add.php"><i class="bi bi-plus-circle"></i> Sell</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/messages/messages.php">
                            <i class="bi bi-chat-dots"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/cart/cart.php">
                            <i class="bi bi-cart3"></i> Cart
                            <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo sanitize($currentUser['first_name'] ?? 'Account'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile/profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/orders/my_orders.php"><i class="bi bi-bag"></i> My Orders</a></li>
                            <?php if (getUserRole() === 'seller' || getUserRole() === 'admin'): ?>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/orders/my_sales.php"><i class="bi bi-cash-stack"></i> My Sales</a></li>
                            <?php endif; ?>
                            <?php if (getUserRole() === 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="<?php echo SITE_URL; ?>/admin/"><i class="bi bi-speedometer2"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning btn-sm text-dark ms-2 px-3" href="<?php echo SITE_URL; ?>/auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<div class="container mt-3">
    <?php echo displayFlash(); ?>
</div>
