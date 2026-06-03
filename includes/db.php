<?php
// includes/db.php
// Настройки подключения к БД — OpenServer 5
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'phonestore');
define('DB_PORT', '3306');

function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    // OpenServer 5 использует MySQL/MariaDB через TCP localhost или named pipe
    $attempts = [
        // TCP — самый надёжный вариант в OpenServer 5
        "mysql:host=127.0.0.1;port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        "mysql:host=localhost;port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        // Named pipe OpenServer 5 (MySQL 5.x / MariaDB)
        "mysql:unix_socket=\\\\.\\pipe\\MySQL;dbname=" . DB_NAME . ";charset=utf8mb4",
        "mysql:unix_socket=\\\\.\\pipe\\mysql;dbname=" . DB_NAME . ";charset=utf8mb4",
        "mysql:unix_socket=\\\\.\\pipe\\MariaDB;dbname=" . DB_NAME . ";charset=utf8mb4",
    ];

    $lastError = '';
    foreach ($attempts as $dsn) {
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $lastError = $e->getMessage();
            $pdo = null;
        }
    }

    // Не удалось подключиться — показываем понятную ошибку
    $safeError = htmlspecialchars($lastError, ENT_QUOTES);
    die("<!DOCTYPE html><html><head><meta charset='utf-8'><title>Ошибка БД</title>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;padding:40px;background:#fff5f5;margin:0}
.b{max-width:640px;margin:0 auto;background:#fff;border-radius:14px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.1)}
h2{color:#c0392b;margin:0 0 16px}
code{background:#f0f0f0;padding:2px 7px;border-radius:4px;font-size:13px}
pre{background:#1d1d1f;color:#a8ff78;padding:14px;border-radius:8px;font-size:13px;margin:12px 0;overflow-x:auto}
ul{padding-left:20px;line-height:2}
</style></head><body><div class='b'>
<h2>⚠ Ошибка подключения к MySQL</h2>
<p>Не удалось подключиться к базе данных <code>" . DB_NAME . "</code>.</p>
<p><b>Последняя ошибка:</b> <code>{$safeError}</code></p>
<h3>Что нужно проверить:</h3>
<ul>
  <li>OpenServer 5 запущен и MySQL/MariaDB активен (зелёный флаг)</li>
  <li>База данных <code>" . DB_NAME . "</code> создана — откройте phpMyAdmin и импортируйте <code>install.sql</code></li>
  <li>Пользователь <code>" . DB_USER . "</code> имеет доступ к базе</li>
  <li>Порт <code>" . DB_PORT . "</code> не занят другим процессом</li>
</ul>
<p>Если порт MySQL отличается от 3306, измените константу <code>DB_PORT</code> в файле <code>includes/db.php</code></p>
</div></body></html>");
}
