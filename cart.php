<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($action === 'remove' && $productId) {
        $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$_SESSION['user_id'], $productId]);
        $_SESSION['flash'] = 'Item removed from cart.';
    } elseif ($action === 'update' && $productId) {
        $qty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        if (!$qty || $qty < 1) {
            $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$_SESSION['user_id'], $productId]);
        } else {
            $stmt = $pdo->prepare('UPDATE cart_items SET quantity = LEAST(?, 10) WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$qty, $_SESSION['user_id'], $productId]);
        }
        $_SESSION['flash'] = 'Cart updated.';
    }
    redirect('cart.php');
}
$stmt = $pdo->prepare('SELECT c.product_id, c.quantity, p.name, p.price, p.image_url, p.stock FROM cart_items c JOIN products p ON p.id=c.product_id WHERE c.user_id=? ORDER BY c.id DESC');
$stmt->execute([$_SESSION['user_id']]); $items = $stmt->fetchAll();
$total = 0; foreach ($items as $item) $total += (float)$item['price'] * (int)$item['quantity'];
$pageTitle = 'Shopping cart'; require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">YOUR BAG</p><h1>Shopping cart</h1><p class="muted">Review your items before checkout.</p></section>
<?php if (!$items): ?><div class="empty-state"><div class="empty-icon">🛒</div><h2>Your cart is empty</h2><p>Find something you love and add it here.</p><a class="btn btn-primary" href="products.php">Browse products</a></div>
<?php else: ?>
<div class="cart-layout"><section class="cart-items">
<?php foreach ($items as $item): ?>
<article class="cart-item">
  <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>">
  <div class="cart-item-info"><h3><?= e($item['name']) ?></h3><strong>₹<?= number_format((float)$item['price'],2) ?></strong><p class="muted small">Subtotal: ₹<?= number_format((float)$item['price']*(int)$item['quantity'],2) ?></p>
    <div class="cart-actions">
      <form method="post" class="quantity-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>"><input type="hidden" name="action" value="update"><label class="small">Qty <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="10"></label><button class="text-button" type="submit">Update</button></form>
      <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>"><input type="hidden" name="action" value="remove"><button class="text-button danger-text" type="submit">Remove</button></form>
    </div>
  </div>
</article>
<?php endforeach; ?>
</section>
<aside class="summary-card"><h2>Price details</h2><div class="summary-line"><span>Items (<?= count($items) ?>)</span><span>₹<?= number_format($total,2) ?></span></div><div class="summary-line"><span>Delivery</span><span class="success-text">Free</span></div><div class="summary-total"><span>Total</span><strong>₹<?= number_format($total,2) ?></strong></div><form action="checkout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn btn-primary btn-full" type="submit">Place order</button></form><p class="muted small">This demo places an order without collecting payment.</p></aside>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
