<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['flash'] = 'Please log in to continue.';
        redirect('login.php');
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Invalid request token. Go back and try again.');
    }
}

function flash_message(): string {
    $message = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return is_string($message) ? $message : '';
}

function cart_count(PDO $pdo): int {
    if (!is_logged_in()) return 0;
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) AS count FROM cart_items WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return (int)$stmt->fetch()['count'];
}
