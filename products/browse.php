<?php
$pageTitle = 'Browse Products';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();

// Filters
$search   = trim($_GET['q'] ?? '');
$catId    = (int) ($_GET['cat'] ?? 0);
$sort     = $_GET['sort'] ?? 'newest';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$condition = $_GET['condition'] ?? '';
$page     = max(1, (int) ($_GET['page'] ?? 1));
$perPage  = 12;
$offset   = ($page - 1) * $perPage;

$where  = ["p.status = 'active'"];
$params = [];

if ($search) {
    $where[]  = "(p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catId) {
    $where[]  = "p.category_id = ?";
    $params[] = $catId;
}
if ($minPrice !== '') {
    $where[]  = "p.price >= ?";
    $params[] = (float) $minPrice;
}
if ($maxPrice !== '') {
    $where[]  = "p.price <= ?";
    $params[] = (float) $maxPrice;
}
if ($condition) {
    $where[]  = "p.`condition` = ?";
    $params[] = $condition;
}

$whereSQL = implode(' AND ', $where);

$orderMap = [
    'newest'     => 'p.created_at DESC',
    'price_low'  => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'oldest'     => 'p.created_at ASC',
];
$orderSQL = $orderMap[$sort] ?? 'p.created_at DESC';

// Total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $whereSQL");
$countStmt->execute($params);
$total     = (int) $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

// Products
$sql = "SELECT p.*, u.username AS seller_name, c.name AS category_name,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image
        FROM products p
        JOIN users u ON p.seller_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE $whereSQL
        ORDER BY $orderSQL
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = getCategories();
?>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white"><i class="bi bi-funnel"></i> Filters</div>
                <div class="card-body">
                    <form method="GET">
                        <input type="hidden" name="q" value="<?php echo sanitize($search); ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="cat" class="form-select form-select-sm">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $catId == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($c['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Price Range (ZAR)</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min"
                                       value="<?php echo sanitize($minPrice); ?>">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max"
                                       value="<?php echo sanitize($maxPrice); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Condition</label>
                            <select name="condition" class="form-select form-select-sm">
                                <option value="">Any</option>
                                <option value="new" <?php echo $condition === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="used" <?php echo $condition === 'used' ? 'selected' : ''; ?>>Used</option>
                                <option value="refurbished" <?php echo $condition === 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort By</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-sm">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <?php if ($search): ?>
                        Results for "<?php echo sanitize($search); ?>"
                    <?php else: ?>
                        All Products
                    <?php endif; ?>
                    <small class="text-muted fs-6">(<?php echo $total; ?> found)</small>
                </h4>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No products found. Try a different search or filter.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4">
                        <div class="card h-100 shadow-sm product-card">
                            <img src="<?php echo SITE_URL . '/' . ($product['image'] ?: 'assets/images/placeholder.png'); ?>"
                                 class="card-img-top" alt="<?php echo sanitize($product['title']); ?>"
                                 style="height:180px;object-fit:cover;">
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-secondary mb-1" style="width:fit-content;font-size:0.7rem;"><?php echo sanitize($product['category_name']); ?></span>
                                <h6 class="card-title mb-1"><?php echo sanitize($product['title']); ?></h6>
                                <p class="text-success fw-bold fs-5 mt-auto mb-1"><?php echo formatPrice($product['price']); ?></p>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> <?php echo sanitize($product['location'] ?: 'SA'); ?>
                                    &middot; <?php echo sanitize($product['condition']); ?></small>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <a href="<?php echo SITE_URL; ?>/products/view.php?id=<?php echo $product['id']; ?>"
                                   class="btn btn-outline-success btn-sm w-100">View</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php echo $p == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $p])); ?>"><?php echo $p; ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
