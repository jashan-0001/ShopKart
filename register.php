<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$name = '';
$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if ($name === '' || mb_strlen($name) > 100) {
        $error = 'Enter your name (up to 100 characters).';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $name)) {
        $error = 'Name must be 3–30 characters and contain only letters, numbers, or underscores.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            session_regenerate_id(true);
            $_SESSION['flash'] = 'Your account has been created.';
            redirect('dashboard.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'This name or email address is already registered. Please choose another.';
            } else {
                throw $e;
            }
        }
    }
}

$pageTitle = 'Create account';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <form class="form-card" method="post">
    <p class="eyebrow">JOIN SHOPKART</p>
    <h1>Create account</h1>
    <p class="muted">Create an account to save your cart and track orders.</p>

    <?php if ($error): ?>
      <div class="error"><?= e($error) ?></div>
    <?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <label>
      Username
      <input
        name="name"
        minlength="3"
        maxlength="30"
        pattern="[a-zA-Z0-9_]+"
        required
        autocomplete="username"
        value="<?= e($name) ?>"
      >
    </label>

    <label>
      Email address
      <input type="email" name="email" required autocomplete="email" value="<?= e($email) ?>">
    </label>

    <label>
      Password
      <input type="password" name="password" minlength="8" required autocomplete="new-password">
    </label>

    <button class="btn btn-primary btn-full" type="submit">Create account</button>
    <p class="form-bottom">Already registered? <a href="login.php">Login</a></p>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
