<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$categories = ['Electronics','Mobiles','Fashion','Home','Books & Stationery'];
$q = trim((string)($_GET['q'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));
$sql = 'SELECT * FROM products WHERE 1=1'; $params = [];
if ($q !== '') { $sql .= ' AND (name LIKE ? OR description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if (in_array($category, $categories, true)) { $sql .= ' AND category = ?'; $params[] = $category; }
$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
$pageTitle = 'Shop products';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">THE SHOP</p><h1><?= $category ? e($category) : 'Explore products' ?></h1><p class="muted"><?= count($products) ?> product(s) found<?= $q ? ' for “'.e($q).'”' : '' ?>.</p></section>
<div class="filter-row">
  <a class="filter-chip <?= $category===''?'active':'' ?>" href="products.php">All</a>
  <?php foreach ($categories as $cat): ?><a class="filter-chip <?= $category===$cat?'active':'' ?>" href="products.php?category=<?= urlencode($cat) ?>"><?= e($cat) ?></a><?php endforeach; ?>
</div>
<?php if (!$products): ?><div class="empty-state"><h2>No products found</h2><p>Try a different search or category.</p><a class="btn btn-primary" href="products.php">Clear filters</a></div>
<?php else: ?><div class="product-grid">
<?php foreach ($products as $product): ?>
<article class="product-card">
  <a class="product-image" href="product.php?id=<?= (int)$product['id'] ?>"><img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" loading="lazy"></a>
  <div class="product-info"><span class="tag"><?= e($product['category']) ?></span><a class="product-name" href="product.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a><strong class="price">₹<?= number_format((float)$product['price'],2) ?></strong><span class="muted small"><?= (int)$product['stock'] > 0 ? 'In stock' : 'Out of stock' ?></span><a class="btn btn-primary btn-full" href="product.php?id=<?= (int)$product['id'] ?>">View product</a></div>
</article>
<?php endforeach; ?>
</div><?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
