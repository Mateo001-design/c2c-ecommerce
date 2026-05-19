<?php
$pageTitle = 'Manage Roles (RBAC)';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_role'])) {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        if ($name) {
            $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)")->execute([strtolower($name), $desc]);
            setFlash('success', 'Role created.');
        }
    }
    if (isset($_POST['update_role'])) {
        $id   = (int) $_POST['role_id'];
        $desc = trim($_POST['description']);
        $pdo->prepare("UPDATE roles SET description = ? WHERE id = ?")->execute([$desc, $id]);
        setFlash('success', 'Role updated.');
    }
    if (isset($_POST['delete_role'])) {
        $id = (int) $_POST['role_id'];
        // Don't delete if users exist with this role
        $count = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
        $count->execute([$id]);
        if ($count->fetchColumn() > 0) {
            setFlash('error', 'Cannot delete role: users are assigned to it.');
        } else {
            $pdo->prepare("DELETE FROM roles WHERE id = ?")->execute([$id]);
            setFlash('success', 'Role deleted.');
        }
    }
    header('Location: roles.php');
    exit;
}

$roles = $pdo->query("SELECT r.*, (SELECT COUNT(*) FROM users WHERE role_id = r.id) AS user_count FROM roles r ORDER BY r.id")->fetchAll();
?>

<h2 class="mb-3">Role-Based Access Control (RBAC)</h2>
<p class="text-muted">Manage user roles and permissions. Each user is assigned one role that determines their access level.</p>

<div class="row g-4">
    <!-- Roles Table -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark"><tr><th>ID</th><th>Role Name</th><th>Description</th><th>Users</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($roles as $r): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><span class="badge bg-<?php echo match($r['name']) { 'admin'=>'danger','seller'=>'success','buyer'=>'primary',default=>'secondary' }; ?> fs-6"><?php echo ucfirst($r['name']); ?></span></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="role_id" value="<?php echo $r['id']; ?>">
                                <input type="text" name="description" class="form-control form-control-sm" value="<?php echo sanitize($r['description'] ?? ''); ?>">
                                <button type="submit" name="update_role" class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i></button>
                            </form>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo $r['user_count']; ?></span></td>
                        <td>
                            <?php if ($r['user_count'] == 0): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                <input type="hidden" name="role_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" name="delete_role" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Role -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white"><h5 class="mb-0">Create New Role</h5></div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. moderator">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="What can this role do?">
                    </div>
                    <button type="submit" name="create_role" class="btn btn-success w-100">Create Role</button>
                </form>
            </div>
        </div>

        <!-- RBAC Info -->
        <div class="card mt-3">
            <div class="card-body">
                <h6>Access Levels:</h6>
                <ul class="small mb-0">
                    <li><strong class="text-danger">Admin:</strong> Full CRUD on all users, products, orders, roles.</li>
                    <li><strong class="text-success">Seller:</strong> Create/edit own listings, manage orders, message buyers.</li>
                    <li><strong class="text-primary">Buyer:</strong> Browse, purchase, review sellers, message sellers.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
