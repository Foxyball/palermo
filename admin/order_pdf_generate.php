<?php

declare(strict_types=1);

require_once(__DIR__ . '/../include/config.php');
require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../include/functions.php');
require_once(__DIR__ . '/../vendor/autoload.php');

requireAdminLogin();

function fetchOrderData(PDO $pdo, int $orderId): array|false
{
    $stmt = $pdo->prepare('
        SELECT 
            o.id,
            o.user_id,
            o.amount,
            o.message,
            o.order_address,
            o.created_at,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            u.address,
            u.city,
            u.zip_code,
            os.name AS status_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_statuses os ON o.status_id = os.id
        WHERE o.id = ? LIMIT 1
    ');
    $stmt->execute([$orderId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetchOrderItems(PDO $pdo, int $orderId): array
{
    $stmt = $pdo->prepare('
        SELECT 
            oi.id as order_item_id,
            oi.product_id,
            oi.qty as quantity,
            oi.unit_price,
            oi.subtotal,
            p.name as product_name,
            p.description as product_description
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC
    ');
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch addons for each item
    foreach ($items as $key => $item) {
        $addonsStmt = $pdo->prepare('
            SELECT 
                1 as quantity,
                oia.price as unit_price,
                a.name as addon_name
            FROM order_item_addons oia
            LEFT JOIN addons a ON oia.addon_id = a.id
            WHERE oia.order_item_id = ?
            ORDER BY a.name ASC
        ');
        $addonsStmt->execute([$item['order_item_id']]);
        $items[$key]['addons'] = $addonsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $items;
}

function initializePDFLibrary(): array
{
    try {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
        ]);
        return ['library' => 'mpdf', 'instance' => $mpdf];
    } catch (Exception $e) {
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setChroot(__DIR__ . '/..');
        return ['library' => 'dompdf', 'instance' => $dompdf];
    }
}

function loadInvoiceCSS(string $cssFile): string
{
    if (file_exists($cssFile)) {
        return file_get_contents($cssFile);
    }
    return '';
}

function generateInvoiceHeader(array $order): string
{
    return '
    <div class="header">
        <h1>PALERMO RESTAURANT</h1>
        <p>INVOICE</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td width="50%"><strong>Invoice Number:</strong> #' . htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8') . '</td>
                <td width="50%"><strong>Date:</strong> ' . date('F j, Y', strtotime($order['created_at'])) . '</td>
            </tr>
            <tr>
                <td><strong>Time:</strong> ' . date('g:i A', strtotime($order['created_at'])) . '</td>
                <td><strong>Status:</strong> <span class="status-badge">' . htmlspecialchars($order['status_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</span></td>
            </tr>
        </table>
    </div>';
}

function generateCustomerInfo(array $order): string
{
    $html = '<div class="section-title">Customer Information</div>
    <div class="customer-info">
        <table>
            <tr>
                <td width="50%" style="border-right: 2px solid #ddd;">
                    <strong>Bill To:</strong><br><br>';

    if ($order['user_id']) {
        $customerName = trim($order['first_name'] . ' ' . $order['last_name']);
        $html .= '
                    <strong>' . htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') . '</strong><br>
                    ' . htmlspecialchars($order['email'], ENT_QUOTES, 'UTF-8') . '<br>';

        if (!empty($order['phone'])) {
            $html .= 'Phone: ' . htmlspecialchars($order['phone'], ENT_QUOTES, 'UTF-8') . '<br>';
        }

        if (!empty($order['address']) || !empty($order['city']) || !empty($order['zip_code'])) {
            $html .= '<br><strong>Address:</strong><br>';
            if (!empty($order['address'])) {
                $html .= htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8') . '<br>';
            }
            if (!empty($order['city']) || !empty($order['zip_code'])) {
                $html .= htmlspecialchars(trim($order['city'] . ', ' . $order['zip_code']), ENT_QUOTES, 'UTF-8') . '<br>';
            }
        }
    } else {
        $html .= '<strong>Guest Customer</strong><br>';
    }

    $html .= '
                </td>
                <td width="50%" style="padding-left: 15px;">
                    <strong>Delivery To:</strong><br><br>';

    if (!empty($order['order_address'])) {
        $html .= nl2br(htmlspecialchars($order['order_address'], ENT_QUOTES, 'UTF-8'));
    } else {
        $html .= '<em>No specific delivery address provided</em>';
    }

    if (!empty($order['message'])) {
        $html .= '<br><br><strong>Special Instructions:</strong><br>' . nl2br(htmlspecialchars($order['message'], ENT_QUOTES, 'UTF-8'));
    }

    $html .= '
                </td>
            </tr>
        </table>
    </div>';

    return $html;
}

function generateOrderItemsTable(array $orderItems): array
{
    $html = '<div class="section-title">Order Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="45%">Description</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Unit Price</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>';

    $grandTotal = 0;

    foreach ($orderItems as $item) {
        // Calculate item total: (unit_price * quantity) + addons
        $baseItemTotal = $item['unit_price'] * $item['quantity'];

        // Calculate addon total
        $addonTotal = 0;
        foreach ($item['addons'] as $addon) {
            $addonTotal += $addon['unit_price'];
        }

        $itemTotal = $baseItemTotal + $addonTotal;
        $grandTotal += $itemTotal;

        $html .= '
            <tr>
                <td>
                    <strong>' . htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') . '</strong>';

        if (!empty($item['product_description'])) {
            $html .= '<br><small style="color: #666;">' . htmlspecialchars($item['product_description'], ENT_QUOTES, 'UTF-8') . '</small>';
        }

        // Add addons
        foreach ($item['addons'] as $addon) {
            $html .= '<br><div class="addon-item">+ ' . htmlspecialchars($addon['addon_name'], ENT_QUOTES, 'UTF-8') . ' — ' . displayPrice($addon['unit_price']) . '</div>';
        }

        $html .= '
                </td>
                <td class="text-center">' . $item['quantity'] . '</td>
                <td class="text-right">' . displayPrice($item['unit_price']) . '</td>
                <td class="text-right"><strong>' . displayPrice($itemTotal) . '</strong></td>
            </tr>';
    }

    $html .= '
        </tbody>
    </table>';

    return ['html' => $html, 'grandTotal' => $grandTotal];
}

function generateTotalsAndFooter(float $grandTotal): string
{
    return '
    <div class="clearfix">
        <div class="totals">
            <table>
                <tr class="total-row">
                    <td><strong>Total Amount:</strong></td>
                    <td class="text-right"><strong>' . displayPrice($grandTotal) . '</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <p><strong>Thank you for choosing Palermo Restaurant!</strong></p>
        <p>This invoice was generated on ' . date('F j, Y \a\t g:i A') . '</p>
    </div>';
}

function generateInvoiceHTML(array $order, array $orderItems, string $cssContent): string
{
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #' . $order['id'] . '</title>
    <style>' . $cssContent . '</style>
</head>
<body>';

    $html .= generateInvoiceHeader($order);
    $html .= generateCustomerInfo($order);

    $itemsData = generateOrderItemsTable($orderItems);
    $html .= $itemsData['html'];
    $html .= generateTotalsAndFooter($itemsData['grandTotal']);

    $html .= '
</body>
</html>';

    return $html;
}

function outputPDF(string $library, object $pdfInstance, string $html, int $orderId): void
{
    $filename = 'Invoice_' . $orderId . '_' . date('Y-m-d') . '.pdf';

    if ($library === 'dompdf') {
        $pdfInstance->loadHtml($html);
        $pdfInstance->render();
        $pdfInstance->stream($filename, array("Attachment" => false));
    } else {
        $pdfInstance->SetTitle('Invoice #' . $orderId . ' - Palermo Restaurant');
        $pdfInstance->SetAuthor('Palermo Restaurant');
        $pdfInstance->WriteHTML($html);
        $pdfInstance->Output($filename, 'I'); // 'I' = inline display
    }
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid order ID';
    header('Location: order_list');
    exit;
}

// Fetch order data
$order = fetchOrderData($pdo, $id);

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: order_list');
    exit;
}

// Fetch order items with addons
$orderItems = fetchOrderItems($pdo, $id);

// Initialize PDF library
$pdfConfig = initializePDFLibrary();

// Load CSS from external file
$cssFile = __DIR__ . '/css/invoice.css';
$cssContent = loadInvoiceCSS($cssFile);

// Generate complete invoice HTML
$html = generateInvoiceHTML($order, $orderItems, $cssContent);

try {
    outputPDF($pdfConfig['library'], $pdfConfig['instance'], $html, $id);
} catch (Exception $e) {
    $_SESSION['error'] = 'PDF generation failed: ' . $e->getMessage();
    header('Location: order_show.php?id=' . $id);
    exit;
}
