<?php
// api/cart.php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$data   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $data['action'] ?? '';
$pid    = (int)($data['product_id'] ?? 0);
$qty    = max(1, (int)($data['quantity'] ?? 1));

if (!$pid && $action !== 'get') {
    echo json_encode(['error' => 'Invalid product_id']);
    exit;
}

$db = getDB();

// Verify product exists
if ($pid) {
    $p = $db->prepare("SELECT id FROM products WHERE id = ?");
    $p->execute([$pid]);
    if (!$p->fetch()) {
        echo json_encode(['error' => 'Product not found']);
        exit;
    }
}

function getCount($db, $userId) {
    if ($userId) {
        $s = $db->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $s->execute([$userId]);
        return (int)$s->fetchColumn();
    }
    return array_sum($_SESSION['cart'] ?? []);
}

$uid = isLoggedIn() ? $_SESSION['user_id'] : null;

switch ($action) {
    case 'add':
        if ($uid) {
            $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,1)
                          ON DUPLICATE KEY UPDATE quantity = quantity + 1")
               ->execute([$uid, $pid]);
        } else {
            $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
        }
        echo json_encode(['success' => true, 'cart_count' => getCount($db, $uid), 'message' => 'Товар добавлен в корзину']);
        break;

    case 'update':
        if ($uid) {
            $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
               ->execute([$qty, $uid, $pid]);
        } else {
            $_SESSION['cart'][$pid] = $qty;
        }
        echo json_encode(['success' => true, 'cart_count' => getCount($db, $uid)]);
        break;

    case 'remove':
        if ($uid) {
            $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
               ->execute([$uid, $pid]);
        } else {
            unset($_SESSION['cart'][$pid]);
        }
        echo json_encode(['success' => true, 'cart_count' => getCount($db, $uid)]);
        break;

    case 'get':
        echo json_encode(['success' => true, 'cart_count' => getCount($db, $uid)]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
