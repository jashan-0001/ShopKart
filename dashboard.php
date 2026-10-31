<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$stmt = $pdo->prepare('SELECT COUNT(*) AS order_count, COALESCE(SUM(total),0) AS spent FROM orders WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]); $stats = $stmt->fetch();
$stmt = $pdo->prepare('SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 5');
$stmt->execute([$_SESSION['user_id']]); $orders = $stmt->fetchAll();
$pageTitle = 'My dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">YOUR ACCOUNT</p><h1>Hello, <?= e($_SESSION['user_name'] ?? 'Shopper') ?> 👋</h1><p class="muted">Your shopping activity, all in one place.</p></section>
<div class="stats-grid">
  <div class="stat-card"><span>Cart items</span><strong><?= cart_count($pdo) ?></strong><a href="cart.php">Open cart →</a></div>
  <div class="stat-card"><span>Orders placed</span><strong><?= (int)$stats['order_count'] ?></strong><a href="orders.php">View orders →</a></div>
  <div class="stat-card"><span>Total order value</span><strong>₹<?= number_format((float)$stats['spent'], 2) ?></strong><span class="muted">Across all orders</span></div>
</div>
<section class="panel">
  <div class="section-head"><h2>Recent orders</h2><a href="orders.php" class="text-link">All orders →</a></div>
  <?php if (!$orders): ?><p>You haven't placed an order yet. <a href="products.php">Explore products</a>.</p>
  <?php else: ?>
  <div class="table-wrap"><table><thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Total</th></tr></thead><tbody>
  <?php foreach ($orders as $order): ?><tr><td>#<?= (int)$order['id'] ?></td><td><?= e($order['created_at']) ?></td><td><span class="status"><?= e($order['status']) ?></span></td><td>₹<?= number_format((float)$order['total'],2) ?></td></tr><?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
