<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';

$stats = getAdminStats();
$pdo = getDBConnection();

// Recent orders
$recentOrders = $pdo->query("SELECT o.*, b.username AS buyer_name, s.username AS seller_name
    FROM orders o JOIN users b ON o.buyer_id = b.id JOIN users s ON o.seller_id = s.id
    ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

// Recent users
$recentUsers = $pdo->query("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC LIMIT 5")->fetchAll();
?>

<h2 class="mb-4">Dashboard</h2>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h3 class="mb-0"><?php echo $stats['users']; ?></h3><small>Total Users</small></div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h3 class="mb-0"><?php echo $stats['products']; ?></h3><small>Products</small></div>
                    <i class="bi bi-box fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h3 class="mb-0"><?php echo $stats['orders']; ?></h3><small>Orders</small></div>
                    <i class="bi bi-receipt fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div><h3 class="mb-0"><?php echo formatPrice($stats['revenue']); ?></h3><small>Revenue</small></div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Buyer</th><th>Seller</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td><?php echo $o['id']; ?></td>
                        <td><?php echo sanitize($o['buyer_name']); ?></td>
                        <td><?php echo sanitize($o['seller_name']); ?></td>
                        <td><?php echo formatPrice($o['total_amount']); ?></td>
                        <td><span class="badge bg-<?php echo match($o['status']) { 'pending'=>'warning','paid'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger',default=>'secondary' }; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">New Users</h5>
                <a href="users.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>User</th><th>Role</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentUsers as $u): ?>
                    <tr>
                        <td><?php echo sanitize($u['username']); ?></td>
                        <td><span class="badge bg-<?php echo $u['role_name'] === 'admin' ? 'danger' : ($u['role_name'] === 'seller' ? 'success' : 'primary'); ?>"><?php echo ucfirst($u['role_name']); ?></span></td>
                        <td><small><?php echo date('d M', strtotime($u['created_at'])); ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
