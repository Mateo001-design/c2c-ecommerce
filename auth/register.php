<?php
$pageTitle = 'Register';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';
    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $city       = trim($_POST['city'] ?? '');
    $province   = trim($_POST['province'] ?? '');
    $role       = $_POST['account_type'] ?? 'buyer';

    // Validation
    if (strlen($username) < 3)             $errors[] = 'Username must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6)             $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)            $errors[] = 'Passwords do not match.';
    if (empty($firstName) || empty($lastName)) $errors[] = 'First and last name are required.';

    if (empty($errors)) {
        $pdo = getDBConnection();

        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already registered.';
        } else {
            $roleId = ($role === 'seller') ? 2 : 3;
            $hash   = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, phone, city, province, role_id)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hash, $firstName, $lastName, $phone, $city, $province, $roleId]);

            setFlash('success', 'Registration successful! Please log in.');
            header('Location: ' . SITE_URL . '/auth/login.php');
            exit;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4"><i class="bi bi-person-plus text-success"></i> Create Account</h3>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0"><?php foreach ($errors as $e) echo '<li>' . sanitize($e) . '</li>'; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required
                                       value="<?php echo sanitize($_POST['first_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required
                                       value="<?php echo sanitize($_POST['last_name'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" required
                                       value="<?php echo sanitize($_POST['username'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?php echo sanitize($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control"
                                       value="<?php echo sanitize($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account Type</label>
                                <select name="account_type" class="form-select">
                                    <option value="buyer">Buyer</option>
                                    <option value="seller" <?php echo (($_POST['account_type'] ?? '') === 'seller') ? 'selected' : ''; ?>>Seller</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control"
                                       value="<?php echo sanitize($_POST['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Province</label>
                                <select name="province" class="form-select">
                                    <option value="">Select Province</option>
                                    <?php foreach (['Gauteng','Western Cape','KwaZulu-Natal','Eastern Cape','Free State','Limpopo','Mpumalanga','North West','Northern Cape'] as $p): ?>
                                    <option value="<?php echo $p; ?>" <?php echo (($_POST['province'] ?? '') === $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-success w-100 btn-lg">Create Account</button>
                            </div>
                        </div>
                    </form>
                    <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php" class="text-success">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
