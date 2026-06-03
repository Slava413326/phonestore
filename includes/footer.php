<?php // includes/footer.php ?>
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div>
        <div class="footer__col-title">PhoneStore</div>
        <p style="font-size:13px;line-height:1.6">Лучшие смартфоны по доступным ценам. Официальная гарантия на все модели.</p>
      </div>
      <div>
        <div class="footer__col-title">Каталог</div>
        <ul class="footer__links">
          <li><a href="/products.php?brand=Apple">Apple</a></li>
          <li><a href="/products.php?brand=Samsung">Samsung</a></li>
          <li><a href="/products.php?brand=Xiaomi">Xiaomi</a></li>
          <li><a href="/products.php?brand=Huawei">Huawei</a></li>
        </ul>
      </div>
      <div>
        <div class="footer__col-title">Покупателям</div>
        <ul class="footer__links">
          <li><a href="#">Доставка и оплата</a></li>
          <li><a href="#">Возврат товара</a></li>
          <li><a href="#">Гарантия</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
      <div>
        <div class="footer__col-title">Контакты</div>
        <ul class="footer__links">
          <li><a href="tel:+79991234567">+7 (999) 123-45-67</a></li>
          <li><a href="mailto:info@phonestore.ru">info@phonestore.ru</a></li>
          <li>г. Москва, ул. Примерная, д. 10</li>
        </ul>
      </div>
    </div>
    <div class="footer__bottom">
      <span>&copy; <?= date('Y') ?> PhoneStore. Все права защищены.</span>
      <span>Работает на OpenServer + PHP + MySQL</span>
    </div>
  </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>
