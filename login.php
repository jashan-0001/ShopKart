<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) redirect('dashboard.php');

$email = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['flash'] = 'Welcome back!';
        redirect('dashboard.php');
    }
    $error = 'Email or password is incorrect.';
}
$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <form class="form-card" method="post">
    <p class="eyebrow">WELCOME BACK</p><h1>Login</h1>
    <p class="muted">Sign in to continue shopping.</p>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Email address<input type="email" name="email" required autocomplete="email" value="<?= e($email) ?>"></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn btn-primary btn-full" type="submit">Login securely</button>
    <p class="form-bottom">New to ShopKart? <a href="register.php">Create an account</a></p>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
