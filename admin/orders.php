<?php
// admin/orders.php
$pageTitle = 'Заказы — Админ';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db  = getDB();
$msg = '';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['new','processing','shipped','delivered','cancelled'];
    $status  = in_array($_POST['status'], $allowed) ? $_POST['status'] : 'new';
    $db->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, (int)$_POST['order_id']]);
    $msg = 'Статус обновлён';
}

$orders = $db->query("
    SELECT o.id, o.total, o.status, o.created_at, u.name as user_name, u.email
    FROM orders o JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';

$statusMap = [
    'new'        => ['badge--blue',  'Новый'],
    'processing' => ['badge--gray',  'В работе'],
    'shipped'    => ['badge--blue',  'Отправлен'],
    'delivered'  => ['badge--green', 'Доставлен'],
    'cancelled'  => ['badge--red',   'Отменён'],
];
?>

<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-sidebar__title">Меню</div>
    <ul class="admin-nav">
      <li><a href="/admin/index.php">📊 Дашборд</a></li>
      <li><a href="/admin/products.php">📱 Товары</a></li>
      <li><a href="/admin/orders.php" class="active">📦 Заказы</a></li>
      <li><a href="/admin/users.php">👤 Пользователи</a></li>
      <li><a href="/index.php" style="margin-top:20px;border-top:1px solid rgba(255,255,255,.1);padding-top:20px">← На сайт</a></li>
    </ul>
  </aside>

  <main class="admin-main">
    <h1 style="font-size:28px;font-weight:700;margin-bottom:28px">Заказы</h1>

    <?php if ($msg): ?>
      <div class="form-msg form-msg--success" style="margin-bottom:20px"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="admin-card">
      <?php if (empty($orders)): ?>
        <p style="color:var(--gray-5);text-align:center;padding:60px 0">Заказов пока нет</p>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Покупатель</th>
              <th>Email</th>
              <th>Сумма</th>
              <th>Статус</th>
              <th>Дата</th>
              <th>Изменить статус</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($orders as $o): ?>
              <?php [$cls, $lbl] = $statusMap[$o['status']] ?? ['badge--gray', $o['status']]; ?>
              <tr>
                <td><strong>#<?= $o['id'] ?></strong></td>
                <td><?= htmlspecialchars($o['user_name']) ?></td>
                <td style="color:var(--gray-5)"><?= htmlspecialchars($o['email']) ?></td>
                <td><strong><?= number_format($o['total'],0,',',' ') ?> руб.</strong></td>
                <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                <td style="color:var(--gray-5);white-space:nowrap"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                <td>
                  <form method="POST" style="display:flex;gap:6px;align-items:center">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                    <select name="status" class="filters__select" style="border-radius:6px;padding:6px 28px 6px 10px;font-size:13px">
                      <?php foreach($statusMap as $val => [$_cls, $label]): ?>
                        <option value="<?= $val ?>" <?= $o['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">OK</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
