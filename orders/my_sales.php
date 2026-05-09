<?php
$pageTitle = 'My Sales';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo = getDBConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId  = (int) $_POST['order_id'];
    $newStatus = $_POST['new_status'];
    $allowed = ['pending','paid','shipped','delivered','cancelled'];
    if (in_array($newStatus, $allowed)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND seller_id = ?")
            ->execute([$newStatus, $orderId, $_SESSION['user_id']]);
        setFlash('success', 'Order status updated.');
    }
    header('Location: my_sales.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.*, u.username AS buyer_name
    FROM orders o
    JOIN users u ON o.buyer_id = u.id
    WHERE o.seller_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// My products
$prodStmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
$prodStmt->execute([$_SESSION['user_id']]);
$myProducts = $prodStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <h2><i class="bi bi-cash-stack"></i> My Sales &amp; Listings</h2>

    <!-- My Products -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">My Listings</h5>
            <a href="<?php echo SITE_URL; ?>/products/add.php" class="btn btn-success btn-sm"><i class="bi bi-plus"></i> New</a>
        </div>
        <div class="card-body p-0">
            <?php if (empty($myProducts)): ?>
                <p class="text-muted text-center py-3">No products listed yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($myProducts as $p): ?>
                    <tr>
                        <td><a href="<?php echo SITE_URL; ?>/products/view.php?id=<?php echo $p['id']; ?>"><?php echo sanitize($p['title']); ?></a></td>
                        <td><?php echo formatPrice($p['price']); ?></td>
                        <td><?php echo $p['quantity']; ?></td>
                        <td><span class="badge bg-<?php echo $p['status'] === 'active' ? 'success' : ($p['status'] === 'sold' ? 'danger' : 'secondary'); ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td>
                            <a href="<?php echo SITE_URL; ?>/products/edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="<?php echo SITE_URL; ?>/products/delete.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this product?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Orders received -->
    <div class="card shadow-sm">
        <div class="card-header"><h5 class="mb-0">Orders Received</h5></div>
        <div class="card-body p-0">
            <?php if (empty($orders)): ?>
                <p class="text-muted text-center py-3">No orders received yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Buyer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                        <td><?php echo sanitize($order['buyer_name']); ?></td>
                        <td class="fw-bold"><?php echo formatPrice($order['total_amount']); ?></td>
                        <td><span class="badge bg-<?php echo match($order['status']) { 'pending'=>'warning','paid'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger',default=>'secondary' }; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td><small><?php echo date('d M Y', strtotime($order['created_at'])); ?></small></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="new_status" class="form-select form-select-sm" style="width:auto;">
                                    <?php foreach (['pending','paid','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-outline-success">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
