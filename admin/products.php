<?php
// admin/products.php
$pageTitle = 'Товары — Админ';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db  = getDB();
$msg = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare("DELETE FROM products WHERE id = ?")->execute([(int)$_POST['id']]);
        $msg = 'Товар удалён';
    }

    if ($action === 'add' || $action === 'edit') {
        $name  = trim($_POST['name']  ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $desc  = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);

        if ($name && $brand && $price > 0) {
            if ($action === 'add') {
                $db->prepare("INSERT INTO products (name,brand,price,description,image,stock) VALUES (?,?,?,?,?,?)")
                   ->execute([$name, $brand, $price, $desc, $image, $stock]);
                $msg = 'Товар добавлен';
            } else {
                $db->prepare("UPDATE products SET name=?,brand=?,price=?,description=?,image=?,stock=? WHERE id=?")
                   ->execute([$name, $brand, $price, $desc, $image, $stock, (int)$_POST['id']]);
                $msg = 'Товар обновлён';
            }
        }
    }
}

$products = $db->query("SELECT * FROM products ORDER BY id")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-sidebar__title">Меню</div>
    <ul class="admin-nav">
      <li><a href="/admin/index.php">📊 Дашборд</a></li>
      <li><a href="/admin/products.php" class="active">📱 Товары</a></li>
      <li><a href="/admin/orders.php">📦 Заказы</a></li>
      <li><a href="/admin/users.php">👤 Пользователи</a></li>
      <li><a href="/index.php" style="margin-top:20px;border-top:1px solid rgba(255,255,255,.1);padding-top:20px">← На сайт</a></li>
    </ul>
  </aside>

  <main class="admin-main">
    <h1 style="font-size:28px;font-weight:700;margin-bottom:28px">Товары</h1>

    <?php if ($msg): ?>
      <div class="form-msg form-msg--success" style="margin-bottom:20px"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="admin-card" style="margin-bottom:24px">
      <div class="admin-card__header">
        <div class="admin-card__title">Добавить товар</div>
        <button class="btn btn-primary btn-sm" onclick="toggleForm('add-form')">+ Добавить</button>
      </div>

      <div id="add-form" style="display:none;margin-top:16px">
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <?php include __DIR__ . '/partials/product_form.php'; ?>
          <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
      </div>
    </div>

    <div class="admin-card">
      <div class="admin-card__header">
        <div class="admin-card__title">Все товары (<?= count($products) ?>)</div>
      </div>
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Фото</th>
            <th>Название</th>
            <th>Бренд</th>
            <th>Цена</th>
            <th>Склад</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($products as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><img src="<?= htmlspecialchars($p['image']) ?>" style="width:48px;height:48px;object-fit:contain;background:var(--gray-1);border-radius:6px;padding:4px"></td>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
              <td><span class="badge badge--blue"><?= htmlspecialchars($p['brand']) ?></span></td>
              <td><?= number_format($p['price'],0,',',' ') ?> руб.</td>
              <td><?= $p['stock'] ?> шт.</td>
              <td>
                <button class="btn btn-outline btn-sm" onclick="toggleEditForm(<?= $p['id'] ?>)">Изменить</button>
                <form method="POST" style="display:inline" onsubmit="return confirm('Удалить товар?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                </form>
              </td>
            </tr>
            <tr id="edit-row-<?= $p['id'] ?>" style="display:none;background:var(--gray-1)">
              <td colspan="7" style="padding:20px">
                <form method="POST">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <?php include __DIR__ . '/partials/product_form.php'; ?>
                  <button type="submit" class="btn btn-primary">Обновить</button>
                  <button type="button" class="btn btn-outline" onclick="toggleEditForm(<?= $p['id'] ?>)">Отмена</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script>
function toggleForm(id) {
  const el = document.getElementById(id);
  el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function toggleEditForm(id) {
  const row = document.getElementById('edit-row-' + id);
  row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
