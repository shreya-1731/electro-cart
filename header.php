<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Calculate active cart count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartCount += (int)$qty;
    }
}

// Current page identifier for active nav link
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ElectroCart' : 'ElectroCart - Next-Gen Electronics & Tech Store' ?></title>
    <meta name="description" content="Discover futuristic laptops, flagship smartphones, audiophile headphones, smart wearables and pro gaming gear with fast delivery.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>

    <!-- Top Announcement Bar -->
    <div style="background: linear-gradient(90deg, #090d16, #161b2e, #090d16); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 7px 0; font-size: 0.82rem; text-align: center; color: var(--text-secondary);">
        <div class="container" style="display: flex; justify-content: center; align-items: center; gap: 14px;">
            <span><i class="fa-solid fa-bolt text-cyan"></i> <strong style="color: #fff;">CYBER SPECIAL:</strong> Use code <span style="background: rgba(0,240,255,0.15); color: var(--accent-cyan); padding: 2px 8px; border-radius: 4px; font-weight: 700; border: 1px dashed rgba(0,240,255,0.4);">TECH2026</span> for 15% OFF!</span>
            <span style="opacity: 0.4;">|</span>
            <span><i class="fa-solid fa-truck-fast text-violet"></i> Free Worldwide Delivery on Orders Over $100</span>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="site-header">
        <div class="container nav-wrapper">
            <!-- Brand Logo -->
            <a href="index.php" class="logo-brand" id="brand-logo">
                <i class="fa-solid fa-microchip animate-float"></i>
                <span>ELECTRO<span class="gradient-text">CART</span></span>
            </a>

            <!-- Navigation Links -->
            <ul class="nav-menu" id="primary-nav">
                <li><a href="index.php" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
                <li><a href="shop.php" class="nav-link <?= $currentPage === 'shop' && !isset($_GET['category']) ? 'active' : '' ?>">All Products (20)</a></li>
                <li><a href="shop.php?category=1" class="nav-link <?= isset($_GET['category']) && $_GET['category'] == '1' ? 'active' : '' ?>">Laptops</a></li>
                <li><a href="shop.php?category=2" class="nav-link <?= isset($_GET['category']) && $_GET['category'] == '2' ? 'active' : '' ?>">Smartphones</a></li>
                <li><a href="shop.php?category=3" class="nav-link <?= isset($_GET['category']) && $_GET['category'] == '3' ? 'active' : '' ?>">Audio</a></li>
                <li><a href="shop.php?category=4" class="nav-link <?= isset($_GET['category']) && $_GET['category'] == '4' ? 'active' : '' ?>">Wearables</a></li>
                <li><a href="shop.php?category=5" class="nav-link <?= isset($_GET['category']) && $_GET['category'] == '5' ? 'active' : '' ?>">Gaming</a></li>
            </ul>

            <!-- Search and Cart Actions -->
            <div class="nav-actions">
                <form action="shop.php" method="GET" class="header-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Search devices, specs..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </form>

                <!-- Trigger Cart Drawer -->
                <button type="button" class="cart-trigger" id="cart-drawer-trigger" title="Open Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="cart-badge" id="header-cart-badge"><?= $cartCount ?></span>
                </button>

                <!-- Mobile Menu Button -->
                <button type="button" class="mobile-toggle" aria-label="Toggle Navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Slide-out Cart Drawer -->
    <div class="cart-drawer-backdrop" id="cart-drawer-backdrop"></div>
    <div class="cart-drawer" id="cart-drawer">
        <div class="cart-drawer-header">
            <h3><i class="fa-solid fa-bag-shopping text-cyan"></i> Your Cart</h3>
            <button type="button" class="cart-drawer-close" id="cart-drawer-close">&times;</button>
        </div>
        <div class="cart-drawer-body">
            <div id="drawer-items-list">
                <!-- Dynamically loaded items via AJAX -->
            </div>
            <div id="drawer-empty-msg" class="cart-empty-state" style="display: none;">
                <i class="fa-solid fa-cart-arrow-down"></i>
                <h4>Your cart is empty</h4>
                <p style="font-size: 0.88rem; margin-top: 8px;">Explore our 20 futuristic electronics and find your next upgrade!</p>
                <a href="shop.php" class="btn btn-outline btn-sm" style="margin-top: 18px;" onclick="closeCartDrawer()">Start Shopping</a>
            </div>
        </div>
        <div class="cart-drawer-footer" id="drawer-footer-actions">
            <div class="cart-drawer-subtotal">
                <span style="color: var(--text-secondary); font-weight: 500;">Subtotal:</span>
                <span id="drawer-subtotal-val" class="gradient-text" style="font-size: 1.3rem;">$0.00</span>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="cart.php" class="btn btn-secondary btn-full" onclick="closeCartDrawer()">
                    <i class="fa-solid fa-cart-flatbed"></i> View Cart
                </a>
                <a href="checkout.php" class="btn btn-primary btn-full">
                    Checkout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
