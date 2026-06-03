<?php
// cart.php
$pageTitle = 'Корзина — PhoneStore';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$db    = getDB();
$items = [];
$total = 0;

if (isLoggedIn()) {
    $stmt = $db->prepare("
        SELECT c.product_id, c.quantity, p.name, p.brand, p.price, p.image
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.user_id = ?
        ORDER BY c.id
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll();
} else {
    $guestCart = $_SESSION['cart'] ?? [];
    foreach ($guestCart as $pid => $qty) {
        $stmt = $db->prepare("SELECT id as product_id, name, brand, price, image FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        $p = $stmt->fetch();
        if ($p) {
            $p['quantity'] = $qty;
            $items[] = $p;
        }
    }
}

foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<section class="section">
  <div class="container">
    <h1 style="font-size:clamp(28px,4vw,44px);font-weight:700;letter-spacing:-.02em;margin-bottom:40px">Корзина</h1>

    <?php if (empty($items)): ?>
      <div class="cart-empty">
        <div class="cart-empty__icon">🛒</div>
        <div class="cart-empty__title">Корзина пуста</div>
        <div class="cart-empty__sub">Добавьте товары из каталога</div>
        <a href="/products.php" class="btn btn-primary">Перейти к покупкам</a>
      </div>
    <?php else: ?>
      <div class="cart-grid">
        <div id="cart-items">
          <?php foreach($items as $item): ?>
            <div class="cart-item" id="cart-item-<?= $item['product_id'] ?>">
              <img class="cart-item__img" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              <div class="cart-item__info">
                <div class="cart-item__brand"><?= htmlspecialchars($item['brand']) ?></div>
                <div class="cart-item__name"><?= htmlspecialchars($item['name']) ?></div>
                <div class="cart-item__price"><?= number_format($item['price'],0,',',' ') ?> руб. / шт.</div>
              </div>
              <div class="cart-item__qty">
                <button class="qty-btn" onclick="changeQty(<?= $item['product_id'] ?>, -1)">−</button>
                <span class="qty-num" id="qty-<?= $item['product_id'] ?>"><?= $item['quantity'] ?></span>
                <button class="qty-btn" onclick="changeQty(<?= $item['product_id'] ?>, 1)">+</button>
              </div>
              <div class="cart-item__total" id="item-total-<?= $item['product_id'] ?>">
                <?= number_format($item['price'] * $item['quantity'],0,',',' ') ?> руб.
              </div>
              <button class="cart-item__remove" onclick="removeItem(<?= $item['product_id'] ?>)" title="Удалить">×</button>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="cart-summary">
          <div class="cart-summary__title">Итого</div>
          <div class="cart-summary__row">
            <span>Товары (<?= array_sum(array_column($items,'quantity')) ?> шт.)</span>
            <span id="subtotal"><?= number_format($total,0,',',' ') ?> руб.</span>
          </div>
          <div class="cart-summary__row">
            <span>Доставка</span>
            <span style="color:var(--green);font-weight:500">Бесплатно</span>
          </div>
          <hr class="cart-summary__divider">
          <div class="cart-summary__total">
            <span>К оплате</span>
            <span id="grand-total"><?= number_format($total,0,',',' ') ?> руб.</span>
          </div>
          <?php if (!isLoggedIn()): ?>
            <p style="font-size:13px;color:var(--gray-5);margin-bottom:14px">Для оформления заказа необходимо <a href="/login.php">войти</a></p>
          <?php endif; ?>
          <button class="btn btn-primary btn-full" onclick="checkout()" <?= !isLoggedIn() ? 'disabled' : '' ?>>
            Оформить заказ
          </button>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
// Cart item prices for client-side total recalc
const itemPrices = {
  <?php foreach($items as $item): ?>
    <?= $item['product_id'] ?>: <?= $item['price'] ?>,
  <?php endforeach; ?>
};

async function changeQty(pid, delta) {
  const qtyEl = document.getElementById('qty-' + pid);
  let qty = parseInt(qtyEl.textContent) + delta;
  if (qty < 1) { removeItem(pid); return; }
  qtyEl.textContent = qty;
  document.getElementById('item-total-' + pid).textContent = formatNum(itemPrices[pid] * qty) + ' руб.';
  recalcTotal();
  await updateCartItem(pid, qty);
}

async function removeItem(pid) {
  const el = document.getElementById('cart-item-' + pid);
  el.style.transition = 'opacity .3s, transform .3s';
  el.style.opacity = '0';
  el.style.transform = 'translateX(20px)';
  setTimeout(() => { el.remove(); recalcTotal(); }, 300);
  await removeCartItem(pid);
}

function recalcTotal() {
  let total = 0;
  let count = 0;
  document.querySelectorAll('[id^="cart-item-"]').forEach(el => {
    const pid = parseInt(el.id.split('-').pop());
    const qty = parseInt(document.getElementById('qty-' + pid)?.textContent || 0);
    total += (itemPrices[pid] || 0) * qty;
    count += qty;
  });
  const st = document.getElementById('subtotal');
  const gt = document.getElementById('grand-total');
  if (st) st.textContent = formatNum(total) + ' руб.';
  if (gt) gt.textContent = formatNum(total) + ' руб.';
}

function formatNum(n) {
  return n.toLocaleString('ru-RU');
}

async function checkout() {
  const res  = await fetch('/api/checkout.php', { method: 'POST' });
  const data = await res.json();
  if (data.success) {
    showToast('Заказ оформлен! Номер: #' + data.order_id, 'success');
    setTimeout(() => location.href = '/index.php', 2000);
  } else {
    showToast(data.error || 'Ошибка', 'error');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
