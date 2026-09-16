<?php
// index.php - Page 1: Home Showcase & Featured Products
$pageTitle = "Futuristic Electronics & Gadgets";
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

$categories = getCategories();
$featuredProducts = getFeaturedProducts(8);
?>

<main>
    <!-- Hero Section -->
    <section style="position: relative; padding: 60px 0 90px; overflow: hidden;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 48px; align-items: center;">
                <!-- Left Hero Content -->
                <div class="fade-in-up">
                    <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(0, 240, 255, 0.1); border: 1px solid rgba(0, 240, 255, 0.25); padding: 6px 16px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 600; color: var(--accent-cyan); margin-bottom: 20px;">
                        <span class="pulse-dot"></span> 2026 FLAGSHIP DROP &bull; 20 CURATED DEVICES
                    </div>
                    
                    <h1 style="font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; line-height: 1.1; margin-bottom: 20px;">
                        Next-Gen Gear For The <span class="gradient-text">Modern Pioneer</span>.
                    </h1>

                    <p style="font-size: 1.15rem; color: var(--text-secondary); margin-bottom: 32px; max-width: 540px; line-height: 1.6;">
                        Explore our catalog of 20 revolutionary devices across 5 breakthrough categories. Engineered with titanium durability, OLED perfection, and spatial fidelity.
                    </p>

                    <!-- Interactive Hero Buttons -->
                    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
                        <a href="shop.php" class="btn btn-primary btn-lg" id="hero-shop-btn">
                            <i class="fa-solid fa-layer-group"></i> Explore All 20 Items
                        </a>
                        <button type="button" onclick="openCartDrawer()" class="btn btn-secondary btn-lg" id="hero-cart-btn">
                            <i class="fa-solid fa-bag-shopping text-cyan"></i> View Cart
                        </button>
                    </div>

                    <!-- Metrics / Trust Numbers -->
                    <div style="display: flex; gap: 36px; margin-top: 48px; padding-top: 28px; border-top: 1px solid var(--border-glass);">
                        <div>
                            <div style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">20</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">Handcrafted Items</div>
                        </div>
                        <div>
                            <div style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">5</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">Specialized Categories</div>
                        </div>
                        <div>
                            <div style="font-size: 1.8rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">100%</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">Zero-Lag Guarantee</div>
                        </div>
                    </div>
                </div>

                <!-- Right Hero Image with Floating Glass Badges -->
                <div style="position: relative;" class="animate-float">
                    <div style="position: relative; border-radius: var(--radius-xl); overflow: hidden; border: 1px solid var(--border-glass); box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.9), var(--shadow-glow-cyan);">
                        <img src="assets/images/hero-banner.jpg" alt="Futuristic Tech Showcase" style="width: 100%; object-fit: cover; aspect-ratio: 16/10;">
                        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 60%, rgba(6, 8, 13, 0.8) 100%);"></div>
                    </div>

                    <!-- Floating Badge 1 -->
                    <div style="position: absolute; bottom: -20px; left: -20px; background: rgba(12, 17, 28, 0.9); backdrop-filter: blur(14px); border: 1px solid var(--border-glass-glow); padding: 14px 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-card); display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--gradient-neon); display: flex; align-items: center; justify-content: center; color: #06080d; font-size: 1.2rem;">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem;">Tech Innovation '26</div>
                            <div style="font-size: 0.78rem; color: var(--accent-cyan);">Apex Category Winner</div>
                        </div>
                    </div>

                    <!-- Floating Badge 2 -->
                    <div style="position: absolute; top: -15px; right: -15px; background: rgba(12, 17, 28, 0.9); backdrop-filter: blur(14px); border: 1px solid rgba(139, 92, 246, 0.4); padding: 12px 18px; border-radius: var(--radius-md); box-shadow: var(--shadow-card); display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-bolt text-violet" style="font-size: 1.1rem;"></i>
                        <span style="font-weight: 600; font-size: 0.85rem;">M3 Max &bull; RTX 4080 Ready</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5 Categories Grid -->
    <section style="padding: 60px 0;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;">
                        Explore Ecosystem
                    </div>
                    <h2 style="font-size: 2.2rem;">5 Core Tech Categories</h2>
                </div>
                <a href="shop.php" class="btn btn-outline btn-sm">
                    Browse All 20 <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <?php foreach ($categories as $cat): ?>
                    <a href="shop.php?category=<?= $cat['id'] ?>" class="tilt-card" style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 26px 20px; text-align: center; backdrop-filter: blur(12px); display: flex; flex-direction: column; align-items: center; text-decoration: none; position: relative; overflow: hidden;">
                        <div style="width: 64px; height: 64px; border-radius: var(--radius-md); background: rgba(0, 240, 255, 0.08); border: 1px solid rgba(0, 240, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: var(--accent-cyan); margin-bottom: 16px; transition: transform var(--transition-base);">
                            <i class="fa-solid <?= $cat['icon'] ?>"></i>
                        </div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 6px;"><?= htmlspecialchars($cat['name']) ?></h3>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 14px; line-height: 1.4;">
                            <?= htmlspecialchars($cat['description']) ?>
                        </p>
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--accent-cyan); margin-top: auto; display: flex; align-items: center; gap: 6px;">
                            4 Products <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Trending & Featured Products Showcase -->
    <section style="padding: 70px 0;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;">
                        Top Selections
                    </div>
                    <h2 style="font-size: 2.2rem;">Featured Inventions</h2>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="shop.php?sort=rating" class="btn btn-secondary btn-sm">Highest Rated</a>
                    <a href="shop.php" class="btn btn-primary btn-sm">View All 20 Devices</a>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="product-grid">
                <?php foreach ($featuredProducts as $item): ?>
                    <div class="product-card tilt-card" id="product-card-<?= $item['id'] ?>">
                        <div class="product-card-img-wrap">
                            <?php if ($item['badge']): ?>
                                <span class="product-badge <?= strtolower($item['badge']) === 'flagship' ? 'flagship' : '' ?>">
                                    <?= htmlspecialchars($item['badge']) ?>
                                </span>
                            <?php endif; ?>
                            <a href="product.php?id=<?= $item['id'] ?>">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-card-img" loading="lazy">
                            </a>
                        </div>

                        <div class="product-card-body">
                            <div class="product-card-category"><?= htmlspecialchars($item['category_name']) ?></div>
                            <h3 class="product-card-title">
                                <a href="product.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                            </h3>
                            <p class="product-card-desc"><?= htmlspecialchars($item['short_desc']) ?></p>

                            <div class="product-card-rating">
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-solid fa-star<?= $i <= round($item['rating']) ? '' : '-half-stroke' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span>(<?= $item['reviews_count'] ?> reviews)</span>
                            </div>

                            <div class="product-card-footer">
                                <div class="product-price-box">
                                    <span class="product-price">$<?= number_format($item['price'], 2) ?></span>
                                    <?php if ($item['old_price']): ?>
                                        <span class="product-old-price">$<?= number_format($item['old_price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div style="display: flex; gap: 8px;">
                                    <a href="product.php?id=<?= $item['id'] ?>" class="btn btn-secondary btn-sm" title="View Specs">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-primary btn-sm btn-add-cart" 
                                            data-id="<?= $item['id'] ?>" 
                                            title="Add to Cart">
                                        <i class="fa-solid fa-cart-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Cyber Deals Callout Banner -->
    <section style="padding: 40px 0;">
        <div class="container">
            <div style="background: linear-gradient(135deg, rgba(14, 21, 37, 0.9), rgba(28, 16, 50, 0.9)); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: var(--radius-xl); padding: 48px; display: grid; grid-template-columns: 1.4fr 1fr; gap: 36px; align-items: center; box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.8), var(--shadow-glow-cyan); position: relative; overflow: hidden;">
                <div style="position: absolute; right: -80px; top: -80px; width: 280px; height: 280px; border-radius: 50%; background: radial-gradient(circle, rgba(0, 240, 255, 0.2), transparent 70%); pointer-events: none;"></div>
                
                <div>
                    <span style="background: rgba(0, 240, 255, 0.15); color: var(--accent-cyan); padding: 4px 14px; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                        Exclusive Discount Code
                    </span>
                    <h2 style="font-size: 2.3rem; margin: 14px 0 10px;">Upgrade Your Setup With 15% OFF</h2>
                    <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 24px; max-width: 500px;">
                        Apply promotional coupon code <strong>TECH2026</strong> in your cart to instantly save 15% across all 20 premium laptops, phones, audio gear, and gaming hardware.
                    </p>
                    <div style="display: flex; gap: 14px; align-items: center;">
                        <button type="button" class="btn btn-outline btn-md" onclick="navigator.clipboard.writeText('TECH2026'); showToast('success', 'Coupon Copied!', 'TECH2026 copied to clipboard. Apply it on checkout!');">
                            <i class="fa-regular fa-copy"></i> Copy Code: <strong>TECH2026</strong>
                        </button>
                        <a href="shop.php" class="btn btn-primary btn-md">
                            Shop 20 Items <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div style="text-align: center; background: rgba(8, 12, 20, 0.6); padding: 32px; border-radius: var(--radius-lg); border: 1px solid var(--border-glass); backdrop-filter: blur(12px);">
                    <div style="font-size: 3rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">15% OFF</div>
                    <div style="font-size: 1rem; font-weight: 600; margin-top: 4px;">AUTOMATIC CART SAVINGS</div>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 8px;">Valid across all 5 electronics categories. Free priority courier over $100.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
