<?php
// products.php
$pageTitle = 'Смартфоны — PhoneStore';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$db = getDB();

$brand = $_GET['brand'] ?? '';
$price = $_GET['price'] ?? '';
$sort  = $_GET['sort']  ?? 'id';

$where  = ['1=1'];
$params = [];

if ($brand) {
    $where[]  = 'brand = ?';
    $params[] = $brand;
}

if ($price) {
    [$min, $max] = array_map('intval', explode('-', $price));
    $where[]  = 'price >= ?';
    $params[] = $min;
    if ($max) {
        $where[]  = 'price <= ?';
        $params[] = $max;
    }
}

// PHP 7 compatible (match() requires PHP 8.0+)
switch ($sort) {
    case 'price_asc':  $orderBy = 'price ASC';  break;
    case 'price_desc': $orderBy = 'price DESC'; break;
    case 'name':       $orderBy = 'name ASC';   break;
    default:           $orderBy = 'id ASC';     break;
}

$sql      = "SELECT * FROM products WHERE " . implode(' AND ', $where) . " ORDER BY $orderBy";
$stmt     = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$brands  = $db->query("SELECT DISTINCT brand FROM products ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="section">
  <div class="container">
    <h1 class="section__title" style="margin-bottom:40px">
      <?= $brand ? htmlspecialchars($brand) . ' — смартфоны' : 'Все смартфоны' ?>
    </h1>

    <!-- FILTERS -->
    <form method="GET" class="filters" id="filter-form">
      <select name="brand" class="filters__select" onchange="this.form.submit()">
        <option value="">Все бренды</option>
        <?php foreach($brands as $b): ?>
          <option value="<?= htmlspecialchars($b) ?>" <?= $brand === $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="price" class="filters__select" onchange="this.form.submit()">
        <option value="">Любая цена</option>
        <option value="0-30000"    <?= $price === '0-30000'    ? 'selected' : '' ?>>До 30 000 руб.</option>
        <option value="30000-60000" <?= $price === '30000-60000' ? 'selected' : '' ?>>30 000 – 60 000 руб.</option>
        <option value="60000-90000" <?= $price === '60000-90000' ? 'selected' : '' ?>>60 000 – 90 000 руб.</option>
        <option value="90000-99999999" <?= $price === '90000-99999999' ? 'selected' : '' ?>>Свыше 90 000 руб.</option>
      </select>

      <select name="sort" class="filters__select" onchange="this.form.submit()">
        <option value="id"         <?= $sort === 'id'         ? 'selected' : '' ?>>По умолчанию</option>
        <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Сначала дешевле</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Сначала дороже</option>
        <option value="name"       <?= $sort === 'name'       ? 'selected' : '' ?>>По названию</option>
      </select>

      <?php if ($brand || $price || $sort !== 'id'): ?>
        <a href="/products.php" class="btn btn-outline btn-sm">Сбросить</a>
      <?php endif; ?>

      <span style="margin-left:auto;font-size:14px;color:var(--gray-5)"><?= count($products) ?> товаров</span>
    </form>

    <?php if (empty($products)): ?>
      <div style="text-align:center;padding:80px 0">
        <div style="font-size:48px;margin-bottom:16px">🔍</div>
        <h3 style="font-size:22px;font-weight:700;margin-bottom:8px">Ничего не найдено</h3>
        <p style="color:var(--gray-5)">Попробуйте изменить фильтры</p>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach($products as $p): ?>
          <div class="product-card">
            <div class="product-card__img-wrap">
              <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
            </div>
            <div class="product-card__body">
              <div class="product-card__brand"><?= htmlspecialchars($p['brand']) ?></div>
              <div class="product-card__name"><?= htmlspecialchars($p['name']) ?></div>
              <div class="product-card__desc"><?= htmlspecialchars($p['description']) ?></div>
              <div class="product-card__footer">
                <div class="product-card__price"><?= number_format($p['price'],0,',',' ') ?> <span>руб.</span></div>
                <button class="btn btn-primary btn-sm" data-add-cart="<?= $p['id'] ?>">В корзину</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
