<?php
// profile.php
$pageTitle = 'Мой профиль — PhoneStore';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$db   = getDB();
$user = getCurrentUser();

$orders = $db->prepare("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$orders->execute([$user['id']]);
$orders = $orders->fetchAll();

$msg   = '';
$error = '';

// Change name/password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'name') {
        $name = trim($_POST['name'] ?? '');
        if ($name) {
            $db->prepare("UPDATE users SET name = ? WHERE id = ?")->execute([$name, $user['id']]);
            $_SESSION['user_name'] = $name;
            $msg = 'Имя обновлено';
        }
    }
    if ($action === 'password') {
        $cur = $_POST['current'] ?? '';
        $new = $_POST['new']     ?? '';
        $con = $_POST['confirm'] ?? '';
        $row = $db->prepare("SELECT password FROM users WHERE id = ?")->execute([$user['id']]);
        $row = $db->prepare("SELECT password FROM users WHERE id = ?")->execute([$user['id']]) ? $db->query("SELECT password FROM users WHERE id={$user['id']}")->fetch() : null;

        if ($new !== $con) {
            $error = 'Пароли не совпадают';
        } elseif (strlen($new) < 6) {
            $error = 'Пароль слишком короткий';
        } elseif ($row && password_verify($cur, $row['password'])) {
            $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
            $msg = 'Пароль изменён';
        } else {
            $error = 'Неверный текущий пароль';
        }
    }
}

require_once __DIR__ . '/includes/header.php';

$statusMap = [
    'new'        => ['badge--blue',  'Новый'],
    'processing' => ['badge--gray',  'В работе'],
    'shipped'    => ['badge--blue',  'Отправлен'],
    'delivered'  => ['badge--green', 'Доставлен'],
    'cancelled'  => ['badge--red',   'Отменён'],
];
?>

<section class="section">
  <div class="container">
    <h1 style="font-size:clamp(28px,4vw,44px);font-weight:700;letter-spacing:-.02em;margin-bottom:40px">
      Мой профиль
    </h1>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:32px;align-items:start">

      <!-- LEFT: settings -->
      <div>
        <div class="admin-card" style="margin-bottom:20px">
          <div style="font-size:18px;font-weight:700;margin-bottom:20px">Данные аккаунта</div>
          <?php if ($msg):   ?><div class="form-msg form-msg--success" style="margin-bottom:16px"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
          <?php if ($error): ?><div class="form-msg form-msg--error"   style="margin-bottom:16px"><?= htmlspecialchars($error) ?></div><?php endif; ?>

          <form method="POST" style="margin-bottom:24px">
            <input type="hidden" name="action" value="name">
            <div class="form-group">
              <label class="form-label">Имя</label>
              <input class="form-input" type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input class="form-input" type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity:.6">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Сохранить имя</button>
          </form>

          <hr style="border:none;border-top:1px solid var(--gray-2);margin:20px 0">

          <form method="POST">
            <input type="hidden" name="action" value="password">
            <div style="font-size:15px;font-weight:600;margin-bottom:14px">Сменить пароль</div>
            <div class="form-group">
              <label class="form-label">Текущий пароль</label>
              <input class="form-input" type="password" name="current" required>
            </div>
            <div class="form-group">
              <label class="form-label">Новый пароль</label>
              <input class="form-input" type="password" name="new" required>
            </div>
            <div class="form-group">
              <label class="form-label">Подтверждение</label>
              <input class="form-input" type="password" name="confirm" required>
            </div>
            <button type="submit" class="btn btn-outline btn-sm">Сменить пароль</button>
          </form>
        </div>
      </div>

      <!-- RIGHT: orders -->
      <div>
        <div class="admin-card">
          <div style="font-size:18px;font-weight:700;margin-bottom:20px">Мои заказы (<?= count($orders) ?>)</div>
          <?php if (empty($orders)): ?>
            <div style="text-align:center;padding:40px 0;color:var(--gray-5)">
              <div style="font-size:40px;margin-bottom:12px">📦</div>
              <div>У вас ещё нет заказов</div>
              <a href="/products.php" class="btn btn-primary btn-sm" style="margin-top:16px">Перейти к покупкам</a>
            </div>
          <?php else: ?>
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Товаров</th>
                  <th>Сумма</th>
                  <th>Статус</th>
                  <th>Дата</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($orders as $o): ?>
                  <?php [$cls, $lbl] = $statusMap[$o['status']] ?? ['badge--gray', $o['status']]; ?>
                  <tr>
                    <td><strong>#<?= $o['id'] ?></strong></td>
                    <td><?= $o['item_count'] ?> шт.</td>
                    <td><?= number_format($o['total'],0,',',' ') ?> руб.</td>
                    <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                    <td style="color:var(--gray-5)"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
