<?php
$pageTitle = 'Leave a Review';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$orderId = (int) ($_GET['order_id'] ?? 0);
$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT o.*, u.username AS seller_name FROM orders o JOIN users u ON o.seller_id = u.id WHERE o.id = ? AND o.buyer_id = ? AND o.status = 'delivered'");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found or not eligible for review.');
    header('Location: my_orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = max(1, min(5, (int) $_POST['rating']));
    $comment = trim($_POST['comment'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO reviews (reviewer_id, reviewed_user_id, order_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $order['seller_id'], $orderId, $rating, $comment]);

    setFlash('success', 'Review submitted. Thank you!');
    header('Location: my_orders.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3><i class="bi bi-star text-warning"></i> Review Seller: <?php echo sanitize($order['seller_name']); ?></h3>
                    <p class="text-muted">Order #<?php echo $order['id']; ?></p>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Good</option>
                                <option value="3">3 - Average</option>
                                <option value="2">2 - Poor</option>
                                <option value="1">1 - Terrible</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comment (optional)</label>
                            <textarea name="comment" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
