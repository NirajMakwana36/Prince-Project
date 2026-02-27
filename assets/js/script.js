// CoGroCart Core Logic
const api = {
    cart: APP_CONFIG.baseUrl + 'api/cart.php',
    products: APP_CONFIG.baseUrl + 'api/products.php'
};

// Update cart count badge
async function updateCartCount() {
    if (!APP_CONFIG.isLoggedIn) return;
    try {
        const res = await fetch(api.cart + '?action=count');
        const data = await res.json();
        const badge = document.getElementById('cartBadge');
        if (badge) {
            badge.innerText = data.count || 0;
            badge.style.display = data.count > 0 ? 'flex' : 'none';
        }
    } catch (e) { console.error('Cart count error:', e); }
}

// Global Toast System
function showToast(message, type = 'success') {
    const container = document.querySelector('.toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast shadow-lg show animate__animated animate__slideInRight`;
    
    const icon = type === 'success' ? 'fa-check-circle text-success' : 
                type === 'error' ? 'fa-times-circle text-danger' : 'fa-info-circle text-info';

    toast.innerHTML = `
        <div class="toast-icon"><i class="fas ${icon}" style="font-size: 1.5rem;"></i></div>
        <div class="toast-content">
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
            <p style="margin: 0; font-size: 0.9rem;">${message}</p>
        </div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.replace('animate__slideInRight', 'animate__slideOutRight');
        setTimeout(() => toast.remove(), 1000);
    }, 4000);
}

// Add to Cart Logic
async function addToCart(productId, quantity = 1) {
    if (!APP_CONFIG.isLoggedIn) {
        showToast('Please login to shop!', 'error');
        return;
    }

    try {
        const response = await fetch(api.cart, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=add&product_id=${productId}&quantity=${quantity}`
        });

        const data = await response.json();
        if (data.status === 'success') {
            showToast(data.message, 'success');
            if (document.getElementById('cartBadge')) {
                document.getElementById('cartBadge').innerText = data.cart_count;
                document.getElementById('cartBadge').style.display = 'flex';
            }
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Network error, try again', 'error');
    }
}

// DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    
    // Add hover sound or slight haptic feedback logic if needed
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Logic for micro-interactions
        });
    });
});
