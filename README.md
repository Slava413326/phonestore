# PhoneStore — PHP интернет-магазин смартфонов

## Стек
- **Бэкенд:** PHP 7.4+ / PHP 8.x
- **База данных:** MySQL 5.7+ / MariaDB (через PDO)
- **Сервер:** OpenServer 5 (Apache + mod_php)
- **Фронтенд:** Vanilla JS + CSS (Apple-inspired design)

---

## Установка в OpenServer 5

### 1. Скопируйте проект
Разместите папку `phonestore` в папку сайтов OpenServer 5:
```
C:\OSPanel\domains\phonestore\
```

### 2. Запустите OpenServer 5
- Убедитесь, что в трее флаг **зелёный**
- MySQL и Apache должны быть активны

### 3. Создайте базу данных
Откройте **phpMyAdmin** (через меню OpenServer → Дополнительно → phpMyAdmin):

1. Создайте базу данных `phonestore` с кодировкой `utf8mb4_unicode_ci`
2. Выберите базу `phonestore` слева
3. Перейдите на вкладку **SQL**
4. Вставьте содержимое файла `install.sql` и нажмите **Вперёд**

### 4. Проверьте настройки подключения
Откройте `includes/db.php`. По умолчанию:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');      // пароль root в OpenServer 5 обычно пустой
define('DB_NAME', 'phonestore');
define('DB_PORT', '3306');
```

Если у вас другой пароль root — измените `DB_PASS`.

### 5. Откройте сайт
```
http://phonestore/
```
или (если домен не настроен):
```
http://localhost/phonestore/
```

---

## Доступ к сайту

### Покупатель
- Регистрация: `/register.php`
- Вход: `/login.php`

### Администратор (создан автоматически)
- Email: `admin@phonestore.ru`
- Пароль: `admin123`
- Панель: `/admin/index.php`

---

## Структура проекта

```
phonestore/
├── index.php           # Главная страница
├── products.php        # Каталог товаров
├── cart.php            # Корзина
├── login.php           # Вход
├── register.php        # Регистрация
├── logout.php          # Выход
├── profile.php         # Профиль пользователя
├── install.sql         # SQL для создания БД
├── .htaccess           # Apache настройки
│
├── includes/
│   ├── db.php          # Подключение к БД (настройте здесь)
│   ├── auth.php        # Функции авторизации
│   ├── header.php      # Шапка сайта
│   └── footer.php      # Подвал сайта
│
├── api/
│   ├── cart.php        # API корзины (AJAX)
│   └── checkout.php    # API оформления заказа
│
├── admin/
│   ├── index.php       # Дашборд
│   ├── products.php    # Управление товарами
│   ├── orders.php      # Управление заказами
│   └── users.php       # Управление пользователями
│
└── assets/
    ├── css/style.css
    └── js/main.js
```

---

## Решение типичных проблем в OpenServer 5

| Проблема | Решение |
|---|---|
| Белый экран / ошибка БД | Запустите MySQL в OS5, создайте БД через phpMyAdmin |
| 404 на всех страницах | Проверьте, что домен `phonestore` добавлен в OS5 |
| "Access denied for user root" | Укажите правильный пароль в `DB_PASS` в `includes/db.php` |
| Страница работает, но без стилей | Проверьте путь `/assets/css/style.css` через инструменты браузера |

---

## Безопасность
- Пароли хранятся в bcrypt-хешах (`password_hash`)
- Входные данные экранируются через `htmlspecialchars`
- Запросы к БД только через PDO prepared statements
- Страницы профиля и админки защищены проверкой сессии
