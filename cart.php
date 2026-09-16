<?php
// cart.php - Page 4: Attractive Interactive Shopping Cart
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "Your Attractive Shopping Cart";
require_once __DIR__ . '/config/db.php';

// Prepare cart items & totals from session
$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$subtotal = 0.0;
$totalCount = 0;

if (!empty($cart)) {
    foreach ($cart as $id => $qty) {
        $prod = getProductById((int)$id);
        if ($prod) {
            $lineTotal = $prod['price'] * $qty;
            $subtotal += $lineTotal;
            $totalCount += $qty;
            $cartItems[] = [
                'id' => $prod['id'],
                'name' => $prod['name'],
                'price' => (float)$prod['price'],
                'image_url' => $prod['image_url'],
                'category' => $prod['category_name'],
                'stock' => $prod['stock'],
                'quantity' => $qty,
                'line_total' => $lineTotal
            ];
        }
    }
}

$coupon = $_SESSION['coupon'] ?? null;
$discount = ($coupon && $subtotal > 0) ? round($subtotal * $coupon['rate'], 2) : 0.0;
$shippingThreshold = 100.00;
$freeShippingEligible = ($subtotal >= $shippingThreshold || $subtotal == 0);
$shipping = $freeShippingEligible ? 0.0 : 15.0;
$taxable = max(0, $subtotal - $discount);
$tax = round($taxable * 0.08, 2);
$grandTotal = round($taxable + $shipping + $tax, 2);

$shippingProgress = min(100, round(($subtotal / $shippingThreshold) * 100));
$shippingNeeded = max(0, $shippingThreshold - $subtotal);

$extraScript = 'assets/js/cart.js';
require_once __DIR__ . '/includes/header.php';
?>

