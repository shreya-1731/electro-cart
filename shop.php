<?php
// shop.php - Page 2: Complete Catalog of 20 items across 5 categories
$pageTitle = "Catalog & Electronics Store (20 Items)";
require_once __DIR__ . '/config/db.php';

$categoryId = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : null;

$categories = getCategories();
$products = getProducts($categoryId, $search, $sort);

// Active category name if filtered
$activeCategoryName = 'All Electronics';
if ($categoryId) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $categoryId) {
            $activeCategoryName = $cat['name'];
            break;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main style="padding: 40px 0 80px;">
    <div class="container">
        <!-- Catalog Header -->
        <div style="margin-bottom: 36px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 8px;">
                <a href="index.php">Home</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
                <span class="text-cyan"><?= htmlspecialchars($activeCategoryName) ?></span>
            </div>
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 16px;">
                <div>
                    <h1 style="font-size: 2.5rem;"><?= htmlspecialchars($activeCategoryName) ?></h1>
                    <p style="color: var(--text-secondary); margin-top: 6px;">
                        Showing <?= count($products) ?> available premium electronic devices
                        <?php if ($search): ?>
                            matching "<strong><?= htmlspecialchars($search) ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Sort & Filter Form Controls -->
                <form action="shop.php" method="GET" style="display: flex; gap: 12px; align-items: center;">
                    <?php if ($categoryId): ?>
                        <input type="hidden" name="category" value="<?= $categoryId ?>">
                    <?php endif; ?>
                    <?php if ($search): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>

                    <label for="sort-select" style="font-size: 0.88rem; color: var(--text-secondary);">Sort By:</label>
                    <select name="sort" id="sort-select" onchange="this.form.submit()" style="background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 8px 16px; color: var(--text-primary); outline: none; cursor: pointer;">
                        <option value="" <?= empty($sort) ? 'selected' : '' ?>>Default Featured</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Customer Rating</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Category Filter Tabs (5 Categories + All) -->
        <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 12px; margin-bottom: 36px; scrollbar-width: none;">
            <a href="shop.php<?= $sort ? '?sort=' . urlencode($sort) : '' ?>" 
               class="btn btn-sm <?= !$categoryId ? 'btn-primary' : 'btn-secondary' ?>" 
               style="white-space: nowrap;">
                <i class="fa-solid fa-border-all"></i> All 20 Devices
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="shop.php?category=<?= $cat['id'] ?><?= $sort ? '&sort=' . urlencode($sort) : '' ?>" 
                   class="btn btn-sm <?= $categoryId == $cat['id'] ? 'btn-primary' : 'btn-secondary' ?>" 
                   style="white-space: nowrap;">
                    <i class="fa-solid <?= $cat['icon'] ?>"></i> <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Product Grid (20 Items) -->
        <?php if (empty($products)): ?>
            <div class="cart-empty-state" style="background: var(--bg-glass-card); border-radius: var(--radius-lg); border: 1px solid var(--border-glass); padding: 70px 20px;">
                <i class="fa-solid fa-box-open" style="font-size: 3.5rem;"></i>
                <h3>No Devices Found</h3>
                <p style="margin-top: 10px; color: var(--text-muted);">Try adjusting your search query or view all 20 devices across our categories.</p>
                <a href="shop.php" class="btn btn-outline btn-md" style="margin-top: 20px;">
                    <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                </a>
            </div>
        <?php else: ?>
            <div class="product-grid" id="catalog-products-grid">
                <?php foreach ($products as $item): ?>
                    <div class="product-card tilt-card" id="catalog-item-<?= $item['id'] ?>">
                        <div class="product-card-img-wrap">
                            <?php if (!empty($item['badge'])): ?>
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
                            <h2 class="product-card-title">
                                <a href="product.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                            </h2>
                            <p class="product-card-desc"><?= htmlspecialchars($item['short_desc']) ?></p>

                            <div class="product-card-rating">
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-solid fa-star<?= $i <= round($item['rating']) ? '' : '-half-stroke' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span><?= $item['rating'] ?> (<?= $item['reviews_count'] ?> reviews)</span>
                            </div>

                            <div class="product-card-footer">
                                <div class="product-price-box">
                                    <span class="product-price">$<?= number_format($item['price'], 2) ?></span>
                                    <?php if ($item['old_price']): ?>
                                        <span class="product-old-price">$<?= number_format($item['old_price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div style="display: flex; gap: 8px;">
                                    <a href="product.php?id=<?= $item['id'] ?>" class="btn btn-secondary btn-sm" title="View Details">
                                        <i class="fa-solid fa-eye"></i> Specs
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
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
