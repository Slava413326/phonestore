<?php
// admin/users.php
$pageTitle = 'Пользователи — Админ';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db  = getDB();
$msg = '';

// Toggle role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $role = in_array($_POST['role'], ['user','admin']) ? $_POST['role'] : 'user';
    if ((int)$_POST['user_id'] !== $_SESSION['user_id']) { // Can't change own role
        $db->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, (int)$_POST['user_id']]);
        $msg = 'Роль обновлена';
    }
}

$users = $db->query("
    SELECT u.*, COUNT(o.id) as order_count
    FROM users u
    LEFT JOIN orders o ON o.user_id = u.id
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-sidebar__title">Меню</div>
    <ul class="admin-nav">
      <li><a href="/admin/index.php">📊 Дашборд</a></li>
      <li><a href="/admin/products.php">📱 Товары</a></li>
      <li><a href="/admin/orders.php">📦 Заказы</a></li>
      <li><a href="/admin/users.php" class="active">👤 Пользователи</a></li>
      <li><a href="/index.php" style="margin-top:20px;border-top:1px solid rgba(255,255,255,.1);padding-top:20px">← На сайт</a></li>
    </ul>
  </aside>

  <main class="admin-main">
    <h1 style="font-size:28px;font-weight:700;margin-bottom:28px">Пользователи</h1>

    <?php if ($msg): ?>
      <div class="form-msg form-msg--success" style="margin-bottom:20px"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="admin-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Заказы</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($users as $u): ?>
            <tr>
              <td><?= $u['id'] ?></td>
              <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
              <td style="color:var(--gray-5)"><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="badge <?= $u['role'] === 'admin' ? 'badge--red' : 'badge--gray' ?>">
                  <?= $u['role'] === 'admin' ? 'Админ' : 'Пользователь' ?>
                </span>
              </td>
              <td><?= $u['order_count'] ?></td>
              <td style="color:var(--gray-5)"><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
              <td>
                <?php if ((int)$u['id'] !== $_SESSION['user_id']): ?>
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="role" value="<?= $u['role'] === 'admin' ? 'user' : 'admin' ?>">
                    <button type="submit" class="btn btn-outline btn-sm">
                      <?= $u['role'] === 'admin' ? 'Убрать админа' : 'Сделать админом' ?>
                    </button>
                  </form>
                <?php else: ?>
                  <span style="color:var(--gray-4);font-size:13px">Это вы</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
