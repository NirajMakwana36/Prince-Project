<?php
$page_title = 'My Shopping Cart';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'customer/login.php', 'Please login to view your cart', 'info');
}

$user_id = $_SESSION['user_id'];
$cart_items = getCartItems($conn, $user_id);
$cart_total = getCartTotal($conn, $user_id);
$delivery_charge = getDeliveryCharge($conn);
$grand_total = $cart_total + $delivery_charge;
?>

<style>
    .cart-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; margin: 4rem 0; }
    .cart-card { background: white; border-radius: 2rem; padding: 2.5rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    
    .cart-item { display: grid; grid-template-columns: 100px 1fr auto; gap: 2rem; padding: 2rem 0; border-bottom: 1px solid var(--border); align-items: center; transition: var(--transition); }
    .cart-item:last-child { border-bottom: none; }
    .cart-item:hover { transform: translateX(5px); }
    
    .cart-item-img { width: 100px; height: 100px; border-radius: 1.5rem; object-fit: cover; background: var(--bg); }
    .cart-item-info h3 { font-family: 'Outfit', sans-serif; font-size: 1.25rem; margin-bottom: 0.5rem; }
    .cart-item-info p { color: var(--text-muted); font-size: 0.9rem; }

    .qty-box { display: flex; align-items: center; background: var(--bg); padding: 0.5rem; border-radius: 1rem; gap: 1rem; width: fit-content; margin-top: 1rem; }
    .qty-btn { width: 30px; height: 30px; border-radius: 0.75rem; border: none; background: white; color: var(--secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; transition: var(--transition); }
    .qty-btn:hover { background: var(--primary); color: white; }
    .qty-input { width: 30px; border: none; background: transparent; text-align: center; font-weight: 700; font-family: inherit; outline: none; }

    .summary-row { display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-weight: 500; }
    .summary-total { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed var(--border); display: flex; justify-content: space-between; align-items: center; }

    .remove-btn { color: #f87171; border: none; background: #fee2e2; width: 36px; height: 36px; border-radius: 10px; cursor: pointer; transition: var(--transition); font-size: 1rem; }
    .remove-btn:hover { background: #f87171; color: white; transform: rotate(15deg); }

    @media (max-width: 900px) {
        .cart-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container animate__animated animate__fadeIn">
    <div style="margin-top: 4rem;">
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Shopping <span style="color: var(--primary);">Cart</span></h1>
        <p style="color: var(--text-muted);">You have <?php echo count($cart_items); ?> items in your cart.</p>
    </div>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 8rem 0;">
            <div style="font-size: 6rem; color: var(--border); margin-bottom: 2rem;"><i class="fas fa-shopping-basket"></i></div>
            <h2 style="margin-bottom: 1.5rem;">Your cart is empty!</h2>
            <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Looks like you haven't added anything to your cart yet.</p>
            <a href="products.php" class="btn btn-primary btn-lg">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <div class="cart-card">
                <div class="cart-items-list">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                        <img src="../assets/images/<?php echo $item['image']; ?>" class="cart-item-img" onerror="this.src='https://via.placeholder.com/100'">
                        <div class="cart-item-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo formatCurrency(getDiscountedPrice($item['price'], $item['discount'])); ?> per unit</p>
                            
                            <div class="qty-box">
                                <button class="qty-btn" onclick="updateQty(<?php echo $item['id']; ?>, -1)">-</button>
                                <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                                <button class="qty-btn" onclick="updateQty(<?php echo $item['id']; ?>, 1)">+</button>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem;">
                                <?php echo formatCurrency(getDiscountedPrice($item['price'], $item['discount']) * $item['quantity']); ?>
                            </div>
                            <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)" title="Remove Item"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside>
                <div class="cart-card" style="position: sticky; top: 100px;">
                    <h2 style="margin-bottom: 2rem; font-family: 'Outfit', sans-serif;">Order Summary</h2>
                    
                    <div class="summary-row">
                        <span style="color: var(--text-muted);">Subtotal</span>
                        <span id="subtotal"><?php echo formatCurrency($cart_total); ?></span>
                    </div>
                    <div class="summary-row">
                        <span style="color: var(--text-muted);">Delivery Charge</span>
                        <span><?php echo formatCurrency($delivery_charge); ?></span>
                    </div>
                    
                    <div class="summary-total">
                        <div>
                            <span style="display: block; font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">Total Amount</span>
                            <span style="font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; color: var(--secondary);" id="grandTotal">
                                <?php echo formatCurrency($grand_total); ?>
                            </span>
                        </div>
                    </div>

                    <div style="margin-top: 2.5rem; display: flex; flex-direction: column; gap: 1rem;">
                        <a href="checkout.php" class="btn btn-primary btn-lg btn-block" style="justify-content: center;">
                            Checkout Now <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="products.php" class="btn btn-secondary btn-block" style="justify-content: center;">
                            Continue Shopping
                        </a>
                    </div>

                    <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg); border-radius: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                        <i class="fas fa-shield-check" style="color: var(--primary); font-size: 1.5rem;"></i>
                        <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">Your payment and data are secured with 256-bit encryption.</p>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQty(cartId, delta) {
    const input = document.querySelector(`#cart-item-${cartId} .qty-input`);
    let newQty = parseInt(input.value) + delta;
    if (newQty < 1) return;

    fetch('<?php echo BASE_URL; ?>api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&cart_id=${cartId}&quantity=${newQty}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Simple reload to update totals
        }
    });
}

function removeItem(cartId) {
    if (!confirm('Remove this item from your cart?')) return;
    
    fetch('<?php echo BASE_URL; ?>api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&cart_id=${cartId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        }
    });
}
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
