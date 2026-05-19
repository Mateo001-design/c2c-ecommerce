<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $uid = (int) $_POST['user_id'];
        if ($uid != $_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
            setFlash('success', 'User deleted.');
        } else {
            setFlash('error', 'Cannot delete yourself.');
        }
    }
    if (isset($_POST['update_user'])) {
        $uid     = (int) $_POST['user_id'];
        $roleId  = (int) $_POST['role_id'];
        $verified = isset($_POST['is_verified']) ? 1 : 0;
        $pdo->prepare("UPDATE users SET role_id = ?, is_verified = ? WHERE id = ?")->execute([$roleId, $verified, $uid]);
        setFlash('success', 'User updated.');
    }
    if (isset($_POST['create_user'])) {
        $username  = trim($_POST['username']);
        $email     = trim($_POST['email']);
        $password  = $_POST['password'];
        $firstName = trim($_POST['first_name']);
        $lastName  = trim($_POST['last_name']);
        $roleId    = (int) $_POST['role_id'];

        if ($username && $email && $password && $firstName && $lastName) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, role_id, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$username, $email, $hash, $firstName, $lastName, $roleId]);
            setFlash('success', 'User created.');
        }
    }
    header('Location: users.php');
    exit;
}

// Search
$search = trim($_GET['q'] ?? '');
$roleFilter = (int) ($_GET['role'] ?? 0);

$where = [];
$params = [];
if ($search) {
    $where[] = "(u.username LIKE ? OR u.email LIKE ? OR u.first_name LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($roleFilter) {
    $where[] = "u.role_id = ?";
    $params[] = $roleFilter;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$users = $pdo->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id $whereSQL ORDER BY u.created_at DESC");
$users->execute($params);
$allUsers = $users->fetchAll();

$roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Users</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal"><i class="bi bi-person-plus"></i> Create User</button>
</div>

<!-- Filters -->
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Search users..." value="<?php echo sanitize($search); ?>">
    </div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="0">All Roles</option>
            <?php foreach ($roles as $r): ?>
            <option value="<?php echo $r['id']; ?>" <?php echo $roleFilter == $r['id'] ? 'selected' : ''; ?>><?php echo ucfirst($r['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
</form>

<!-- Users Table -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Verified</th><th>Joined</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($allUsers as $u): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><strong><?php echo sanitize($u['username']); ?></strong></td>
            <td><?php echo sanitize($u['email']); ?></td>
            <td><?php echo sanitize($u['first_name'] . ' ' . $u['last_name']); ?></td>
            <td><span class="badge bg-<?php echo $u['role_name'] === 'admin' ? 'danger' : ($u['role_name'] === 'seller' ? 'success' : 'primary'); ?>"><?php echo ucfirst($u['role_name']); ?></span></td>
            <td><?php echo $u['is_verified'] ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?></td>
            <td><small><?php echo date('d M Y', strtotime($u['created_at'])); ?></small></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUser<?php echo $u['id']; ?>"><i class="bi bi-pencil"></i></button>
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                    <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                <?php endif; ?>
            </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editUser<?php echo $u['id']; ?>" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Edit: <?php echo sanitize($u['username']); ?></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role_id" class="form-select">
                                <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>" <?php echo $u['role_id'] == $r['id'] ? 'selected' : ''; ?>><?php echo ucfirst($r['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_verified" id="verified<?php echo $u['id']; ?>" <?php echo $u['is_verified'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="verified<?php echo $u['id']; ?>">Verified Seller</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_user" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div></div>
        </div>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST">
            <div class="modal-header"><h5 class="modal-title">Create User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-2"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
                <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-2"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="row mb-2">
                    <div class="col"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" required></div>
                    <div class="col"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" required></div>
                </div>
                <div class="mb-2"><label class="form-label">Role</label>
                    <select name="role_id" class="form-select">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="create_user" class="btn btn-success">Create</button>
            </div>
        </form>
    </div></div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
