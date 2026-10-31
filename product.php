<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?'); $stmt->execute([$id ?: 0]); $product = $stmt->fetch();
if (!$product) { http_response_code(404); $pageTitle='Product not found'; require __DIR__.'/includes/header.php'; echo '<div class="empty-state"><h1>Product not found</h1><a href="products.php">Back to shop</a></div>'; require __DIR__.'/includes/footer.php'; exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); require_login();
    $qty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $qty = max(1, min(10, (int)$qty));
    if ((int)$product['stock'] < $qty) {
        $error = 'Not enough stock for that quantity.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = LEAST(quantity + VALUES(quantity), 10)');
        $stmt->execute([$_SESSION['user_id'], $product['id'], $qty]);
        $_SESSION['flash'] = 'Product added to your cart.';
        redirect('cart.php');
    }
}
$pageTitle = $product['name']; require __DIR__ . '/includes/header.php';
?>
<div class="detail-layout">
  <div class="detail-image"><img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>"></div>
  <div class="detail-info"><span class="tag"><?= e($product['category']) ?></span><h1><?= e($product['name']) ?></h1><div class="detail-price">₹<?= number_format((float)$product['price'],2) ?></div><p><?= e($product['description']) ?></p><p class="muted"><?= (int)$product['stock'] ?> available</p>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <?php if ((int)$product['stock'] > 0): ?>
    <form method="post" class="add-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Quantity <input type="number" name="quantity" value="1" min="1" max="10"></label><button class="btn btn-primary" type="submit">Add to cart</button></form>
    <?php else: ?><p class="error">Currently out of stock.</p><?php endif; ?>
    <p class="muted small">Demo store: online payment is not enabled.</p>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
