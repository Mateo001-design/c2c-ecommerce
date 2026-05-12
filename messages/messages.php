<?php
$pageTitle = 'Messages';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiverId = (int) ($_POST['receiver_id'] ?? 0);
    $productId  = (int) ($_POST['product_id'] ?? 0) ?: null;
    $message    = trim($_POST['message'] ?? '');

    if ($receiverId && $message) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $receiverId, $productId, $message]);
    }
    header('Location: messages.php?to=' . $receiverId . ($productId ? '&product=' . $productId : ''));
    exit;
}

// Get conversation list (unique contacts)
$contacts = $pdo->prepare("
    SELECT
        d.contact_id,
        (SELECT username FROM users WHERE id = d.contact_id) AS contact_name,
        (SELECT message FROM messages WHERE
            (sender_id = ? AND receiver_id = d.contact_id)
            OR (receiver_id = ? AND sender_id = d.contact_id)
            ORDER BY created_at DESC LIMIT 1) AS last_message,
        MAX(d.created_at) AS last_time
    FROM (
        SELECT
            CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS contact_id,
            created_at
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
    ) AS d
    GROUP BY d.contact_id
    ORDER BY last_time DESC
");
$contacts->execute([$userId, $userId, $userId, $userId, $userId]);
$contactList = $contacts->fetchAll();

// Active conversation
$toId = (int) ($_GET['to'] ?? 0);
$productId = (int) ($_GET['product'] ?? 0);
$conversation = [];
$contactName = '';

if ($toId) {
    $nameStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $nameStmt->execute([$toId]);
    $contactName = $nameStmt->fetchColumn() ?: 'Unknown';

    $convStmt = $pdo->prepare("
        SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $convStmt->execute([$userId, $toId, $toId, $userId]);
    $conversation = $convStmt->fetchAll();

    // Mark as read
    $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?")->execute([$toId, $userId]);
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <h2><i class="bi bi-chat-dots"></i> Messages</h2>
    <div class="row g-3" style="min-height:500px;">
        <!-- Contact List -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">Conversations</div>
                <div class="list-group list-group-flush" style="max-height:500px;overflow-y:auto;">
                    <?php if (empty($contactList) && !$toId): ?>
                        <p class="text-muted text-center p-3 small">No messages yet.</p>
                    <?php endif; ?>
                    <?php foreach ($contactList as $c): ?>
                    <a href="messages.php?to=<?php echo $c['contact_id']; ?>"
                       class="list-group-item list-group-item-action <?php echo $toId == $c['contact_id'] ? 'active' : ''; ?>">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo sanitize($c['contact_name']); ?></strong>
                            <small><?php echo timeAgo($c['last_time']); ?></small>
                        </div>
                        <small class="text-truncate d-block" style="max-width:200px;"><?php echo sanitize($c['last_message']); ?></small>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Conversation -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100 d-flex flex-column">
                <?php if ($toId): ?>
                <div class="card-header"><strong><?php echo sanitize($contactName); ?></strong></div>
                <div class="card-body flex-grow-1" style="max-height:400px;overflow-y:auto;" id="chatBox">
                    <?php foreach ($conversation as $msg): ?>
                    <div class="mb-2 <?php echo $msg['sender_id'] == $userId ? 'text-end' : ''; ?>">
                        <div class="d-inline-block p-2 rounded <?php echo $msg['sender_id'] == $userId ? 'bg-success text-white' : 'bg-light'; ?>" style="max-width:75%;">
                            <p class="mb-0 small"><?php echo nl2br(sanitize($msg['message'])); ?></p>
                        </div>
                        <br><small class="text-muted"><?php echo timeAgo($msg['created_at']); ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-footer">
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="receiver_id" value="<?php echo $toId; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required autofocus>
                        <button type="submit" name="send_message" class="btn btn-success"><i class="bi bi-send"></i></button>
                    </form>
                </div>
                <?php else: ?>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <p class="text-muted">Select a conversation or message a seller from a product page.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-scroll chat to bottom
const chatBox = document.getElementById('chatBox');
if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
