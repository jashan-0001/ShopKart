<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$_SESSION['user_id']]); $orders = $stmt->fetchAll();
$pageTitle = 'My orders'; require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">ORDER HISTORY</p><h1>My orders</h1><p class="muted">Your recent purchases and order details.</p></section>
<?php if (!$orders): ?><div class="empty-state"><h2>No orders yet</h2><p>Your placed orders will appear here.</p><a class="btn btn-primary" href="products.php">Shop now</a></div>
<?php else: foreach ($orders as $order): ?>
<section class="panel order-panel"><div class="order-heading"><div><span class="muted small">ORDER #<?= (int)$order['id'] ?></span><h2><?= e($order['created_at']) ?></h2></div><span class="status"><?= e($order['status']) ?></span></div>
<div class="table-wrap"><table><thead><tr><th>Product</th><th>Quantity</th><th>Price</th></tr></thead><tbody>
<?php $detail = $pdo->prepare('SELECT product_name, quantity, price FROM order_items WHERE order_id = ?'); $detail->execute([$order['id']]); foreach ($detail->fetchAll() as $line): ?>
<tr><td><?= e($line['product_name']) ?></td><td><?= (int)$line['quantity'] ?></td><td>₹<?= number_format((float)$line['price'],2) ?></td></tr>
<?php endforeach; ?></tbody></table></div><div class="order-total">Order total: <strong>₹<?= number_format((float)$order['total'],2) ?></strong></div>
</section>
<?php endforeach; endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