<main style="padding: 40px 0 80px;">
    <div class="container">
        <!-- Page Title & Stepper -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 2.6rem;">
                    Attractive <span class="gradient-text">Cart</span>
                </h1>
                <p style="color: var(--text-secondary); margin-top: 4px;">
                    Review your high-tech items, apply discounts, and proceed to secure checkout.
                </p>
            </div>

            <!-- Stepper Indicators -->
            <div style="display: flex; align-items: center; gap: 12px; background: var(--bg-surface); padding: 8px 18px; border-radius: var(--radius-full); border: 1px solid var(--border-glass);">
                <span style="color: var(--accent-cyan); font-weight: 700; font-size: 0.88rem;">
                    <i class="fa-solid fa-circle-check"></i> 1. Cart
                </span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                <span style="color: var(--text-muted); font-size: 0.88rem;">2. Shipping</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                <span style="color: var(--text-muted); font-size: 0.88rem;">3. Payment</span>
            </div>
        </div>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart State -->
            <div class="cart-empty-state" style="background: var(--bg-glass-card); border-radius: var(--radius-xl); border: 1px solid var(--border-glass); padding: 80px 20px; box-shadow: var(--shadow-card);">
                <div style="width: 90px; height: 90px; border-radius: 50%; background: rgba(0, 240, 255, 0.08); border: 1px solid rgba(0, 240, 255, 0.25); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 2.5rem; color: var(--accent-cyan);" class="animate-float">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h2 style="font-size: 2rem; margin-bottom: 10px;">Your Cart is Currently Empty</h2>
                <p style="color: var(--text-secondary); max-width: 480px; margin: 0 auto 28px; line-height: 1.6;">
                    You haven't added any electronic gear yet. Discover 20 high-performance workstations, foldable phones, noise-cancelling acoustics, and gaming gear.
                </p>
                <a href="shop.php" class="btn btn-primary btn-lg" id="empty-cart-shop-btn">
                    <i class="fa-solid fa-bag-shopping"></i> Explore 20 Items Now
                </a>
            </div>
        <?php else: ?>
            <!-- Free Shipping Progress Tracker -->
            <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 18px 24px; margin-bottom: 32px; backdrop-filter: blur(14px);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 0.9rem;">
                    <?php if ($freeShippingEligible): ?>
                        <span style="color: var(--accent-emerald); font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Congratulations! You've unlocked FREE Worldwide Express Shipping!
                        </span>
                    <?php else: ?>
                        <span>
                            Add <strong class="text-cyan">$<?= number_format($shippingNeeded, 2) ?></strong> more to qualify for <strong>FREE Express Shipping</strong>!
                        </span>
                    <?php endif; ?>
                    <span style="font-weight: 700; color: var(--text-cyan);"><?= $shippingProgress ?>%</span>
                </div>
                <!-- Progress bar track -->
                <div style="width: 100%; height: 8px; background: rgba(255, 255, 255, 0.08); border-radius: 4px; overflow: hidden;">
                    <div style="width: <?= $shippingProgress ?>%; height: 100%; background: var(--gradient-neon); border-radius: 4px; transition: width 0.6s ease;"></div>
                </div>
            </div>

            <!-- Cart Layout: Items Table (Left) + Order Summary (Right) -->
            <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 36px; align-items: start;">
                <!-- Left: Interactive Items List -->
                <div>
                    <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); overflow: hidden; backdrop-filter: blur(16px); box-shadow: var(--shadow-card);">
                        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="font-size: 1.25rem;">
                                Cart Items (<?= $totalCount ?>)
                            </h3>
                            <button type="button" class="btn btn-danger btn-sm" id="clear-cart-btn">
                                <i class="fa-solid fa-trash-can"></i> Clear Cart
                            </button>
                        </div>

                        <!-- Items List -->
                        <div style="display: flex; flex-direction: column;">
                            <?php foreach ($cartItems as $item): ?>
                                <div id="cart-row-<?= $item['id'] ?>" style="display: flex; align-items: center; gap: 20px; padding: 20px 24px; border-bottom: 1px solid var(--border-glass); transition: all 0.3s ease; flex-wrap: wrap;">
                                    <!-- Image Thumbnail -->
                                    <div style="width: 90px; height: 90px; border-radius: var(--radius-md); overflow: hidden; background: #05080e; border: 1px solid var(--border-glass); flex-shrink: 0;">
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>

                                    <!-- Product Info -->
                                    <div style="flex-grow: 1; min-width: 180px;">
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase;">
                                            <?= htmlspecialchars($item['category']) ?>
                                        </div>
                                        <h4 style="font-size: 1.05rem; margin: 3px 0 6px;">
                                            <a href="product.php?id=<?= $item['id'] ?>" style="color: var(--text-primary);">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </a>
                                        </h4>
                                        <div style="font-size: 0.88rem; color: var(--text-secondary);">
                                            Unit Price: <strong style="color: #fff;">$<?= number_format($item['price'], 2) ?></strong>
                                        </div>
                                    </div>

                                    <!-- Quantity Stepper -->
                                    <div class="qty-stepper">
                                        <button type="button" class="qty-btn cart-qty-minus" data-id="<?= $item['id'] ?>" title="Decrease quantity">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input type="number" id="cart-item-qty-<?= $item['id'] ?>" class="qty-input" value="<?= $item['quantity'] ?>" readonly>
                                        <button type="button" class="qty-btn cart-qty-plus" data-id="<?= $item['id'] ?>" title="Increase quantity">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>

                                    <!-- Line Total -->
                                    <div style="text-align: right; min-width: 90px;">
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">Total</div>
                                        <div id="line-total-<?= $item['id'] ?>" style="font-size: 1.2rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">
                                            $<?= number_format($item['line_total'], 2) ?>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <button type="button" class="btn btn-secondary btn-icon" onclick="removeCartRow(<?= $item['id'] ?>)" title="Remove item" style="color: var(--text-muted);">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Footer / Back to shop -->
                        <div style="padding: 16px 24px; background: rgba(0, 0, 0, 0.2); display: flex; justify-content: space-between; align-items: center;">
                            <a href="shop.php" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
                            </a>
                            <span style="font-size: 0.85rem; color: var(--text-muted);">
                                <i class="fa-solid fa-shield text-cyan"></i> 256-Bit SSL Encrypted Cart
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Attractive Summary & Promo Code -->
                <div>
                    <!-- Promo Code Box -->
                    <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 24px; margin-bottom: 24px; backdrop-filter: blur(14px);">
                        <h4 style="font-size: 1.1rem; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-tags text-cyan"></i> Have a Promo Code?
                        </h4>
                        
                        <form id="coupon-form" style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <input type="text" id="coupon-input" placeholder="e.g. TECH2026" style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 10px 14px; flex-grow: 1; outline: none; text-transform: uppercase; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;" value="<?= $coupon['code'] ?? '' ?>">
                            <button type="submit" class="btn btn-primary btn-sm" id="apply-coupon-btn">
                                Apply
                            </button>
                        </form>

                        <div id="active-coupon-badge" style="<?= $coupon ? 'display: block;' : 'display: none;' ?>">
                            <?php if ($coupon): ?>
                                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--accent-emerald); color: #34d399; padding: 6px 12px; border-radius: var(--radius-sm); font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;">
                                    <span><i class="fa-solid fa-check"></i> Applied: <strong><?= htmlspecialchars($coupon['label']) ?></strong></span>
                                    <button type="button" onclick="removeCoupon()" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 1rem;">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                            💡 Hint: Use <strong class="text-cyan" style="cursor: pointer;" onclick="document.getElementById('coupon-input').value='TECH2026'; document.getElementById('apply-coupon-btn').click();">TECH2026</strong> for instant 15% discount!
                        </div>
                    </div>

                    <!-- Order Summary Box -->
                    <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 28px; backdrop-filter: blur(16px); box-shadow: var(--shadow-card);">
                        <h3 style="font-size: 1.35rem; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-glass);">
                            Order Summary
                        </h3>

                        <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px; font-size: 0.95rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Items Subtotal:</span>
                                <span id="summary-subtotal" style="font-weight: 700;">$<?= number_format($subtotal, 2) ?></span>
                            </div>

                            <div id="summary-discount-row" style="display: <?= $discount > 0 ? 'flex' : 'none' ?>; justify-content: space-between; color: var(--accent-emerald);">
                                <span>Promotional Discount:</span>
                                <span id="summary-discount" style="font-weight: 700;">-$<?= number_format($discount, 2) ?></span>
                            </div>

                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Estimated Shipping:</span>
                                <span id="summary-shipping" style="font-weight: 700; color: <?= $shipping == 0 ? 'var(--accent-emerald)' : 'inherit' ?>;">
                                    <?= $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2) ?>
                                </span>
                            </div>

                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Estimated Tax (8%):</span>
                                <span id="summary-tax" style="font-weight: 700;">$<?= number_format($tax, 2) ?></span>
                            </div>

                            <div style="padding-top: 16px; border-top: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: baseline;">
                                <span style="font-size: 1.15rem; font-weight: 700;">Grand Total:</span>
                                <span id="summary-total" style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">
                                    $<?= number_format($grandTotal, 2) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Checkout Button with Glowing Animation -->
                        <a href="checkout.php" class="btn btn-primary btn-lg btn-full pulse-glow" id="proceed-checkout-btn" style="margin-bottom: 16px;">
                            Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        <div style="display: flex; justify-content: center; gap: 16px; font-size: 0.8rem; color: var(--text-muted);">
                            <span><i class="fa-solid fa-lock text-cyan"></i> Secure 256-Bit SSL</span>
                            <span>&bull;</span>
                            <span><i class="fa-solid fa-rotate-left text-emerald"></i> 30-Day Guarantee</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
