<?php
// order_success.php - Order Confirmation & Database Receipt
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "Order Confirmed - Thank You!";
require_once __DIR__ . '/config/db.php';

$orderId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = getDb();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

$itemStmt = $pdo->prepare("SELECT oi.*, p.image_url FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemStmt->execute([$orderId]);
$orderItems = $itemStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main style="padding: 60px 0 90px; position: relative;">
    <!-- Confetti Pieces Container -->
    <div id="confetti-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden;"></div>

    <div class="container" style="max-width: 860px;">
        <!-- Success Card -->
        <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass-glow); border-radius: var(--radius-xl); padding: 48px; backdrop-filter: blur(20px); box-shadow: var(--shadow-elevated), var(--shadow-glow-cyan); text-align: center; margin-bottom: 36px; position: relative; overflow: hidden;">
            
            <!-- Animated Checkmark Icon -->
            <div style="width: 86px; height: 86px; border-radius: 50%; background: var(--accent-emerald); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: #06080d; font-size: 2.5rem; box-shadow: 0 0 35px rgba(16, 185, 129, 0.6);" class="check-pop">
                <i class="fa-solid fa-check"></i>
            </div>

            <div style="display: inline-block; background: rgba(16, 185, 129, 0.15); color: #34d399; font-weight: 700; font-size: 0.85rem; padding: 4px 16px; border-radius: var(--radius-full); margin-bottom: 12px; border: 1px solid rgba(16, 185, 129, 0.3);">
                PAYMENT CONFIRMED &bull; ORDER DISPATCHED
            </div>

            <h1 style="font-size: 2.6rem; margin-bottom: 12px;">Thank You, <?= htmlspecialchars($order['customer_name']) ?>!</h1>
            
            <p style="color: var(--text-secondary); max-width: 520px; margin: 0 auto 24px; font-size: 1.05rem;">
                Your order has been recorded in our database. We're preparing your titanium electronics for express courier dispatch.
            </p>

            <div style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 18px 24px; display: inline-flex; align-items: center; gap: 20px; text-align: left;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">ORDER REFERENCE</div>
                    <div style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--accent-cyan); font-size: 1.15rem;">
                        <?= htmlspecialchars($order['order_number']) ?>
                    </div>
                </div>
                <div style="height: 30px; width: 1px; background: var(--border-glass);"></div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">DATE PLACED</div>
                    <div style="font-size: 0.95rem; font-weight: 600;">
                        <?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt & Order Breakdown Container -->
        <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 36px; backdrop-filter: blur(16px); box-shadow: var(--shadow-card);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-glass);">
                <h3 style="font-size: 1.35rem;">
                    <i class="fa-solid fa-receipt text-cyan"></i> Official Order Receipt
                </h3>
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" id="print-invoice-btn">
                    <i class="fa-solid fa-print"></i> Print Invoice
                </button>
            </div>

            <!-- Customer & Delivery Summary -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; background: var(--bg-surface); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-glass); font-size: 0.9rem;">
                <div>
                    <div style="font-weight: 700; color: var(--accent-cyan); margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Delivery Details</div>
                    <div style="color: #fff; font-weight: 600;"><?= htmlspecialchars($order['customer_name']) ?></div>
                    <div style="color: var(--text-secondary);"><?= htmlspecialchars($order['address']) ?></div>
                    <div style="color: var(--text-secondary);"><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['zip']) ?></div>
                    <div style="color: var(--text-muted); margin-top: 4px;"><?= htmlspecialchars($order['customer_email']) ?></div>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--accent-cyan); margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Payment & Shipping</div>
                    <div><span style="color: var(--text-secondary);">Method:</span> <strong><?= strtoupper(htmlspecialchars($order['payment_method'])) ?></strong></div>
                    <div><span style="color: var(--text-secondary);">Fulfillment:</span> <strong style="color: var(--accent-emerald);">Worldwide Express Courier</strong></div>
                    <div><span style="color: var(--text-secondary);">Status:</span> <strong style="color: var(--accent-cyan);">Order Confirmed in Database</strong></div>
                </div>
            </div>

            <!-- Items Purchased Table -->
            <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
                <?php foreach ($orderItems as $item): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: var(--bg-surface); border-radius: var(--radius-md); border: 1px solid var(--border-glass);">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="width: 50px; height: 50px; border-radius: var(--radius-sm); object-fit: cover;">
                            <?php endif; ?>
                            <div>
                                <div style="font-weight: 600; font-size: 0.98rem;"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div style="font-size: 0.82rem; color: var(--text-muted);">Quantity: <?= $item['quantity'] ?> &times; $<?= number_format($item['price'], 2) ?></div>
                            </div>
                        </div>
                        <div style="font-weight: 700; font-size: 1.05rem;">
                            $<?= number_format($item['total'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Financial Breakdown -->
            <div style="border-top: 1px solid var(--border-glass); padding-top: 16px; display: flex; flex-direction: column; gap: 8px; font-size: 0.92rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Subtotal:</span>
                    <span>$<?= number_format($order['subtotal'], 2) ?></span>
                </div>
                <?php if ($order['discount'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; color: var(--accent-emerald);">
                        <span>Coupon Savings:</span>
                        <span>-$<?= number_format($order['discount'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Shipping:</span>
                    <span><?= $order['shipping'] == 0 ? '<strong style="color: var(--accent-emerald)">FREE</strong>' : '$' . number_format($order['shipping'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Tax:</span>
                    <span>$<?= number_format($order['tax'], 2) ?></span>
                </div>
                <div style="padding-top: 14px; margin-top: 6px; border-top: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 1.15rem; font-weight: 700;">Total Paid:</span>
                    <span style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">
                        $<?= number_format($order['total'], 2) ?>
                    </span>
                </div>
            </div>

            <!-- Return Home / Continue Shopping -->
            <div style="display: flex; gap: 14px; margin-top: 32px;">
                <a href="shop.php" class="btn btn-primary btn-lg btn-full" id="continue-shopping-btn">
                    <i class="fa-solid fa-bag-shopping"></i> Continue Shopping
                </a>
                <a href="index.php" class="btn btn-secondary btn-lg" style="white-space: nowrap;">
                    <i class="fa-solid fa-house"></i> Home
                </a>
            </div>
        </div>
    </div>
</main>

<script>
// Confetti Animation Generation
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('confetti-container');
    const colors = ['#00f0ff', '#7000ff', '#10b981', '#f59e0b', '#ec4899'];

    for (let i = 0; i < 40; i++) {
        const piece = document.createElement('div');
        piece.className = 'confetti-piece';
        piece.style.left = Math.random() * 100 + '%';
        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDelay = (Math.random() * 1.5) + 's';
        piece.style.animationDuration = (2.5 + Math.random() * 2) + 's';
        piece.style.width = (8 + Math.random() * 8) + 'px';
        piece.style.height = (8 + Math.random() * 8) + 'px';
        container.appendChild(piece);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
