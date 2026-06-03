<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$cartCount   = getCartCount();
$currentUser = getCurrentUser();
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'PhoneStore') ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>

<nav class="nav">
  <div class="nav__inner">
    <a href="/index.php" class="nav__logo">PhoneStore</a>
    <ul class="nav__links">
      <li><a href="/index.php">Главная</a></li>
      <li><a href="/products.php">Смартфоны</a></li>
      <li><a href="/index.php#about">О нас</a></li>
      <li><a href="/index.php#contacts">Контакты</a></li>
      <?php if ($currentUser): ?>
        <li><a href="/profile.php"><?= htmlspecialchars($currentUser['name']) ?></a></li>
        <?php if ($currentUser['role'] === 'admin'): ?>
          <li><a href="/admin/index.php">Админ</a></li>
        <?php endif; ?>
        <li><a href="/logout.php">Выйти</a></li>
      <?php else: ?>
        <li><a href="/login.php">Войти</a></li>
        <li><a href="/register.php">Регистрация</a></li>
      <?php endif; ?>
    </ul>
    <a href="/cart.php" class="nav__cart-link" id="cart-link">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <span id="cart-badge" class="nav__cart-badge <?= $cartCount === 0 ? 'hidden' : '' ?>"><?= $cartCount ?></span>
    </a>
  </div>
</nav>
