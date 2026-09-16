<?php
// checkout.php - Page 5: Checkout & Payment with Database Order Creation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "Express Checkout & Payment";
require_once __DIR__ . '/config/db.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: shop.php");
    exit;
}

$cartItems = [];
$subtotal = 0.0;

foreach ($cart as $id => $qty) {
    $prod = getProductById((int)$id);
    if ($prod) {
        $lineTotal = $prod['price'] * $qty;
        $subtotal += $lineTotal;
        $cartItems[] = [
            'id' => $prod['id'],
            'name' => $prod['name'],
            'price' => (float)$prod['price'],
            'image_url' => $prod['image_url'],
            'quantity' => $qty,
            'line_total' => $lineTotal
        ];
    }
}

$coupon = $_SESSION['coupon'] ?? null;
$discount = ($coupon && $subtotal > 0) ? round($subtotal * $coupon['rate'], 2) : 0.0;
$shipping = ($subtotal >= 100 || $subtotal == 0) ? 0.0 : 15.0;
$taxable = max(0, $subtotal - $discount);
$tax = round($taxable * 0.08, 2);
$grandTotal = round($taxable + $shipping + $tax, 2);

$error = null;

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'credit_card');

    if (empty($customerName) || empty($customerEmail) || empty($address)) {
        $error = "Please fill in all required shipping fields.";
    } else {
        $pdo = getDb();
        $pdo->beginTransaction();

        try {
            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6)) . '-' . date('y');

            $orderStmt = $pdo->prepare("INSERT INTO orders 
                (order_number, customer_name, customer_email, phone, address, city, zip, payment_method, subtotal, discount, shipping, tax, total, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed')");

            $orderStmt->execute([
                $orderNumber,
                $customerName,
                $customerEmail,
                $phone,
                $address,
                $city,
                $zip,
                $paymentMethod,
                $subtotal,
                $discount,
                $shipping,
                $tax,
                $grandTotal
            ]);

            $orderId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO order_items 
                (order_id, product_id, product_name, price, quantity, total) 
                VALUES (?, ?, ?, ?, ?, ?)");

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['line_total']
                ]);
            }

            $pdo->commit();

            // Clear session cart
            $_SESSION['cart'] = [];
            $_SESSION['coupon'] = null;

            // Redirect to Confirmation Page
            header("Location: order_success.php?id=" . $orderId);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to process order: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main style="padding: 40px 0 80px;">
    <div class="container">
        <!-- Progress Stepper -->
        <div style="display: flex; justify-content: center; margin-bottom: 40px;">
            <div style="display: flex; align-items: center; gap: 16px; background: var(--bg-surface); border: 1px solid var(--border-glass); padding: 10px 24px; border-radius: var(--radius-full);">
                <a href="cart.php" style="color: var(--accent-cyan); font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check"></i> Cart
                </a>
                <i class="fa-solid fa-chevron-right" style="color: var(--text-muted); font-size: 0.75rem;"></i>
                <span style="color: var(--accent-cyan); font-weight: 700; font-size: 0.9rem;">
                    <i class="fa-solid fa-credit-card"></i> 2. Shipping & Payment
                </span>
                <i class="fa-solid fa-chevron-right" style="color: var(--text-muted); font-size: 0.75rem;"></i>
                <span style="color: var(--text-muted); font-size: 0.9rem;">
                    3. Confirmation
                </span>
            </div>
        </div>

        <?php if ($error): ?>
            <div style="background: rgba(244, 63, 94, 0.15); border: 1px solid var(--accent-rose); color: #fda4af; padding: 14px 20px; border-radius: var(--radius-md); margin-bottom: 24px;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" id="checkout-form">
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 36px; align-items: start;">
                <!-- Left: Shipping & Payment Details -->
                <div>
                    <!-- Section 1: Customer Details -->
                    <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 32px; margin-bottom: 28px; backdrop-filter: blur(14px);">
                        <h3 style="font-size: 1.35rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-truck-ramp-box text-cyan"></i> 1. Shipping Information
                        </h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Full Name *</label>
                                <input type="text" name="customer_name" required placeholder="Alex Mercer" value="Alex Mercer" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Email Address *</label>
                                <input type="email" name="customer_email" required placeholder="alex@quantum.dev" value="alex@quantum.dev" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none;">
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Phone Number</label>
                            <input type="tel" name="phone" placeholder="+1 (555) 019-2834" value="+1 (555) 019-2834" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none;">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Street Address *</label>
                            <input type="text" name="address" required placeholder="742 Evergreen Cyber Terrace, Suite 404" value="742 Evergreen Cyber Terrace, Suite 404" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">City</label>
                                <input type="text" name="city" placeholder="San Francisco" value="San Francisco" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Postal / Zip Code</label>
                                <input type="text" name="zip" placeholder="94107" value="94107" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Payment Method -->
                    <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 32px; backdrop-filter: blur(14px);">
                        <h3 style="font-size: 1.35rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-credit-card text-cyan"></i> 2. Select Payment Method
                        </h3>

                        <!-- Payment Option Selectors -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 24px;">
                            <label style="background: var(--bg-surface); border: 2px solid var(--accent-cyan); border-radius: var(--radius-md); padding: 14px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <input type="radio" name="payment_method" value="credit_card" checked style="accent-color: var(--accent-cyan);">
                                <i class="fa-solid fa-credit-card text-cyan" style="font-size: 1.4rem;"></i>
                                <span style="font-size: 0.85rem; font-weight: 600;">Credit / Debit</span>
                            </label>

                            <label style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 14px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <input type="radio" name="payment_method" value="apple_pay" style="accent-color: var(--accent-cyan);">
                                <i class="fa-brands fa-apple" style="font-size: 1.4rem;"></i>
                                <span style="font-size: 0.85rem; font-weight: 600;">Apple Pay</span>
                            </label>

                            <label style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 14px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <input type="radio" name="payment_method" value="crypto" style="accent-color: var(--accent-cyan);">
                                <i class="fa-brands fa-bitcoin text-amber" style="font-size: 1.4rem; color: var(--accent-amber);"></i>
                                <span style="font-size: 0.85rem; font-weight: 600;">Crypto / BTC</span>
                            </label>

                            <label style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 14px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <input type="radio" name="payment_method" value="cod" style="accent-color: var(--accent-cyan);">
                                <i class="fa-solid fa-hand-holding-dollar text-emerald" style="font-size: 1.4rem; color: var(--accent-emerald);"></i>
                                <span style="font-size: 0.85rem; font-weight: 600;">Cash On Delivery</span>
                            </label>
                        </div>

                        <!-- Interactive Animated Credit Card Preview -->
                        <div style="background: linear-gradient(135deg, #161b2e 0%, #0d121f 50%, #1e1136 100%); border: 1px solid rgba(0, 240, 255, 0.4); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.8), var(--shadow-glow-cyan); position: relative; overflow: hidden;" class="pulse-glow">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
                                <i class="fa-solid fa-microchip text-cyan" style="font-size: 1.8rem;"></i>
                                <i class="fa-brands fa-cc-visa" style="font-size: 2.2rem; color: #fff;"></i>
                            </div>
                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 1.3rem; letter-spacing: 0.15em; margin-bottom: 20px; color: #f8fafc;" id="preview-card-num">
                                4242 &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 9021
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; text-transform: uppercase; color: var(--text-secondary);">
                                <div>
                                    <div style="font-size: 0.65rem; color: var(--text-muted);">CARDHOLDER</div>
                                    <div id="preview-card-name" style="color: #fff; font-weight: 600;">Alex Mercer</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.65rem; color: var(--text-muted);">EXPIRES</div>
                                    <div id="preview-card-exp" style="color: #fff; font-weight: 600;">08 / 28</div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Inputs -->
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Card Number</label>
                                <input type="text" placeholder="4242 4242 4242 9021" value="4242 4242 4242 9021" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none; font-family: 'JetBrains Mono', monospace;">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div>
                                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Expiration (MM/YY)</label>
                                    <input type="text" placeholder="08/28" value="08/28" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none; font-family: 'JetBrains Mono', monospace;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">CVC / CVV</label>
                                    <input type="password" placeholder="888" value="888" style="width: 100%; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 12px 16px; outline: none; font-family: 'JetBrains Mono', monospace;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Breakdown & Confirm Button -->
                <div>
                    <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 28px; backdrop-filter: blur(16px); box-shadow: var(--shadow-card); position: sticky; top: 100px;">
                        <h3 style="font-size: 1.35rem; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--border-glass);">
                            Order Review (<?= count($cartItems) ?> Devices)
                        </h3>

                        <!-- Items Snapshot -->
                        <div style="display: flex; flex-direction: column; gap: 14px; max-height: 240px; overflow-y: auto; margin-bottom: 20px; padding-right: 6px;">
                            <?php foreach ($cartItems as $item): ?>
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: 50px; border-radius: var(--radius-sm); object-fit: cover;">
                                    <div style="flex-grow: 1; min-width: 0;">
                                        <div style="font-size: 0.88rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($item['name']) ?></div>
                                        <div style="font-size: 0.78rem; color: var(--text-muted);">Qty: <?= $item['quantity'] ?> &times; $<?= number_format($item['price'], 2) ?></div>
                                    </div>
                                    <div style="font-weight: 700; font-size: 0.92rem;">$<?= number_format($item['line_total'], 2) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Totals Breakdown -->
                        <div style="display: flex; flex-direction: column; gap: 10px; padding: 16px 0; border-top: 1px solid var(--border-glass); border-bottom: 1px solid var(--border-glass); font-size: 0.92rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Subtotal:</span>
                                <span>$<?= number_format($subtotal, 2) ?></span>
                            </div>

                            <?php if ($discount > 0): ?>
                                <div style="display: flex; justify-content: space-between; color: var(--accent-emerald);">
                                    <span>Discount (TECH2026):</span>
                                    <span>-$<?= number_format($discount, 2) ?></span>
                                </div>
                            <?php endif; ?>

                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Shipping:</span>
                                <span style="color: <?= $shipping == 0 ? 'var(--accent-emerald)' : 'inherit' ?>;">
                                    <?= $shipping == 0 ? 'FREE Express' : '$' . number_format($shipping, 2) ?>
                                </span>
                            </div>

                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Estimated Tax:</span>
                                <span>$<?= number_format($tax, 2) ?></span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin: 18px 0 24px;">
                            <span style="font-size: 1.1rem; font-weight: 700;">Total Due:</span>
                            <span style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">
                                $<?= number_format($grandTotal, 2) ?>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-full pulse-glow" id="place-order-btn">
                            <i class="fa-solid fa-lock"></i> Authorize & Place Order
                        </button>

                        <div style="font-size: 0.76rem; color: var(--text-muted); text-align: center; margin-top: 14px; line-height: 1.4;">
                            By placing this order you authorize ElectroCart to charge your designated payment method. Database order record generated instantly.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
