/**
 * assets/js/main.js - Global Interactions, Micro-Interactions, Toast Engine, Cart Drawer
 */

document.addEventListener('DOMContentLoaded', () => {
    initCartDrawer();
    initAddToCartButtons();
    initHeaderScroll();
    initMobileNav();
});

// 1. Toast Notification System
function showToast(type, title, message) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    
    const iconClass = type === 'success' ? 'fa-check' : (type === 'error' ? 'fa-triangle-exclamation' : 'fa-info');
    const iconStyle = type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info');

    toast.innerHTML = `
        <div class="toast-icon ${iconStyle}">
            <i class="fa-solid ${iconClass}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
    `;

    container.appendChild(toast);

    // Animate in
    setTimeout(() => toast.classList.add('show'), 20);

    // Auto dismiss after 3.5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

// 2. Cart Drawer Controls
function initCartDrawer() {
    const trigger = document.getElementById('cart-drawer-trigger');
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.getElementById('cart-drawer-backdrop');
    const closeBtn = document.getElementById('cart-drawer-close');

    if (!drawer) return;

    function openDrawer() {
        refreshCartDrawer();
        drawer.classList.add('active');
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('active');
        backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (trigger) trigger.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    window.openCartDrawer = openDrawer;
    window.closeCartDrawer = closeDrawer;
}

// 3. Refresh Drawer Content via API
async function refreshCartDrawer() {
    try {
        const res = await fetch('api/cart_action.php?action=get');
        const data = await res.json();
        if (data.success) {
            updateCartUI(data.cart);
        }
    } catch (err) {
        console.error('Failed to load cart summary', err);
    }
}

// 4. Update Header Badge and Drawer Items
function updateCartUI(cart) {
    // Update Badge
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        badge.textContent = cart.count;
        badge.classList.remove('badge-bounce');
        void badge.offsetWidth; // Trigger reflow
        badge.classList.add('badge-bounce');
    });

    // Update Drawer Body
    const drawerItems = document.getElementById('drawer-items-list');
    const drawerSubtotal = document.getElementById('drawer-subtotal-val');
    const drawerEmpty = document.getElementById('drawer-empty-msg');
    const drawerFooter = document.getElementById('drawer-footer-actions');

    if (!drawerItems) return;

    if (!cart.items || cart.items.length === 0) {
        drawerItems.innerHTML = '';
        if (drawerEmpty) drawerEmpty.style.display = 'block';
        if (drawerFooter) drawerFooter.style.display = 'none';
        return;
    }

    if (drawerEmpty) drawerEmpty.style.display = 'none';
    if (drawerFooter) drawerFooter.style.display = 'flex';

    let html = '';
    cart.items.forEach(item => {
        html += `
            <div class="cart-drawer-item">
                <img src="${item.image_url}" alt="${item.name}">
                <div class="cart-drawer-item-info">
                    <div class="cart-drawer-item-title">${item.name}</div>
                    <div class="cart-drawer-item-price">$${item.price.toFixed(2)} &times; ${item.quantity} = $${item.line_total.toFixed(2)}</div>
                </div>
                <button class="cart-drawer-item-remove" onclick="removeCartItem(${item.id})" title="Remove item">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
    });

    drawerItems.innerHTML = html;
    if (drawerSubtotal) {
        drawerSubtotal.textContent = `$${cart.subtotal.toFixed(2)}`;
    }
}

// 5. Remove Item from Drawer
window.removeCartItem = async function(productId) {
    try {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', productId);

        const res = await fetch('api/cart_action.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            updateCartUI(data.cart);
            showToast('info', 'Cart Updated', 'Item removed from your cart.');
            // If on main cart page, reload / sync
            if (typeof window.renderCartPage === 'function') {
                window.renderCartPage();
            }
        }
    } catch (err) {
        console.error('Failed to remove item', err);
    }
};

// 6. Global Add to Cart Handler
function initAddToCartButtons() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-add-cart');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.getAttribute('data-id');
        const qtyInput = document.getElementById(`qty-${productId}`);
        const quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Adding...`;

        try {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            const res = await fetch('api/cart_action.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                // Success animation on button
                btn.classList.add('added');
                btn.innerHTML = `<i class="fa-solid fa-check check-pop"></i> Added!`;
                
                updateCartUI(data.cart);
                showToast('success', 'Added to Cart!', `${data.product.name} (x${quantity}) was added.`);

                // Reset button state after 1.8 seconds
                setTimeout(() => {
                    btn.classList.remove('added');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1800);
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
                showToast('error', 'Notice', data.message || 'Could not add item.');
            }
        } catch (err) {
            console.error('Add to cart error', err);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}

// 7. Navbar scroll styling
function initHeaderScroll() {
    const header = document.querySelector('.site-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 30) {
            header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.7)';
            header.style.borderBottomColor = 'rgba(0, 240, 255, 0.15)';
        } else {
            header.style.boxShadow = 'none';
            header.style.borderBottomColor = 'var(--border-glass)';
        }
    });
}

// 8. Mobile Navigation Toggle
function initMobileNav() {
    const toggle = document.querySelector('.mobile-toggle');
    const menu = document.querySelector('.nav-menu');
    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
            if (menu.style.display === 'flex') {
                menu.style.position = 'absolute';
                menu.style.top = '80px';
                menu.style.left = '0';
                menu.style.width = '100%';
                menu.style.flexDirection = 'column';
                menu.style.background = '#090d16';
                menu.style.padding = '20px';
                menu.style.borderBottom = '1px solid var(--border-glass)';
            }
        });
    }
}
