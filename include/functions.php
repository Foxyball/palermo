<?php

function convertToEuro(float $priceBgn): float
{
    return round($priceBgn / BGN_TO_EUR_RATE, 2);
}


function is_user_logged_in(): bool
{
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * @param string|null $redirectUrl Optional URL to redirect to different place.
 * 
 */
function require_user_login(?string $redirectUrl = null): void
{
    if (!is_user_logged_in()) {
        $url = $redirectUrl ?? BASE_URL . 'login';
        header('Location: ' . $url);
        exit;
    }
}


function displayPrice(float $priceBgn): string
{
    $priceEur = convertToEuro($priceBgn);
    return sprintf('%.2f лв / %.2f €', $priceBgn, $priceEur);
}

function verifyUserPassword(string $inputPassword, string $storedHash): bool
{
    if (empty($storedHash)) {
        return false;
    }

    if (password_verify($inputPassword, $storedHash)) {
        return true;
    }

    // legacy MD5 check
    if (md5($inputPassword) === $storedHash) {
        return true;
    }

    return false;
}


function calculateAddonTotal(array $addons): float
{
    $total = 0;
    foreach ($addons as $addon) {
        $total += (float)($addon['addon_price'] ?? 0);
    }
    return $total;
}


function calculateEffectiveUnitPrice(float $basePrice, array $addons): float
{
    return $basePrice + calculateAddonTotal($addons);
}


function calculateLineTotal(float $effectiveUnitPrice, int $quantity): float
{
    return $effectiveUnitPrice * $quantity;
}


function formatOrderPrice(float $priceBgn, bool $showBoth = true): string
{
    if (!$showBoth) {
        return sprintf('%.2f лв', $priceBgn);
    }
    return displayPrice($priceBgn);
}


function hasPriceDiscrepancy(float $calculated, float $stored, float $tolerance = 0.01): bool
{
    return abs($calculated - $stored) > $tolerance;
}


function getStatusClass(string $statusName): string
{
    $statusLower = strtolower($statusName);

    if (strpos($statusLower, 'pending') !== false) return 'bg-warning';
    if (strpos($statusLower, 'confirmed') !== false || strpos($statusLower, 'preparing') !== false) return 'bg-info';
    if (strpos($statusLower, 'ready') !== false || strpos($statusLower, 'out for delivery') !== false) return 'bg-primary';
    if (strpos($statusLower, 'delivered') !== false || strpos($statusLower, 'completed') !== false) return 'bg-success';
    if (strpos($statusLower, 'cancelled') !== false || strpos($statusLower, 'canceled') !== false) return 'bg-danger';

    return 'bg-secondary';
}


function renderAddress(array $order): string
{
    if ($order['order_address']) {
        return nl2br(htmlspecialchars($order['order_address']));
    }

    if ($order['address'] || $order['city'] || $order['zip_code']) {
        $parts = [];
        if ($order['address']) $parts[] = htmlspecialchars($order['address']);
        if ($order['city']) $parts[] = htmlspecialchars($order['city']);
        if ($order['zip_code']) $parts[] = htmlspecialchars($order['zip_code']);
        return implode('<br>', $parts);
    }

    return '<span class="text-muted">No address provided</span>';
}
