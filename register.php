<?php
// register.php
$pageTitle = 'Регистрация — PhoneStore';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Заполните все поля';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        $db   = getDB();
        $chk  = $db->prepare("SELECT id FROM users WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'Пользователь с таким email уже существует';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $ins->execute([$name, $email, $hash]);
            $success = 'Регистрация прошла успешно! <a href="/login.php">Войдите</a>';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <h1 class="auth-card__title">Регистрация</h1>
    <p class="auth-card__sub">Создайте аккаунт для управления заказами</p>

    <?php if ($error):   ?><div class="form-msg form-msg--error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="form-msg form-msg--success"><?= $success ?></div><?php endif; ?>

    <form method="POST" style="margin-top:24px">
      <div class="form-group">
        <label class="form-label" for="name">Имя</label>
        <input class="form-input" type="text" id="name" name="name" required placeholder="Иван Иванов" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input class="form-input" type="email" id="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Пароль</label>
        <input class="form-input" type="password" id="password" name="password" required placeholder="Минимум 6 символов">
      </div>
      <div class="form-group">
        <label class="form-label" for="confirm">Подтвердите пароль</label>
        <input class="form-input" type="password" id="confirm" name="confirm" required placeholder="Повторите пароль">
      </div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">Зарегистрироваться</button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--gray-5)">
      Уже есть аккаунт? <a href="/login.php">Войдите</a>
    </p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
