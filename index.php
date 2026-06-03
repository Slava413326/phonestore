<?php
// index.php
$pageTitle = 'PhoneStore — Смартфоны по лучшим ценам';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Fetch featured products
$db       = getDB();
$featured = $db->query("SELECT * FROM products ORDER BY id LIMIT 4")->fetchAll();
?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <p class="hero__eyebrow">Новинки 2025</p>
    <h1 class="hero__title">Технологии, которые<br>меняют жизнь</h1>
    <p class="hero__subtitle">Самые новые смартфоны от Apple, Samsung, Xiaomi и Huawei. Официальная гарантия, быстрая доставка.</p>
    <div class="hero__actions">
      <a href="/products.php" class="btn btn-primary">Смотреть каталог</a>
      <a href="#featured" class="btn btn-secondary">Популярные модели</a>
    </div>
  </div>
</section>

<!-- BRANDS -->
<section class="section">
  <div class="container">
    <div style="display:flex;gap:24px;justify-content:center;flex-wrap:wrap;align-items:center;opacity:.45;">
      <?php foreach(['Apple','Samsung','Xiaomi','Huawei'] as $b): ?>
        <a href="/products.php?brand=<?= urlencode($b) ?>" style="font-size:22px;font-weight:700;color:var(--black);letter-spacing:-.02em;text-decoration:none;transition:opacity .2s" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=''">
          <?= $b ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED -->
<section class="section section--gray" id="featured">
  <div class="container">
    <p class="section__eyebrow">Хиты продаж</p>
    <h2 class="section__title">Популярные модели</h2>
    <p class="section__sub">Выбор тысяч покупателей — лучшее соотношение цены и качества</p>

    <div class="products-grid">
      <?php foreach($featured as $p): ?>
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

    <div style="text-align:center;margin-top:40px">
      <a href="/products.php" class="btn btn-outline">Весь каталог</a>
    </div>
  </div>
</section>

<!-- ADVANTAGES -->
<section class="section">
  <div class="container">
    <p class="section__eyebrow">Почему мы</p>
    <h2 class="section__title">Покупайте с уверенностью</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:32px;margin-top:48px">
      <?php
      $advantages = [
        ['🚀','Быстрая доставка','Доставляем по всей России за 1–3 дня. Курьер до двери.'],
        ['🛡️','Официальная гарантия','Все товары с полной гарантией производителя.'],
        ['💳','Рассрочка 0%','Покупайте сейчас, платите удобными частями.'],
        ['🔄','Лёгкий возврат','30 дней на возврат без лишних вопросов.'],
      ];
      foreach($advantages as [$icon, $title, $desc]):
      ?>
        <div style="text-align:center;padding:32px 20px;background:var(--gray-1);border-radius:var(--radius-lg)">
          <div style="font-size:40px;margin-bottom:16px"><?= $icon ?></div>
          <div style="font-size:18px;font-weight:700;margin-bottom:8px"><?= $title ?></div>
          <div style="font-size:14px;color:var(--gray-5);line-height:1.55"><?= $desc ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="section section--gray">
  <div class="container container--narrow" style="text-align:center">
    <p class="section__eyebrow">О нас</p>
    <h2 class="section__title">PhoneStore</h2>
    <p style="font-size:19px;color:var(--gray-5);line-height:1.7;max-width:640px;margin:0 auto">
      Интернет-магазин, специализирующийся на продаже смартфонов от ведущих мировых производителей.
      Более 5 лет на рынке, тысячи довольных покупателей, прямые поставки от официальных дистрибьюторов.
    </p>
  </div>
</section>

<!-- CONTACTS -->
<section id="contacts" class="section">
  <div class="container container--narrow" style="text-align:center">
    <p class="section__eyebrow">Контакты</p>
    <h2 class="section__title">Свяжитесь с нами</h2>
    <div style="display:flex;gap:32px;justify-content:center;flex-wrap:wrap;margin-top:32px">
      <div>
        <div style="font-size:13px;color:var(--gray-4);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Телефон</div>
        <a href="tel:+79991234567" style="font-size:20px;font-weight:600;color:var(--black)">+7 (999) 123-45-67</a>
      </div>
      <div>
        <div style="font-size:13px;color:var(--gray-4);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Email</div>
        <a href="mailto:info@phonestore.ru" style="font-size:20px;font-weight:600">info@phonestore.ru</a>
      </div>
      <div>
        <div style="font-size:13px;color:var(--gray-4);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Адрес</div>
        <span style="font-size:20px;font-weight:600;color:var(--black)">г. Москва, ул. Примерная, д. 10</span>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
