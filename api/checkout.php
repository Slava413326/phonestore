<?php
// api/checkout.php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Необходимо войти в систему']);
    exit;
}

$db  = getDB();
$uid = $_SESSION['user_id'];

// Get cart items
$stmt = $db->prepare("
    SELECT c.product_id, c.quantity, p.price, p.stock
    FROM cart c JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$stmt->execute([$uid]);
$items = $stmt->fetchAll();

if (empty($items)) {
    echo json_encode(['error' => 'Корзина пуста']);
    exit;
}

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

try {
    $db->beginTransaction();

    $db->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'new')")
       ->execute([$uid, $total]);
    $orderId = $db->lastInsertId();

    foreach ($items as $item) {
        $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)")
           ->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
    }

    $db->prepare("DELETE FROM cart WHERE user_id = ?")
       ->execute([$uid]);

    $db->commit();

    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['error' => 'Ошибка при оформлении заказа']);
}
