<?php
// db_check.php — диагностика
?><!DOCTYPE html>
<html><head><meta charset='utf-8'><title>Диагностика</title>
<style>
body{font-family:-apple-system,sans-serif;padding:32px;background:#f5f5f7;color:#1d1d1f}
.card{background:#fff;border-radius:14px;padding:24px;max-width:800px;
      box-shadow:0 4px 20px rgba(0,0,0,.08);margin:0 auto 20px}
h2{font-size:18px;margin-bottom:16px}
.row{display:flex;justify-content:space-between;padding:9px 0;
     border-bottom:1px solid #f0f0f0;font-size:14px;align-items:flex-start;gap:20px}
.label{color:#555;flex-shrink:0;min-width:220px}
.ok{color:#27ae60;font-weight:600}
.fail{color:#e74c3c;font-weight:600}
.warn{color:#e67e22;font-weight:600}
code{background:#f0f0f0;padding:2px 7px;border-radius:4px;font-size:12px}
pre{background:#1d1d1f;color:#a8ff78;padding:14px;border-radius:8px;
    font-size:12px;margin:10px 0;overflow-x:auto;white-space:pre-wrap;word-break:break-all}
</style></head>
<body>
<div class='card'>
<h2>🔍 Диагностика PHP + MariaDB</h2>

<?php
function row($label, $val) {
    echo "<div class='row'><span class='label'>{$label}</span><span>{$val}</span></div>";
}

row('PHP версия', PHP_VERSION);
row('OS', PHP_OS . ' ' . php_uname('r'));
row('PDO доступен', class_exists('PDO') ? "<span class='ok'>✓ Да</span>" : "<span class='fail'>✗ Нет</span>");
row('PDO MySQL драйвер', in_array('mysql', PDO::getAvailableDrivers()) ? "<span class='ok'>✓ Да</span>" : "<span class='fail'>✗ Нет — нужно включить php_pdo_mysql в php.ini</span>");

// Проверяем TCP порты через fsockopen
$openPorts = [];
for ($p = 3300; $p <= 3315; $p++) {
    $fp = @fsockopen('127.0.0.1', $p, $e, $es, 0.5);
    if ($fp) { $openPorts[] = $p; fclose($fp); }
}
row('Открытые TCP порты (3300-3315)',
    $openPorts ? "<span class='ok'>✓ " . implode(', ', $openPorts) . "</span>"
               : "<span class='fail'>Нет — MariaDB не слушает TCP!</span>");

// Пробуем PDO подключения
$dsns = [
    '127.0.0.1:3306 TCP'  => "mysql:host=127.0.0.1;port=3306;charset=utf8mb4",
    'localhost:3306 TCP'   => "mysql:host=localhost;port=3306;charset=utf8mb4",
    'Named pipe mysql'     => "mysql:unix_socket=\\\\.\\pipe\\mysql;charset=utf8mb4",
    'Named pipe MariaDB'   => "mysql:unix_socket=\\\\.\\pipe\\MariaDB;charset=utf8mb4",
    'Named pipe mysqld'    => "mysql:unix_socket=\\\\.\\pipe\\mysqld;charset=utf8mb4",
];

$connectedDSN = null;
$connectedPDO = null;

foreach ($dsns as $label => $dsn) {
    try {
        $p = new PDO($dsn, 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 2]);
        $connectedDSN = $label;
        $connectedPDO = $p;
        row("PDO: {$label}", "<span class='ok'>✓ Подключено!</span>");
    } catch (PDOException $ex) {
        $short = substr($ex->getMessage(), 0, 120);
        row("PDO: {$label}", "<span class='fail'>✗ " . htmlspecialchars($short) . "</span>");
    }
}

if ($connectedPDO) {
    $ver = $connectedPDO->query("SELECT VERSION()")->fetchColumn();
    row('Версия MariaDB', htmlspecialchars($ver));

    $res = $connectedPDO->query("SELECT @@skip_networking, @@bind_address, @@port, @@socket")->fetch();
    row('skip_networking', $res['@@skip_networking'] == 1
        ? "<span class='fail'>1 — TCP отключён! Это причина проблемы.</span>"
        : "<span class='ok'>0 — OK</span>");
    row('bind_address', htmlspecialchars($res['@@bind_address']));
    row('port (реальный)', htmlspecialchars($res['@@port']));
    row('socket/pipe', htmlspecialchars($res['@@socket'] ?? 'нет'));

    $dbs = $connectedPDO->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    row('База phonestore',
        in_array('phonestore', $dbs)
        ? "<span class='ok'>✓ Существует</span>"
        : "<span class='fail'>✗ Не найдена — импортируйте install.sql</span>");
}
?>
</div>

<?php if ($connectedPDO && $connectedDSN): ?>
<div class='card'>
<h2>✅ Используйте этот DSN в db.php:</h2>
<pre>// Рабочее подключение: <?= htmlspecialchars($connectedDSN) ?>
</pre>
</div>
<?php else: ?>
<div class='card' style='border:2px solid #e74c3c'>
<h2 style='color:#c0392b'>❌ Подключение не найдено</h2>
<p style='margin-bottom:12px'>Выполните этот запрос в phpMyAdmin → SQL:</p>
<pre>SELECT @@skip_networking, @@bind_address, @@port, @@socket;</pre>
<p>И пришлите результат.</p>
</div>
<?php endif; ?>

<p style='text-align:center;font-size:12px;color:#aaa;margin-top:12px'>Удалите db_check.php после настройки</p>
</body></html>
