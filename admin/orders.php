<?php
$pageTitle = 'Manage Orders';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$_POST['new_status'], (int) $_POST['order_id']]);
    setFlash('success', 'Order status updated.');
    header('Location: orders.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($statusFilter) {
    $where = "WHERE o.status = ?";
    $params = [$statusFilter];
}

$stmt = $pdo->prepare("SELECT o.*, b.username AS buyer_name, s.username AS seller_name
    FROM orders o JOIN users b ON o.buyer_id = b.id JOIN users s ON o.seller_id = s.id
    $where ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<h2 class="mb-3">Manage Orders</h2>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <?php foreach (['pending','paid','shipped','delivered','cancelled'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
</form>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr><th>#</th><th>Buyer</th><th>Seller</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><strong><?php echo $o['id']; ?></strong></td>
            <td><?php echo sanitize($o['buyer_name']); ?></td>
            <td><?php echo sanitize($o['seller_name']); ?></td>
            <td class="fw-bold"><?php echo formatPrice($o['total_amount']); ?></td>
            <td><small><?php echo strtoupper($o['payment_method'] ?? '-'); ?></small></td>
            <td><span class="badge bg-<?php echo match($o['status']) { 'pending'=>'warning','paid'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger',default=>'secondary' }; ?>"><?php echo ucfirst($o['status']); ?></span></td>
            <td><small><?php echo date('d M Y H:i', strtotime($o['created_at'])); ?></small></td>
            <td>
                <form method="POST" class="d-flex gap-1">
                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                    <select name="new_status" class="form-select form-select-sm" style="width:auto;">
                        <?php foreach (['pending','paid','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $o['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
