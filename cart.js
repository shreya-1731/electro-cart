/**
 * assets/js/cart.js - Dedicated Cart Page Dynamic Handlers
 */

document.addEventListener('DOMContentLoaded', () => {
    initCartPage();
});

function initCartPage() {
    initQuantityButtons();
    initCouponForm();
    initClearCartBtn();
}

// 1. Live Quantity Stepper Updates
function initQuantityButtons() {
    document.addEventListener('click', async (e) => {
        const plusBtn = e.target.closest('.cart-qty-plus');
        const minusBtn = e.target.closest('.cart-qty-minus');

        if (!plusBtn && !minusBtn) return;

        const btn = plusBtn || minusBtn;
        const productId = btn.getAttribute('data-id');
        const input = document.getElementById(`cart-item-qty-${productId}`);
        if (!input) return;

        let currentVal = parseInt(input.value, 10) || 1;
        if (plusBtn) {
            currentVal += 1;
        } else if (minusBtn) {
            currentVal -= 1;
        }

        if (currentVal < 1) {
            if (confirm('Do you want to remove this item from your cart?')) {
                removeCartRow(productId);
            }
            return;
        }

        input.value = currentVal;
        await updateCartItemQty(productId, currentVal);
    });
}

// 2. Send Quantity Update to API
async function updateCartItemQty(productId, quantity) {
    try {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        const res = await fetch('api/cart_action.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            updateCartPageUI(data.cart);
            if (typeof updateCartUI === 'function') {
                updateCartUI(data.cart);
            }
        }
    } catch (err) {
        console.error('Update qty error', err);
    }
}

// 3. Remove Cart Row with Animation
async function removeCartRow(productId) {
    const row = document.getElementById(`cart-row-${productId}`);
    if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
    }

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
            setTimeout(() => {
                if (row) row.remove();
                updateCartPageUI(data.cart);
                if (typeof updateCartUI === 'function') {
                    updateCartUI(data.cart);
                }
                showToast('info', 'Item Removed', 'Product was removed from your cart.');
            }, 300);
        }
    } catch (err) {
        console.error('Remove row error', err);
    }
}

// 4. Update Cart Page Elements
function updateCartPageUI(cart) {
    // If cart is empty
    if (!cart.items || cart.items.length === 0) {
        location.reload();
        return;
    }

    // Update item line totals
    cart.items.forEach(item => {
        const lineTotalEl = document.getElementById(`line-total-${item.id}`);
        if (lineTotalEl) {
            lineTotalEl.textContent = `$${item.line_total.toFixed(2)}`;
        }
    });

    // Update Totals
    const subtotalEl = document.getElementById('summary-subtotal');
    const discountEl = document.getElementById('summary-discount');
    const discountRow = document.getElementById('summary-discount-row');
    const shippingEl = document.getElementById('summary-shipping');
    const taxEl = document.getElementById('summary-tax');
    const totalEl = document.getElementById('summary-total');

    if (subtotalEl) subtotalEl.textContent = `$${cart.subtotal.toFixed(2)}`;
    if (shippingEl) shippingEl.textContent = cart.shipping === 0 ? 'FREE' : `$${cart.shipping.toFixed(2)}`;
    if (taxEl) taxEl.textContent = `$${cart.tax.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `$${cart.total.toFixed(2)}`;

    if (discountRow && discountEl) {
        if (cart.discount > 0) {
            discountRow.style.display = 'flex';
            discountEl.textContent = `-$${cart.discount.toFixed(2)}`;
        } else {
            discountRow.style.display = 'none';
        }
    }
}

// 5. Coupon Form Submission
function initCouponForm() {
    const couponForm = document.getElementById('coupon-form');
    if (!couponForm) return;

    couponForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('coupon-input');
        const code = input ? input.value.trim() : '';

        if (!code) return;

        const submitBtn = couponForm.querySelector('button');
        const origText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        submitBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', 'apply_coupon');
            formData.append('coupon_code', code);

            const res = await fetch('api/cart_action.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            submitBtn.innerHTML = origText;
            submitBtn.disabled = false;

            if (data.success) {
                showToast('success', 'Coupon Applied!', data.message);
                updateCartPageUI(data.cart);
                displayCouponBadge(data.cart.coupon);
            } else {
                showToast('error', 'Coupon Error', data.message);
            }
        } catch (err) {
            submitBtn.innerHTML = origText;
            submitBtn.disabled = false;
            console.error('Coupon error', err);
        }
    });
}

function displayCouponBadge(coupon) {
    const box = document.getElementById('active-coupon-badge');
    if (!box) return;

    if (coupon) {
        box.innerHTML = `
            <div class="coupon-pill">
                <i class="fa-solid fa-tag"></i>
                <span>${coupon.label}</span>
                <button type="button" onclick="removeCoupon()" class="remove-coupon-btn">&times;</button>
            </div>
        `;
        box.style.display = 'block';
    } else {
        box.innerHTML = '';
        box.style.display = 'none';
    }
}

window.removeCoupon = async function() {
    try {
        const formData = new FormData();
        formData.append('action', 'remove_coupon');

        const res = await fetch('api/cart_action.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showToast('info', 'Coupon Removed', 'Standard pricing restored.');
            updateCartPageUI(data.cart);
            displayCouponBadge(null);
        }
    } catch (err) {
        console.error('Remove coupon error', err);
    }
};

// 6. Clear Cart
function initClearCartBtn() {
    const clearBtn = document.getElementById('clear-cart-btn');
    if (!clearBtn) return;

    clearBtn.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to empty your shopping cart?')) return;

        try {
            const formData = new FormData();
            formData.append('action', 'clear');

            const res = await fetch('api/cart_action.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                showToast('info', 'Cart Cleared', 'All items have been removed.');
                setTimeout(() => location.reload(), 300);
            }
        } catch (err) {
            console.error('Clear cart error', err);
        }
    });
}
