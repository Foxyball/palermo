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

/**
 * Build a URL for paginated admin list (or similar) preserving existing query params.
 * Only keeps the search term 'q' and sets 'page' when > 1 to keep URL tidy.
 *
 * @param int $page Target page number (1-based)
 * @param string|null $base Base script path (defaults to 'admin_list')
 * @return string URL with query string
 */
function buildPageUrl(int $page, ?string $base = null): string {
    $base = $base ?: 'admin_list';
    $query = [];
    if (isset($_GET['q']) && $_GET['q'] !== '') {
        $query['q'] = $_GET['q'];
    }
    if ($page > 1) {
        $query['page'] = $page;
    }
    $qs = http_build_query($query);
    return $base . ($qs ? ('?' . $qs) : '');
}
