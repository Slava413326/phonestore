<?php
// includes/db.php
// Поддержка Railway (переменные окружения) и OpenServer (локально)

define('DB_HOST', getenv('MYSQLHOST')     ?: getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('MYSQLUSER')     ?: getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'phonestore');
define('DB_PORT', getenv('MYSQLPORT')     ?: getenv('DB_PORT') ?: '3306');

function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        $safeError = htmlspecialchars($e->getMessage(), ENT_QUOTES);
        die("<!DOCTYPE html><html><head><meta charset='utf-8'><title>Ошибка БД</title>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;padding:40px;background:#fff5f5;margin:0}
.b{max-width:640px;margin:0 auto;background:#fff;border-radius:14px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.1)}
h2{color:#c0392b;margin:0 0 16px}
code{background:#f0f0f0;padding:2px 7px;border-radius:4px;font-size:13px}
ul{padding-left:20px;line-height:2}
</style></head><body><div class='b'>
<h2>⚠ Ошибка подключения к MySQL</h2>
<p>Не удалось подключиться к базе данных <code>" . DB_NAME . "</code>.</p>
<p><b>Последняя ошибка:</b> <code>{$safeError}</code></p>
<h3>Что нужно проверить:</h3>
<ul>
  <li>Переменные окружения MYSQLHOST, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD, MYSQLPORT заданы</li>
  <li>База данных создана и SQL-схема импортирована</li>
</ul>
</div></body></html>");
    }
}
