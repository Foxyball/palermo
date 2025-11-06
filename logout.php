<?php

require_once(__DIR__ . '/include/connect.php');

if (isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, remember_expires = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {
    }
}

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header('Location: index');
exit;
