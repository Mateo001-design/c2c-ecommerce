<?php
$pageTitle = 'List a Product';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$userRole = getUserRole();
if ($userRole !== 'seller' && $userRole !== 'admin') {
    setFlash('error', 'Only sellers can list products. Update your account type in your profile.');
    header('Location: ' . SITE_URL . '/profile/profile.php');
    exit;
}

$errors = [];
$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $categoryId  = (int) ($_POST['category_id'] ?? 0);
    $condition   = $_POST['condition'] ?? 'used';
    $quantity    = max(1, (int) ($_POST['quantity'] ?? 1));
    $location    = trim($_POST['location'] ?? '');

    if (empty($title))       $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if ($price <= 0)         $errors[] = 'Price must be greater than R0.';
    if (!$categoryId)        $errors[] = 'Select a category.';

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, title, description, price, `condition`, quantity, location)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $categoryId, $title, $description, $price, $condition, $quantity, $location]);
        $productId = (int) $pdo->lastInsertId();

        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $first = true;
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = [
                    'name'     => $_FILES['images']['name'][$i],
                    'type'     => $_FILES['images']['type'][$i],
                    'tmp_name' => $tmp,
                    'size'     => $_FILES['images']['size'][$i],
                ];
                $path = uploadImage($file);
                if ($path) {
                    $imgStmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)");
                    $imgStmt->execute([$productId, $path, $first ? 1 : 0]);
                    $first = false;
                }
            }
        }

        setFlash('success', 'Product listed successfully!');
        header('Location: ' . SITE_URL . '/products/view.php?id=' . $productId);
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white"><h4 class="mb-0"><i class="bi bi-plus-circle"></i> List a Product for Sale</h4></div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo '<li>' . sanitize($e) . '</li>'; ?></ul></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Product Title *</label>
                            <input type="text" name="title" class="form-control" required maxlength="150"
                                   value="<?php echo sanitize($_POST['title'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="5" required><?php echo sanitize($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Price (ZAR) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input type="number" name="price" class="form-control" step="0.01" min="1" required
                                           value="<?php echo sanitize($_POST['price'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category *</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select</option>
                                    <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo (($_POST['category_id'] ?? '') == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo sanitize($c['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Condition</label>
                                <select name="condition" class="form-select">
                                    <option value="used">Used</option>
                                    <option value="new" <?php echo (($_POST['condition'] ?? '') === 'new') ? 'selected' : ''; ?>>New</option>
                                    <option value="refurbished" <?php echo (($_POST['condition'] ?? '') === 'refurbished') ? 'selected' : ''; ?>>Refurbished</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="1" value="<?php echo sanitize($_POST['quantity'] ?? '1'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Soweto, Gauteng"
                                       value="<?php echo sanitize($_POST['location'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Product Images (up to 5)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">First image will be the main photo. Max 5MB each.</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 mt-2">List Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
