<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();
$adminUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' | ' : ''; ?>Admin – iTradeZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>/admin/">
            <i class="bi bi-speedometer2"></i> iTradeZA Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/users.php"><i class="bi bi-people"></i> Users</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/products.php"><i class="bi bi-box"></i> Products</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/orders.php"><i class="bi bi-receipt"></i> Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/roles.php"><i class="bi bi-shield-lock"></i> Roles</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/"><i class="bi bi-globe"></i> View Site</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (desktop) -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar py-3" style="min-height:calc(100vh - 56px);">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/"><i class="bi bi-house"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/users.php"><i class="bi bi-people"></i> Users</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/products.php"><i class="bi bi-box"></i> Products</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/orders.php"><i class="bi bi-receipt"></i> Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/admin/roles.php"><i class="bi bi-shield-lock"></i> Roles (RBAC)</a></li>
                <hr>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/"><i class="bi bi-globe"></i> View Site</a></li>
            </ul>
        </nav>
        <main class="col-md-10 ms-sm-auto px-4 py-3">
            <?php echo displayFlash(); ?>
