<?php
// includes/footer.php
?>
    <!-- Store Guarantees / Perks Section -->
    <section style="background: rgba(12, 16, 26, 0.7); border-top: 1px solid var(--border-glass); border-bottom: 1px solid var(--border-glass); padding: 40px 0; margin-top: 80px;">
        <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 30px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: rgba(0, 240, 255, 0.1); border: 1px solid rgba(0, 240, 255, 0.3); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--accent-cyan);">
                    <i class="fa-solid fa-plane-up"></i>
                </div>
                <div>
                    <h5 style="font-size: 1rem; margin-bottom: 4px;">Global Priority Shipping</h5>
                    <p style="font-size: 0.82rem; color: var(--text-muted);">Free on orders exceeding $100</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.3); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--accent-violet);">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h5 style="font-size: 1rem; margin-bottom: 4px;">2-Year Warranty</h5>
                    <p style="font-size: 0.82rem; color: var(--text-muted);">Comprehensive hardware protection</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--accent-emerald);">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h5 style="font-size: 1rem; margin-bottom: 4px;">30-Day Money-Back</h5>
                    <p style="font-size: 0.82rem; color: var(--text-muted);">No questions asked refunds</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--accent-amber);">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h5 style="font-size: 1rem; margin-bottom: 4px;">24/7 Tech Concierge</h5>
                    <p style="font-size: 0.82rem; color: var(--text-muted);">Dedicated hardware engineers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="index.php" class="logo-brand">
                        <i class="fa-solid fa-microchip"></i>
                        <span>ELECTRO<span class="gradient-text">CART</span></span>
                    </a>
                    <p>
                        Pioneering cutting-edge consumer computing, titanium mobile technology, spatial acoustics, and high-performance esports hardware since 2026.
                    </p>
                    <div style="display: flex; gap: 12px; margin-top: 18px;">
                        <a href="#" class="btn btn-secondary btn-icon" style="width: 36px; height: 36px; font-size: 0.9rem;"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="btn btn-secondary btn-icon" style="width: 36px; height: 36px; font-size: 0.9rem;"><i class="fa-brands fa-github"></i></a>
                        <a href="#" class="btn btn-secondary btn-icon" style="width: 36px; height: 36px; font-size: 0.9rem;"><i class="fa-brands fa-discord"></i></a>
                        <a href="#" class="btn btn-secondary btn-icon" style="width: 36px; height: 36px; font-size: 0.9rem;"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Categories (5)</h4>
                    <ul class="footer-links">
                        <li><a href="shop.php?category=1">Laptops & Ultrabooks</a></li>
                        <li><a href="shop.php?category=2">Smartphones & Tablets</a></li>
                        <li><a href="shop.php?category=3">Audio & Headphones</a></li>
                        <li><a href="shop.php?category=4">Wearables & Smart IoT</a></li>
                        <li><a href="shop.php?category=5">Gaming & Gear</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="shop.php">All 20 Products</a></li>
                        <li><a href="cart.php">Attractive Cart</a></li>
                        <li><a href="checkout.php">Express Checkout</a></li>
                        <li><a href="shop.php?sort=price_asc">Deals & Offers</a></li>
                        <li><a href="#newsletter">VIP Newsletter</a></li>
                    </ul>
                </div>

                <div class="footer-col" id="newsletter">
                    <h4>VIP Tech Dispatch</h4>
                    <p style="font-size: 0.86rem; color: var(--text-muted); margin-bottom: 12px;">Subscribe for exclusive secret coupon codes, early hardware drops and teardowns.</p>
                    <form onsubmit="event.preventDefault(); showToast('success', 'Subscribed!', 'Welcome to the ElectroCart VIP Club. Check your inbox for your 15% discount!'); this.reset();" class="newsletter-box">
                        <input type="email" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">
                        <i class="fa-solid fa-lock text-cyan"></i> No spam. Instant 15% code on sign-up.
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> <strong>ElectroCart Technologies Inc.</strong> All rights reserved. Powered by PHP & SQLite.
                </div>
                <div class="payment-badges">
                    <i class="fa-brands fa-cc-visa" title="Visa"></i>
                    <i class="fa-brands fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fa-brands fa-cc-amex" title="American Express"></i>
                    <i class="fa-brands fa-cc-apple-pay" title="Apple Pay"></i>
                    <i class="fa-brands fa-cc-paypal" title="PayPal"></i>
                    <i class="fa-brands fa-bitcoin" title="Bitcoin"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <?php if (isset($extraScript)): ?>
        <script src="<?= $extraScript ?>"></script>
    <?php endif; ?>
</body>
</html>
