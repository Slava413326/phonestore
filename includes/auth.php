<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['role'],
    ];
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /index.php');
        exit;
    }
}

function getCartCount() {
    if (!isLoggedIn()) {
        // Guest cart is stored as [product_id => quantity]
        $cart = $_SESSION['cart'] ?? [];
        return array_sum($cart); // Fix: was array_column($cart, 'quantity') which returns 0
    }
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    } catch (Exception $e) {
        return 0;
    }
}
