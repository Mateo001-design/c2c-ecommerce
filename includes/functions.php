<?php
/**
 * Helper Functions - iTradeZA C2C Platform
 */

require_once __DIR__ . '/../config/database.php';

// ── Authentication Helpers ───────────────────────────────────

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if (getUserRole() !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo '<h1>403 – Access Denied</h1>';
        exit;
    }
}

function requireAdmin(): void {
    requireRole('admin');
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function getUserRole(): string {
    return $_SESSION['role'] ?? 'buyer';
}

// ── Formatting Helpers ───────────────────────────────────────

function formatPrice(float $amount): string {
    return 'R ' . number_format($amount, 2, '.', ',');
}

function timeAgo(string $datetime): string {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ── Flash Messages ───────────────────────────────────────────

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function displayFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
         . sanitize($flash['message'])
         . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

// ── Image Upload ─────────────────────────────────────────────

function uploadImage(array $file, string $subfolder = 'products'): ?string {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null; // 5 MB max

    $dir = UPLOAD_DIR . $subfolder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_') . '.' . $ext;
    $dest     = $dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return 'uploads/' . $subfolder . '/' . $filename;
    }
    return null;
}

// ── Product Helpers ──────────────────────────────────────────

function getCategories(): array {
    $pdo = getDBConnection();
    return $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}

function getProductById(int $id): ?array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, u.username AS seller_name, u.city AS seller_city, c.name AS category_name
        FROM products p
        JOIN users u ON p.seller_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getProductImages(int $productId): array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

// ── Cart Helpers ─────────────────────────────────────────────

function getCartCount(): int {
    if (!isLoggedIn()) return 0;
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE buyer_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return (int) $stmt->fetchColumn();
}

// ── Stats (Admin) ────────────────────────────────────────────

function getAdminStats(): array {
    $pdo = getDBConnection();
    return [
        'users'    => (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'products' => (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'orders'   => (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'revenue'  => (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'")->fetchColumn(),
    ];
}
