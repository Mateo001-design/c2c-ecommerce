<?php
$pageTitle = 'Edit Product';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$product = getProductById($id);
if (!$product || ($product['seller_id'] != $_SESSION['user_id'] && getUserRole() !== 'admin')) {
    setFlash('error', 'Product not found or access denied.');
    header('Location: ' . SITE_URL . '/products/browse.php');
    exit;
}

$categories = getCategories();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $categoryId  = (int) ($_POST['category_id'] ?? 0);
    $condition   = $_POST['condition'] ?? 'used';
    $quantity    = max(1, (int) ($_POST['quantity'] ?? 1));
    $location    = trim($_POST['location'] ?? '');
    $status      = $_POST['status'] ?? 'active';

    if (empty($title))       $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if ($price <= 0)         $errors[] = 'Price must be greater than R0.';

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, category_id=?, `condition`=?, quantity=?, location=?, status=? WHERE id=?");
        $stmt->execute([$title, $description, $price, $categoryId, $condition, $quantity, $location, $status, $id]);

        // Handle new images
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $tmp,
                    'size' => $_FILES['images']['size'][$i],
                ];
                $path = uploadImage($file);
                if ($path) {
                    $imgStmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, 0)");
                    $imgStmt->execute([$id, $path]);
                }
            }
        }

        setFlash('success', 'Product updated.');
        header('Location: ' . SITE_URL . '/products/view.php?id=' . $id);
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
$images = getProductImages($id);
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white"><h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Product</h4></div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo '<li>' . sanitize($e) . '</li>'; ?></ul></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Product Title *</label>
                            <input type="text" name="title" class="form-control" required value="<?php echo sanitize($product['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="5" required><?php echo sanitize($product['description']); ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Price (ZAR) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input type="number" name="price" class="form-control" step="0.01" min="1" required value="<?php echo $product['price']; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $product['category_id'] == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo sanitize($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="sold" <?php echo $product['status'] === 'sold' ? 'selected' : ''; ?>>Sold</option>
                                    <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Condition</label>
                                <select name="condition" class="form-select">
                                    <option value="used" <?php echo $product['condition'] === 'used' ? 'selected' : ''; ?>>Used</option>
                                    <option value="new" <?php echo $product['condition'] === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="refurbished" <?php echo $product['condition'] === 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="1" value="<?php echo $product['quantity']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" value="<?php echo sanitize($product['location'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Existing images -->
                        <?php if (!empty($images)): ?>
                        <div class="mt-3">
                            <label class="form-label">Current Images</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($images as $img): ?>
                                <img src="<?php echo SITE_URL . '/' . $img['image_path']; ?>" class="img-thumbnail" style="width:80px;height:80px;object-fit:cover;">
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3 mt-3">
                            <label class="form-label">Add More Images</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 mt-2">Update Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
