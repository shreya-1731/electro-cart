<?php
// api/cart_action.php - Cart API for AJAX operations
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // [product_id => quantity]
}
if (!isset($_SESSION['coupon'])) {
    $_SESSION['coupon'] = null;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));

        $product = getProductById($productId);
        if ($product) {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $qty;
            } else {
                $_SESSION['cart'][$productId] = $qty;
            }
            $response = [
                'success' => true,
                'message' => "Added {$product['name']} to cart!",
                'product' => $product,
                'cart' => getCartSummary()
            ];
        } else {
            $response['message'] = 'Product not found.';
        }
        break;

    case 'update':
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);

        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
            $response = [
                'success' => true,
                'message' => 'Item removed from cart',
                'cart' => getCartSummary()
            ];
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = $qty;
                $response = [
                    'success' => true,
                    'message' => 'Cart updated',
                    'cart' => getCartSummary()
                ];
            } else {
                $response['message'] = 'Item not in cart.';
            }
        }
        break;

    case 'remove':
        $productId = (int)($_POST['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $response = [
                'success' => true,
                'message' => 'Item removed from cart',
                'cart' => getCartSummary()
            ];
        } else {
            $response['message'] = 'Item not found in cart.';
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        $_SESSION['coupon'] = null;
        $response = [
            'success' => true,
            'message' => 'Cart cleared',
            'cart' => getCartSummary()
        ];
        break;

    case 'apply_coupon':
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
        if ($code === 'TECH2026') {
            $_SESSION['coupon'] = [
                'code' => 'TECH2026',
                'rate' => 0.15,
                'label' => '15% Off (TECH2026)'
            ];
            $response = [
                'success' => true,
                'message' => 'Promo code TECH2026 applied (15% OFF)!',
                'cart' => getCartSummary()
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Invalid coupon code. Try: TECH2026'
            ];
        }
        break;

    case 'remove_coupon':
        $_SESSION['coupon'] = null;
        $response = [
            'success' => true,
            'message' => 'Coupon removed',
            'cart' => getCartSummary()
        ];
        break;

    case 'get':
    default:
        $response = [
            'success' => true,
            'cart' => getCartSummary()
        ];
        break;
}

echo json_encode($response);
exit;

/**
 * Calculates current cart details, items, subtotals, tax, discounts
 */
function getCartSummary(): array {
    $cart = $_SESSION['cart'] ?? [];
    $items = [];
    $subtotal = 0.0;
    $totalCount = 0;

    if (!empty($cart)) {
        foreach ($cart as $id => $qty) {
            $product = getProductById((int)$id);
            if ($product) {
                $lineTotal = $product['price'] * $qty;
                $subtotal += $lineTotal;
                $totalCount += $qty;
                $items[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => (float)$product['price'],
                    'image_url' => $product['image_url'],
                    'category' => $product['category_name'],
                    'quantity' => $qty,
                    'line_total' => $lineTotal
                ];
            }
        }
    }

    $discount = 0.0;
    $coupon = $_SESSION['coupon'] ?? null;
    if ($coupon && $subtotal > 0) {
        $discount = round($subtotal * $coupon['rate'], 2);
    }

    $shipping = ($subtotal >= 100 || $subtotal == 0) ? 0.0 : 15.0;
    $taxable = max(0, $subtotal - $discount);
    $tax = round($taxable * 0.08, 2);
    $total = round($taxable + $shipping + $tax, 2);

    return [
        'items' => $items,
        'count' => $totalCount,
        'subtotal' => round($subtotal, 2),
        'discount' => $discount,
        'shipping' => $shipping,
        'tax' => $tax,
        'total' => $total,
        'coupon' => $coupon
    ];
}
