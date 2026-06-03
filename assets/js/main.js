// assets/js/main.js

// ─── TOAST ──────────────────────────────────────────────────
const toastContainer = (() => {
  let el = document.querySelector('.toast-container');
  if (!el) {
    el = document.createElement('div');
    el.className = 'toast-container';
    document.body.appendChild(el);
  }
  return el;
})();

function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.className = `toast toast--${type}`;
  t.textContent = msg;
  toastContainer.appendChild(t);
  requestAnimationFrame(() => {
    requestAnimationFrame(() => t.classList.add('show'));
  });
  setTimeout(() => {
    t.classList.remove('show');
    setTimeout(() => t.remove(), 400);
  }, 3200);
}

// ─── CART ────────────────────────────────────────────────────
async function addToCart(productId) {
  try {
    const res  = await fetch('/api/cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'add', product_id: productId })
    });
    const data = await res.json();
    if (data.success) {
      updateCartBadge(data.cart_count);
      showToast(data.message || 'Добавлено в корзину', 'success');
    } else {
      showToast(data.error || 'Ошибка', 'error');
    }
  } catch (e) {
    showToast('Ошибка сети', 'error');
  }
}

async function updateCartItem(productId, quantity) {
  const res  = await fetch('/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'update', product_id: productId, quantity })
  });
  const data = await res.json();
  if (data.success) {
    updateCartBadge(data.cart_count);
    if (typeof refreshCart === 'function') refreshCart();
  } else {
    showToast(data.error || 'Ошибка', 'error');
  }
}

async function removeCartItem(productId) {
  const res  = await fetch('/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'remove', product_id: productId })
  });
  const data = await res.json();
  if (data.success) {
    updateCartBadge(data.cart_count);
    if (typeof refreshCart === 'function') refreshCart();
  }
}

function updateCartBadge(count) {
  const badge = document.getElementById('cart-badge');
  if (!badge) return;
  badge.textContent = count;
  badge.classList.toggle('hidden', count === 0);
}

// ─── ADD-TO-CART buttons ─────────────────────────────────────
document.addEventListener('click', e => {
  const btn = e.target.closest('[data-add-cart]');
  if (!btn) return;
  e.preventDefault();
  addToCart(parseInt(btn.dataset.addCart));
});

// ─── NAV active link ─────────────────────────────────────────
document.querySelectorAll('.nav__links a').forEach(a => {
  if (a.href === location.href) a.style.opacity = '1';
});
