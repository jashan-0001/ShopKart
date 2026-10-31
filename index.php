<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$categories = ['Electronics', 'Mobiles', 'Fashion', 'Home', 'Books & Stationery'];
$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC, id DESC LIMIT 8');
$products = $stmt->fetchAll();
$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div>
    <p class="eyebrow">EVERYDAY DEALS, MADE SIMPLE</p>
    <h1>Find something<br>you’ll love.</h1>
    <p>Explore useful products across electronics, fashion, home and more.</p>
    <a class="btn btn-light" href="products.php">Start shopping</a>
  </div>
  <div class="hero-art"><span>SHOP</span><strong>SMART.</strong><small>Great finds for every day</small></div>
</section>

<section class="section-head">
  <div><p class="eyebrow">BROWSE</p><h2>Shop by category</h2></div>
</section>
<div class="category-grid">
  <?php foreach ($categories as $category): ?>
    <a class="category-tile" href="products.php?category=<?= urlencode($category) ?>">
      <span class="category-icon"><?= e(match($category) {'Electronics'=>'⌁','Mobiles'=>'▣','Fashion'=>'✦','Home'=>'⌂',default=>'▤'}) ?></span>
      <strong><?= e($category) ?></strong><span>Explore products →</span>
    </a>
  <?php endforeach; ?>
</div>

<section class="section-head">
  <div><p class="eyebrow">JUST FOR YOU</p><h2>Popular products</h2></div>
  <a href="products.php" class="text-link">View all products →</a>
</section>
<div class="product-grid">
<?php foreach ($products as $product): ?>
  <article class="product-card">
    <a class="product-image" href="product.php?id=<?= (int)$product['id'] ?>">
      <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
    </a>
    <div class="product-info">
      <span class="tag"><?= e($product['category']) ?></span>
      <a class="product-name" href="product.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a>
      <strong class="price">₹<?= number_format((float)$product['price'], 2) ?></strong>
      <a class="btn btn-primary btn-full" href="product.php?id=<?= (int)$product['id'] ?>">View product</a>
    </div>
  </article>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
