<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('cart.php');
verify_csrf();

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT c.product_id, c.quantity, p.name, p.price, p.stock FROM cart_items c JOIN products p ON p.id=c.product_id WHERE c.user_id=? FOR UPDATE');
    $stmt->execute([$_SESSION['user_id']]); $items = $stmt->fetchAll();
    if (!$items) {
        $pdo->rollBack();
        $_SESSION['flash'] = 'Your cart is empty.';
        redirect('cart.php');
    }
    $total = 0;
    foreach ($items as $item) {
        if ((int)$item['quantity'] > (int)$item['stock']) {
            throw new RuntimeException('There is not enough stock for ' . $item['name'] . '. Please update your cart.');
        }
        $total += (float)$item['price'] * (int)$item['quantity'];
    }
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], $total]);
    $orderId = (int)$pdo->lastInsertId();
    $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)');
    $reduceStock = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
    foreach ($items as $item) {
        $insertItem->execute([$orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity']]);
        $reduceStock->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
        if ($reduceStock->rowCount() !== 1) throw new RuntimeException('Stock changed while placing the order. Please try again.');
    }
    $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $pdo->commit();
    $_SESSION['flash'] = 'Order #' . $orderId . ' placed successfully.';
    redirect('orders.php');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['flash'] = $e instanceof RuntimeException ? $e->getMessage() : 'Could not place your order. Please try again.';
    redirect('cart.php');
}
