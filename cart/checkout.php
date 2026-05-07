<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo = getDBConnection();

// Get cart items grouped by seller
$stmt = $pdo->prepare("
    SELECT c.*, p.title, p.price, p.seller_id, p.quantity AS stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.buyer_id = ? AND p.status = 'active'
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    setFlash('error', 'Your cart is empty.');
    header('Location: ' . SITE_URL . '/cart/cart.php');
    exit;
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod   = $_POST['payment_method'] ?? 'eft';
    $shippingAddress = trim($_POST['shipping_address'] ?? '');

    if (empty($shippingAddress)) {
        setFlash('error', 'Please enter a shipping address.');
        header('Location: checkout.php');
        exit;
    }

    // Group items by seller and create orders
    $sellerGroups = [];
    foreach ($cartItems as $item) {
        $sellerGroups[$item['seller_id']][] = $item;
    }

    $pdo->beginTransaction();
    try {
        foreach ($sellerGroups as $sellerId => $items) {
            $orderTotal = 0;
            foreach ($items as $item) $orderTotal += $item['price'] * $item['quantity'];

            $orderStmt = $pdo->prepare("INSERT INTO orders (buyer_id, seller_id, total_amount, status, payment_method, shipping_address)
                                        VALUES (?, ?, ?, 'pending', ?, ?)");
            $orderStmt->execute([$_SESSION['user_id'], $sellerId, $orderTotal, $paymentMethod, $shippingAddress]);
            $orderId = (int) $pdo->lastInsertId();

            foreach ($items as $item) {
                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);

                // Decrease stock
                $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")->execute([$item['quantity'], $item['product_id']]);
            }
        }

        // Clear cart
        $pdo->prepare("DELETE FROM cart WHERE buyer_id = ?")->execute([$_SESSION['user_id']]);
        $pdo->commit();

        setFlash('success', 'Order placed successfully! You will be contacted by the seller(s).');
        header('Location: ' . SITE_URL . '/orders/my_orders.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlash('error', 'Checkout failed. Please try again.');
        header('Location: checkout.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <h2><i class="bi bi-credit-card"></i> Checkout</h2>
    <div class="row g-4">
        <!-- Order Summary -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Order Summary</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?php echo sanitize($item['title']); ?> <small class="text-muted">&times;<?php echo $item['quantity']; ?></small></td>
                                <td class="text-end fw-bold"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="fw-bold fs-5">Total</td>
                                <td class="text-end fw-bold fs-5 text-success"><?php echo formatPrice($total); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment & Shipping -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Shipping &amp; Payment</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Shipping Address *</label>
                            <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Full delivery address..."><?php
                                $user = getCurrentUser();
                                echo sanitize(($user['address'] ?? '') . ', ' . ($user['city'] ?? '') . ', ' . ($user['province'] ?? ''));
                            ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="eft">EFT / Bank Transfer</option>
                                <option value="cash">Cash on Delivery</option>
                                <option value="ewallet">e-Wallet (Capitec Pay / FNB)</option>
                                <option value="card">Credit/Debit Card</option>
                            </select>
                            <small class="text-muted">Payment details will be shared after order confirmation.</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-lock"></i> Place Order – <?php echo formatPrice($total); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
