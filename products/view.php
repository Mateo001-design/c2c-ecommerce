<?php
require_once __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) { header('Location: browse.php'); exit; }

$product = getProductById($id);
if (!$product) { header('Location: browse.php'); exit; }

$pageTitle = $product['title'];
$images = getProductImages($id);
$pdo = getDBConnection();

// Seller reviews
$reviewStmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewed_user_id = ? ORDER BY r.created_at DESC LIMIT 5");
$reviewStmt->execute([$product['seller_id']]);
$reviews = $reviewStmt->fetchAll();

$avgStmt = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE reviewed_user_id = ?");
$avgStmt->execute([$product['seller_id']]);
$avgRating = round((float) $avgStmt->fetchColumn(), 1);

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    requireLogin();
    $buyerId = $_SESSION['user_id'];
    if ($buyerId == $product['seller_id']) {
        setFlash('error', 'You cannot buy your own product.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (buyer_id, product_id, quantity) VALUES (?, ?, 1)
                               ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->execute([$buyerId, $id]);
        setFlash('success', 'Added to cart!');
    }
    header('Location: view.php?id=' . $id);
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/">Home</a></li>
            <li class="breadcrumb-item"><a href="browse.php">Products</a></li>
            <li class="breadcrumb-item active"><?php echo sanitize($product['title']); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Images -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <?php if (!empty($images)): ?>
                <img src="<?php echo SITE_URL . '/' . $images[0]['image_path']; ?>" class="card-img-top" id="mainImage"
                     alt="<?php echo sanitize($product['title']); ?>" style="max-height:400px;object-fit:contain;background:#f8f9fa;">
                <?php if (count($images) > 1): ?>
                <div class="d-flex gap-2 p-2 flex-wrap">
                    <?php foreach ($images as $img): ?>
                    <img src="<?php echo SITE_URL . '/' . $img['image_path']; ?>"
                         class="img-thumbnail product-thumb" style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                         onclick="document.getElementById('mainImage').src=this.src">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.png" class="card-img-top" style="max-height:400px;object-fit:contain;background:#f8f9fa;">
                <?php endif; ?>
            </div>
        </div>

        <!-- Details -->
        <div class="col-md-6">
            <span class="badge bg-secondary mb-2"><?php echo sanitize($product['category_name']); ?></span>
            <span class="badge bg-<?php echo $product['condition'] === 'new' ? 'primary' : ($product['condition'] === 'refurbished' ? 'warning' : 'info'); ?>">
                <?php echo ucfirst($product['condition']); ?>
            </span>
            <h2 class="mt-2"><?php echo sanitize($product['title']); ?></h2>
            <h3 class="text-success fw-bold"><?php echo formatPrice($product['price']); ?></h3>
            <p class="text-muted"><i class="bi bi-geo-alt"></i> <?php echo sanitize($product['location'] ?: 'South Africa'); ?>
                &middot; Listed <?php echo timeAgo($product['created_at']); ?></p>

            <hr>
            <p><?php echo nl2br(sanitize($product['description'])); ?></p>
            <p><strong>Quantity available:</strong> <?php echo $product['quantity']; ?></p>

            <?php if ($product['status'] === 'active'): ?>
            <form method="POST" class="d-flex gap-2 mt-3">
                <button type="submit" name="add_to_cart" class="btn btn-success btn-lg flex-grow-1">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
                <a href="<?php echo SITE_URL; ?>/messages/messages.php?to=<?php echo $product['seller_id']; ?>&product=<?php echo $product['id']; ?>"
                   class="btn btn-outline-success btn-lg">
                    <i class="bi bi-chat-dots"></i> Message Seller
                </a>
            </form>
            <?php else: ?>
            <div class="alert alert-warning">This item is no longer available.</div>
            <?php endif; ?>

            <!-- Seller Info -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6><i class="bi bi-person-circle"></i> Seller: <?php echo sanitize($product['seller_name']); ?></h6>
                    <p class="mb-1 small text-muted"><i class="bi bi-geo-alt"></i> <?php echo sanitize($product['seller_city'] ?: 'South Africa'); ?></p>
                    <?php if ($avgRating): ?>
                    <p class="mb-0">
                        <?php for ($i = 1; $i <= 5; $i++) echo '<i class="bi bi-star' . ($i <= round($avgRating) ? '-fill text-warning' : '') . '"></i>'; ?>
                        <small class="text-muted">(<?php echo $avgRating; ?>/5)</small>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <?php if (!empty($reviews)): ?>
    <div class="mt-5">
        <h4>Seller Reviews</h4>
        <?php foreach ($reviews as $review): ?>
        <div class="card mb-2">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <strong><?php echo sanitize($review['username']); ?></strong>
                    <span>
                        <?php for ($i = 1; $i <= 5; $i++) echo '<i class="bi bi-star' . ($i <= $review['rating'] ? '-fill text-warning' : '') . '"></i>'; ?>
                    </span>
                </div>
                <p class="mb-0 small"><?php echo sanitize($review['comment'] ?: 'No comment.'); ?></p>
                <small class="text-muted"><?php echo timeAgo($review['created_at']); ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
