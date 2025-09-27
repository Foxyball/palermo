<?php
function checkAdminLogin(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin(): void {
    if (!checkAdminLogin()) {
        header('Location: login');
        exit;
    }
}

function getCurrentAdmin(PDO $pdo): ?array {
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT admin_id, admin_name, admin_email, active, is_super_admin, last_log_date, last_log_ip, created_at FROM admins WHERE admin_id = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    return $admin !== false ? $admin : null;
}

function isCurrentSuperAdmin(?array $admin): bool {
    if (!$admin) {
        return false;
    }
    return isset($admin['is_super_admin']) && (int)$admin['is_super_admin'] === 1;
}
