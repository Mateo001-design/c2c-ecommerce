<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$product = getProductById($id);

if (!$product || ($product['seller_id'] != $_SESSION['user_id'] && getUserRole() !== 'admin')) {
    setFlash('error', 'Product not found or access denied.');
    header('Location: ' . SITE_URL . '/products/browse.php');
    exit;
}

$pdo = getDBConnection();
$pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
setFlash('success', 'Product deleted.');

if (getUserRole() === 'admin') {
    header('Location: ' . SITE_URL . '/admin/products.php');
} else {
    header('Location: ' . SITE_URL . '/orders/my_sales.php');
}
exit;
