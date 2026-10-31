<?php
$pageTitle = $pageTitle ?? 'ShopKart';
$notice = flash_message();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | ShopKart</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/app.js" defer></script>
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php">Shop<span>Kart</span></a>
  <form class="search" action="products.php" method="get">
    <input type="search" name="q" placeholder="Search for products, brands and more" value="<?= e($_GET['q'] ?? '') ?>">
    <button type="submit" aria-label="Search">Search</button>
  </form>
  <nav class="nav">
    <a href="products.php">Shop</a>
    <?php if (is_logged_in()): ?>
      <a href="dashboard.php">My Account</a>
      <a href="cart.php">Cart <span class="cart-count"><?= cart_count($pdo) ?></span></a>
      <form class="inline-form" action="logout.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="nav-button" type="submit">Logout</button>
      </form>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a class="nav-join" href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<?php if ($notice !== ''): ?><div class="notice"><?= e($notice) ?></div><?php endif; ?>
<main class="container">
