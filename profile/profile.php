<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo  = getDBConnection();
$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $province  = trim($_POST['province'] ?? '');

    $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, phone=?, address=?, city=?, province=? WHERE id=?");
    $stmt->execute([$firstName, $lastName, $phone, $address, $city, $province, $user['id']]);

    // Profile image
    if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $path = uploadImage($_FILES['profile_image'], 'profiles');
        if ($path) {
            $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?")->execute([$path, $user['id']]);
        }
    }

    // Password change
    $newPass = $_POST['new_password'] ?? '';
    if ($newPass) {
        if (strlen($newPass) < 6) {
            setFlash('error', 'Password must be at least 6 characters.');
            header('Location: profile.php');
            exit;
        }
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $user['id']]);
    }

    setFlash('success', 'Profile updated.');
    header('Location: profile.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white"><h4 class="mb-0"><i class="bi bi-person"></i> My Profile</h4></div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?php echo SITE_URL . '/' . ($user['profile_image'] ?: 'assets/images/default-avatar.png'); ?>"
                             class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                        <h5 class="mt-2"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                        <span class="badge bg-<?php echo $user['role_name'] === 'admin' ? 'danger' : ($user['role_name'] === 'seller' ? 'success' : 'primary'); ?>">
                            <?php echo ucfirst($user['role_name']); ?>
                        </span>
                        <?php if ($user['is_verified']): ?>
                        <span class="badge bg-info"><i class="bi bi-patch-check"></i> Verified</span>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo sanitize($user['first_name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo sanitize($user['last_name']); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo sanitize($user['email']); ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo sanitize($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Province</label>
                                <select name="province" class="form-select">
                                    <option value="">Select</option>
                                    <?php foreach (['Gauteng','Western Cape','KwaZulu-Natal','Eastern Cape','Free State','Limpopo','Mpumalanga','North West','Northern Cape'] as $p): ?>
                                    <option value="<?php echo $p; ?>" <?php echo ($user['province'] ?? '') === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?php echo sanitize($user['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="<?php echo sanitize($user['address'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" name="new_password" class="form-control" minlength="6">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
