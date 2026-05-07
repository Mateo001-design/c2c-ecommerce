<?php
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo = getDBConnection();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        $pdo->prepare("DELETE FROM cart WHERE id = ? AND buyer_id = ?")->execute([$_POST['cart_id'], $_SESSION['user_id']]);
    }
    if (isset($_POST['update_qty'])) {
        $qty = max(1, (int) $_POST['quantity']);
        $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND buyer_id = ?")->execute([$qty, $_POST['cart_id'], $_SESSION['user_id']]);
    }
    if (isset($_POST['clear_cart'])) {
        $pdo->prepare("DELETE FROM cart WHERE buyer_id = ?")->execute([$_SESSION['user_id']]);
    }
    header('Location: cart.php');
    exit;
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.title, p.price, p.seller_id, p.status, u.username AS seller_name,
           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN users u ON p.seller_id = u.id
    WHERE c.buyer_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <h2><i class="bi bi-cart3"></i> Shopping Cart</h2>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <p class="text-muted mt-2">Your cart is empty.</p>
            <a href="<?php echo SITE_URL; ?>/products/browse.php" class="btn btn-success">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th style="width:120px;">Qty</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?php echo SITE_URL . '/' . ($item['image'] ?: 'assets/images/placeholder.png'); ?>"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                <div>
                                    <a href="<?php echo SITE_URL; ?>/products/view.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none fw-semibold">
                                        <?php echo sanitize($item['title']); ?>
                                    </a>
                                    <br><small class="text-muted">Seller: <?php echo sanitize($item['seller_name']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" class="form-control form-control-sm" min="1"
                                       value="<?php echo $item['quantity']; ?>" style="width:60px;">
                                <button type="submit" name="update_qty" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button>
                            </form>
                        </td>
                        <td class="fw-bold"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <form method="POST">
                    <button type="submit" name="clear_cart" class="btn btn-outline-danger">Clear Cart</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-end">
                        <h4>Total: <span class="text-success"><?php echo formatPrice($total); ?></span></h4>
                        <a href="<?php echo SITE_URL; ?>/cart/checkout.php" class="btn btn-success btn-lg mt-2">
                            <i class="bi bi-credit-card"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
