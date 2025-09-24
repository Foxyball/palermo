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