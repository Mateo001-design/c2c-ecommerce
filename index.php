<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();

// Featured products (latest active)
$featured = $pdo->query("
    SELECT p.*, u.username AS seller_name, c.name AS category_name,
           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image
    FROM products p
    JOIN users u ON p.seller_id = u.id
    JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 8
")->fetchAll();

$categories = getCategories();
?>

<!-- Hero Banner -->
<section class="hero-banner text-white text-center py-5" style="background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);">
    <div class="container py-4">
        <h1 class="display-4 fw-bold">Buy &amp; Sell Across South Africa</h1>
        <p class="lead mb-4">iTradeZA connects buyers and sellers in the township economy and beyond. List your goods, find great deals, trade with confidence.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?php echo SITE_URL; ?>/products/browse.php" class="btn btn-light btn-lg px-4"><i class="bi bi-search"></i> Browse Products</a>
            <?php if (!isLoggedIn()): ?>
            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-warning btn-lg px-4"><i class="bi bi-person-plus"></i> Start Selling</a>
            <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/products/add.php" class="btn btn-warning btn-lg px-4"><i class="bi bi-plus-circle"></i> List an Item</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="bg-light py-3">
    <div class="container">
        <div class="row text-center">
            <div class="col-4">
                <h4 class="text-success mb-0"><?php echo $pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(); ?></h4>
                <small class="text-muted">Active Listings</small>
            </div>
            <div class="col-4">
                <h4 class="text-success mb-0"><?php echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); ?></h4>
                <small class="text-muted">Registered Users</small>
            </div>
            <div class="col-4">
                <h4 class="text-success mb-0"><?php echo $pdo->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn(); ?></h4>
                <small class="text-muted">Completed Trades</small>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row g-3">
            <?php
            $catIcons = ['bi-cpu','bi-bag','bi-house','bi-truck','bi-book','bi-bicycle','bi-heart-pulse','bi-basket','bi-wrench','bi-three-dots'];
            foreach ($categories as $i => $cat): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="<?php echo SITE_URL; ?>/products/browse.php?cat=<?php echo $cat['id']; ?>" class="text-decoration-none">
                    <div class="card text-center h-100 border-0 shadow-sm category-card">
                        <div class="card-body py-4">
                            <i class="bi <?php echo $catIcons[$i] ?? 'bi-tag'; ?> fs-1 text-success"></i>
                            <p class="mt-2 mb-0 fw-semibold text-dark small"><?php echo sanitize($cat['name']); ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Latest Listings</h2>
            <a href="<?php echo SITE_URL; ?>/products/browse.php" class="btn btn-outline-success">View All</a>
        </div>
        <div class="row g-4">
            <?php if (empty($featured)): ?>
                <p class="text-muted text-center">No listings yet. Be the first to sell!</p>
            <?php else: ?>
                <?php foreach ($featured as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm product-card">
                        <img src="<?php echo SITE_URL . '/' . ($product['image'] ?: 'assets/images/placeholder.png'); ?>"
                             class="card-img-top" alt="<?php echo sanitize($product['title']); ?>" style="height:200px;object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-secondary mb-2" style="width:fit-content;"><?php echo sanitize($product['category_name']); ?></span>
                            <h6 class="card-title"><?php echo sanitize($product['title']); ?></h6>
                            <p class="text-success fw-bold fs-5 mt-auto mb-1"><?php echo formatPrice($product['price']); ?></p>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> <?php echo sanitize($product['location'] ?: 'South Africa'); ?></small>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="<?php echo SITE_URL; ?>/products/view.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="mb-4">How iTradeZA Works</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4">
                    <i class="bi bi-person-plus fs-1 text-success"></i>
                    <h5 class="mt-3">1. Register</h5>
                    <p class="text-muted">Create a free account in seconds. Verify your identity to become a trusted seller.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="bi bi-camera fs-1 text-success"></i>
                    <h5 class="mt-3">2. List or Browse</h5>
                    <p class="text-muted">Take a photo, set your price in Rands, and list your item — or browse thousands of deals.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="bi bi-shield-check fs-1 text-success"></i>
                    <h5 class="mt-3">3. Trade Safely</h5>
                    <p class="text-muted">Communicate directly, pay securely, and build your reputation through reviews.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
