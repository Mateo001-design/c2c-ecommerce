<?php
$pageTitle = 'Manage Products';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_product'])) {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([(int) $_POST['product_id']]);
        setFlash('success', 'Product deleted.');
    }
    if (isset($_POST['update_status'])) {
        $pdo->prepare("UPDATE products SET status = ? WHERE id = ?")->execute([$_POST['new_status'], (int) $_POST['product_id']]);
        setFlash('success', 'Status updated.');
    }
    header('Location: products.php');
    exit;
}

$search = trim($_GET['q'] ?? '');
$where = '';
$params = [];
if ($search) {
    $where = "WHERE p.title LIKE ? OR u.username LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$stmt = $pdo->prepare("SELECT p.*, u.username AS seller_name, c.name AS category_name
    FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id
    $where ORDER BY p.created_at DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<h2 class="mb-3">Manage Products</h2>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-5">
        <input type="text" name="q" class="form-control" placeholder="Search products or sellers..." value="<?php echo sanitize($search); ?>">
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
</form>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr><th>ID</th><th>Title</th><th>Seller</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?php echo $p['id']; ?></td>
            <td><a href="<?php echo SITE_URL; ?>/products/view.php?id=<?php echo $p['id']; ?>"><?php echo sanitize($p['title']); ?></a></td>
            <td><?php echo sanitize($p['seller_name']); ?></td>
            <td><small><?php echo sanitize($p['category_name']); ?></small></td>
            <td><?php echo formatPrice($p['price']); ?></td>
            <td><?php echo $p['quantity']; ?></td>
            <td>
                <form method="POST" class="d-flex gap-1">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <select name="new_status" class="form-select form-select-sm" style="width:auto;">
                        <?php foreach (['active','sold','inactive'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $p['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i></button>
                </form>
            </td>
            <td>
                <a href="<?php echo SITE_URL; ?>/products/edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
