<?php
$pageTitle = 'My Orders';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT o.*, u.username AS seller_name
    FROM orders o
    JOIN users u ON o.seller_id = u.id
    WHERE o.buyer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <h2><i class="bi bi-bag"></i> My Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="bi bi-bag-x fs-1 text-muted"></i>
            <p class="text-muted">You haven't placed any orders yet.</p>
            <a href="<?php echo SITE_URL; ?>/products/browse.php" class="btn btn-success">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr><th>Order #</th><th>Seller</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order):
                    $statusBadge = match($order['status']) {
                        'pending'   => 'warning',
                        'paid'      => 'info',
                        'shipped'   => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default     => 'secondary',
                    };
                    // Get items
                    $itemStmt = $pdo->prepare("SELECT oi.*, p.title FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                    $itemStmt->execute([$order['id']]);
                    $items = $itemStmt->fetchAll();
                ?>
                    <tr>
                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                        <td><?php echo sanitize($order['seller_name']); ?></td>
                        <td class="fw-bold"><?php echo formatPrice($order['total_amount']); ?></td>
                        <td><small><?php echo strtoupper($order['payment_method'] ?? 'N/A'); ?></small></td>
                        <td><span class="badge bg-<?php echo $statusBadge; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td><small><?php echo date('d M Y', strtotime($order['created_at'])); ?></small></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#items<?php echo $order['id']; ?>">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="items<?php echo $order['id']; ?>">
                        <td colspan="7">
                            <ul class="list-unstyled mb-0 small">
                                <?php foreach ($items as $item): ?>
                                <li><?php echo sanitize($item['title']); ?> &times;<?php echo $item['quantity']; ?>
                                    – <?php echo formatPrice($item['price_at_purchase']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ($order['status'] === 'delivered'): ?>
                            <a href="<?php echo SITE_URL; ?>/orders/review.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-warning mt-1">Leave Review</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
