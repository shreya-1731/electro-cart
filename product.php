<?php
// product.php - Page 3: Product Details & Technical Specifications
require_once __DIR__ . '/config/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 1;
$product = getProductById($id);

if (!$product) {
    header("Location: shop.php");
    exit;
}

$pageTitle = $product['name'] . " - Tech Specs & Details";
$specs = json_decode($product['specs'] ?? '[]', true) ?: [];
$relatedProducts = getRelatedProducts($product['category_id'], $product['id'], 3);

require_once __DIR__ . '/includes/header.php';
?>

<main style="padding: 40px 0 80px;">
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 28px;">
            <a href="index.php">Home</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
            <a href="shop.php">Shop</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
            <a href="shop.php?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a>
            <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
            <span class="text-cyan"><?= htmlspecialchars($product['name']) ?></span>
        </div>

        <!-- Main Product Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; margin-bottom: 70px;">
            <!-- Left: Product Image Showcase -->
            <div style="position: relative;">
                <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); overflow: hidden; padding: 20px; box-shadow: var(--shadow-elevated);">
                    <div style="position: relative; width: 100%; padding-top: 85%; border-radius: var(--radius-lg); overflow: hidden; background: #05080e;">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="main-product-img" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                    </div>
                </div>

                <!-- Guarantee Badges below image -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 16px;">
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-glass); padding: 12px; border-radius: var(--radius-md); text-align: center; font-size: 0.8rem;">
                        <i class="fa-solid fa-truck-fast text-cyan" style="font-size: 1.1rem; margin-bottom: 6px; display: block;"></i>
                        <span>Express Delivery</span>
                    </div>
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-glass); padding: 12px; border-radius: var(--radius-md); text-align: center; font-size: 0.8rem;">
                        <i class="fa-solid fa-shield-halved text-violet" style="font-size: 1.1rem; margin-bottom: 6px; display: block;"></i>
                        <span>2-Year Warranty</span>
                    </div>
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-glass); padding: 12px; border-radius: var(--radius-md); text-align: center; font-size: 0.8rem;">
                        <i class="fa-solid fa-rotate-left text-emerald" style="font-size: 1.1rem; margin-bottom: 6px; display: block;"></i>
                        <span>30-Day Returns</span>
                    </div>
                </div>
            </div>

            <!-- Right: Product Info & Actions -->
            <div>
                <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 12px;">
                    <span style="background: rgba(0, 240, 255, 0.15); color: var(--accent-cyan); padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                    <?php if ($product['badge']): ?>
                        <span class="product-badge" style="position: static; padding: 4px 12px;">
                            <?= htmlspecialchars($product['badge']) ?>
                        </span>
                    <?php endif; ?>
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; color: var(--accent-emerald); font-weight: 600; margin-left: auto;">
                        <span class="pulse-dot"></span> <?= $product['stock'] ?> Units Left In Stock
                    </span>
                </div>

                <h1 style="font-size: 2.4rem; margin-bottom: 14px;"><?= htmlspecialchars($product['name']) ?></h1>

                <!-- Rating -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <div style="color: var(--accent-amber); font-size: 1rem;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-solid fa-star<?= $i <= round($product['rating']) ? '' : '-half-stroke' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span style="font-weight: 700;"><?= $product['rating'] ?> / 5.0</span>
                    <span style="color: var(--text-muted);">&bull; <?= $product['reviews_count'] ?> Verified Customer Ratings</span>
                </div>

                <!-- Price Box -->
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 20px 24px; margin-bottom: 28px; display: flex; align-items: baseline; gap: 16px;">
                    <span style="font-size: 2.5rem; font-weight: 800; font-family: 'Outfit';" class="gradient-text">
                        $<?= number_format($product['price'], 2) ?>
                    </span>
                    <?php if ($product['old_price']): ?>
                        <span style="font-size: 1.3rem; color: var(--text-muted); text-decoration: line-through;">
                            $<?= number_format($product['old_price'], 2) ?>
                        </span>
                        <span style="background: rgba(244, 63, 94, 0.15); color: #fda4af; font-size: 0.85rem; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full);">
                            Save $<?= number_format($product['old_price'] - $product['price'], 2) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div style="color: var(--text-secondary); line-height: 1.7; font-size: 1.02rem; margin-bottom: 32px;">
                    <p style="margin-bottom: 14px;"><strong>Highlights:</strong> <?= htmlspecialchars($product['short_desc']) ?></p>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                </div>

                <!-- Interactive Add to Cart & Buy Now Action Controls -->
                <div style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 32px;">
                    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">QUANTITY</label>
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn" onclick="let q=document.getElementById('qty-<?= $product['id'] ?>'); if(q.value>1)q.value--;">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" id="qty-<?= $product['id'] ?>" class="qty-input" value="1" min="1" max="<?= $product['stock'] ?>">
                                <button type="button" class="qty-btn" onclick="let q=document.getElementById('qty-<?= $product['id'] ?>'); if(q.value<<?= $product['stock'] ?>)q.value++;">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div style="flex-grow: 1; display: flex; gap: 12px; align-items: flex-end;">
                            <button type="button" 
                                    class="btn btn-primary btn-lg btn-add-cart" 
                                    data-id="<?= $product['id'] ?>" 
                                    style="flex-grow: 1;" 
                                    id="detail-add-cart-btn">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>

                            <button type="button" 
                                    class="btn btn-secondary btn-lg" 
                                    onclick="quickCheckout(<?= $product['id'] ?>)" 
                                    style="border-color: var(--accent-cyan); color: var(--accent-cyan);"
                                    id="detail-buy-now-btn">
                                <i class="fa-solid fa-bolt"></i> Buy Now
                            </button>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 14px; font-size: 0.85rem; color: var(--text-muted);">
                    <span><i class="fa-solid fa-fingerprint text-cyan"></i> Authenticity Guaranteed</span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-rotate text-violet"></i> Free Returns in 30 Days</span>
                </div>
            </div>
        </div>

        <!-- Technical Specifications Table -->
        <div style="background: var(--bg-glass-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); padding: 36px; margin-bottom: 60px;">
            <h3 style="font-size: 1.6rem; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-microchip text-cyan"></i> Full Technical Specifications
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                <?php foreach ($specs as $key => $val): ?>
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 16px 20px;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                            <?= htmlspecialchars($key) ?>
                        </div>
                        <div style="font-size: 0.98rem; font-weight: 500; color: var(--text-primary);">
                            <?= htmlspecialchars($val) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Related Products in same category -->
        <?php if (!empty($relatedProducts)): ?>
            <div style="margin-bottom: 60px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
                    <h3 style="font-size: 1.6rem;">Related Devices in <?= htmlspecialchars($product['category_name']) ?></h3>
                    <a href="shop.php?category=<?= $product['category_id'] ?>" class="btn btn-outline btn-sm">
                        View All in Category <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="product-grid">
                    <?php foreach ($relatedProducts as $rel): ?>
                        <div class="product-card tilt-card">
                            <div class="product-card-img-wrap">
                                <a href="product.php?id=<?= $rel['id'] ?>">
                                    <img src="<?= htmlspecialchars($rel['image_url']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="product-card-img" loading="lazy">
                                </a>
                            </div>
                            <div class="product-card-body">
                                <h4 class="product-card-title">
                                    <a href="product.php?id=<?= $rel['id'] ?>"><?= htmlspecialchars($rel['name']) ?></a>
                                </h4>
                                <div class="product-card-footer">
                                    <span class="product-price">$<?= number_format($rel['price'], 2) ?></span>
                                    <button type="button" class="btn btn-primary btn-sm btn-add-cart" data-id="<?= $rel['id'] ?>">
                                        <i class="fa-solid fa-cart-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Buy now direct handler
async function quickCheckout(productId) {
    const qtyInput = document.getElementById('qty-' + productId);
    const quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    try {
        const res = await fetch('api/cart_action.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = 'checkout.php';
        }
    } catch(e) {
        window.location.href = 'cart.php';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
