<?php
// admin/index.php
$pageTitle = 'Админ-панель — PhoneStore';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db = getDB();

$stats = [
    'products' => $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders'   => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'users'    => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'revenue'  => $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn(),
];

$recentOrders = $db->query("
    SELECT o.id, o.total, o.status, o.created_at, u.name as user_name
    FROM orders o JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC LIMIT 8
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar__title">Меню</div>
    <ul class="admin-nav">
      <li><a href="/admin/index.php" class="active">📊 Дашборд</a></li>
      <li><a href="/admin/products.php">📱 Товары</a></li>
      <li><a href="/admin/orders.php">📦 Заказы</a></li>
      <li><a href="/admin/users.php">👤 Пользователи</a></li>
      <li><a href="/index.php" style="margin-top:20px;border-top:1px solid rgba(255,255,255,.1);padding-top:20px">← На сайт</a></li>
    </ul>
  </aside>

  <!-- Main -->
  <main class="admin-main">
    <h1 style="font-size:28px;font-weight:700;margin-bottom:28px">Дашборд</h1>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card__label">Товары</div>
        <div class="stat-card__value"><?= $stats['products'] ?></div>
        <div class="stat-card__sub">в каталоге</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__label">Заказы</div>
        <div class="stat-card__value"><?= $stats['orders'] ?></div>
        <div class="stat-card__sub">всего</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__label">Пользователи</div>
        <div class="stat-card__value"><?= $stats['users'] ?></div>
        <div class="stat-card__sub">зарегистрировано</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__label">Выручка</div>
        <div class="stat-card__value"><?= number_format($stats['revenue'],0,',',' ') ?></div>
        <div class="stat-card__sub">руб.</div>
      </div>
    </div>

    <div class="admin-card">
      <div class="admin-card__header">
        <div class="admin-card__title">Последние заказы</div>
        <a href="/admin/orders.php" class="btn btn-outline btn-sm">Все заказы</a>
      </div>

      <?php if (empty($recentOrders)): ?>
        <p style="color:var(--gray-5);text-align:center;padding:40px 0">Заказов пока нет</p>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Покупатель</th>
              <th>Сумма</th>
              <th>Статус</th>
              <th>Дата</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recentOrders as $o): ?>
              <tr>
                <td><strong>#<?= $o['id'] ?></strong></td>
                <td><?= htmlspecialchars($o['user_name']) ?></td>
                <td><?= number_format($o['total'],0,',',' ') ?> руб.</td>
                <td>
                  <?php
                  $statusMap = [
                    'new'        => ['badge--blue',  'Новый'],
                    'processing' => ['badge--gray',  'В работе'],
                    'shipped'    => ['badge--blue',  'Отправлен'],
                    'delivered'  => ['badge--green', 'Доставлен'],
                    'cancelled'  => ['badge--red',   'Отменён'],
                  ];
                  [$cls, $lbl] = $statusMap[$o['status']] ?? ['badge--gray', $o['status']];
                  ?>
                  <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                </td>
                <td style="color:var(--gray-5)"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
