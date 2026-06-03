<?php
// login.php
$pageTitle = 'Вход — PhoneStore';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role']       = $user['role'];

            // Merge guest cart into DB
            if (!empty($_SESSION['cart'])) {
                $db2 = getDB();
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    $db2->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)
                                   ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)")
                        ->execute([$user['id'], $pid, $qty]);
                }
                unset($_SESSION['cart']);
            }

            header('Location: /index.php');
            exit;
        } else {
            $error = 'Неверный email или пароль';
        }
    } else {
        $error = 'Заполните все поля';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <h1 class="auth-card__title">Вход</h1>
    <p class="auth-card__sub">Войдите, чтобы управлять заказами</p>

    <?php if ($error): ?>
      <div class="form-msg form-msg--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" style="margin-top:24px">
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input class="form-input" type="email" id="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Пароль</label>
        <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">Войти</button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--gray-5)">
      Нет аккаунта? <a href="/register.php">Зарегистрируйтесь</a>
    </p>
    <p style="text-align:center;margin-top:8px;font-size:13px;color:var(--gray-4)">
      Демо-доступ: slava@gmail.com / 123456
    </p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
