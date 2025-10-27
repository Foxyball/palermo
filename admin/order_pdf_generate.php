<?php

require_once(__DIR__ . '/../include/config.php');
require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../include/functions.php');
require_once(__DIR__ . '/../vendor/autoload.php');

requireAdminLogin();

$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid order ID';
    header('Location: order_list');
    exit;
}

$orderStmt = $pdo->prepare('
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
$orderStmt->execute([$id]);
$order = $orderStmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: order_list');
    exit;
}

$itemsStmt = $pdo->prepare('
    SELECT 
        oi.id as order_item_id,
        oi.product_id,
        oi.qty as quantity,
        oi.unit_price,
        oi.subtotal,
        p.name as product_name,
        p.short_description as product_description
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
');
$itemsStmt->execute([$id]);
$orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($orderItems as $key => $item) {
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
    $orderItems[$key]['addons'] = $addonsStmt->fetchAll(PDO::FETCH_ASSOC);
}

$useDomPDF = false;

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
} catch (Exception $e) {
    $useDomPDF = true;
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->getOptions()->setChroot(__DIR__ . '/..');
}

// Build HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #' . $order['id'] . '</title>
    <style>
        @page { margin: 20mm; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333; 
            padding-bottom: 15px; 
        }
        .header h1 { 
            color: #333; 
            margin: 0 0 5px 0; 
            font-size: 24px;
        }
        .header p { 
            margin: 0; 
            font-size: 16px; 
            color: #666; 
        }
        .invoice-details { 
            margin-bottom: 25px; 
        }
        .invoice-details table { 
            width: 100%; 
            border-collapse: collapse;
        }
        .invoice-details td { 
            padding: 8px 0; 
            vertical-align: top;
        }
        .section-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #333; 
            margin: 20px 0 10px 0; 
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .customer-info { 
            margin-bottom: 25px; 
        }
        .customer-info table { 
            width: 100%; 
            border-collapse: collapse;
        }
        .customer-info td { 
            padding: 5px 10px; 
            vertical-align: top; 
            border: 1px solid #ddd;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
        }
        .items-table th, .items-table td { 
            border: 1px solid #ddd; 
            padding: 10px 8px; 
            text-align: left; 
        }
        .items-table th { 
            background-color: #f8f9fa; 
            font-weight: bold; 
            text-align: center;
        }
        .items-table .text-right { 
            text-align: right; 
        }
        .items-table .text-center { 
            text-align: center; 
        }
        .addon-item { 
            font-style: italic; 
            color: #666; 
            font-size: 11px;
            margin-left: 10px;
        }
        .totals { 
            margin-top: 30px; 
        }
        .totals table { 
            width: 100%; 
            margin-left: auto;
            margin-right: 0;
            width: 300px;
            float: right;
        }
        .totals td { 
            padding: 8px 10px; 
            border: 1px solid #ddd;
        }
        .total-row { 
            font-weight: bold; 
            font-size: 14px; 
            background-color: #f8f9fa;
        }
        .status-badge { 
            display: inline-block; 
            padding: 4px 8px; 
            border-radius: 4px; 
            background-color: #e9ecef; 
            border: 1px solid #adb5bd; 
            font-size: 11px;
            font-weight: bold;
        }
        .footer { 
            margin-top: 40px; 
            text-align: center; 
            font-size: 10px; 
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PALERMO RESTAURANT</h1>
        <p>INVOICE</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td width="50%"><strong>Invoice Number:</strong> #' . htmlspecialchars($order['id']) . '</td>
                <td width="50%"><strong>Date:</strong> ' . date('F j, Y', strtotime($order['created_at'])) . '</td>
            </tr>
            <tr>
                <td><strong>Time:</strong> ' . date('g:i A', strtotime($order['created_at'])) . '</td>
                <td><strong>Status:</strong> <span class="status-badge">' . htmlspecialchars($order['status_name'] ?? 'Unknown') . '</span></td>
            </tr>
        </table>
    </div>

    <div class="section-title">Customer Information</div>
    <div class="customer-info">
        <table>
            <tr>
                <td width="50%" style="border-right: 2px solid #ddd;">
                    <strong>Bill To:</strong><br><br>';

if ($order['user_id']) {
    $customerName = trim($order['first_name'] . ' ' . $order['last_name']);
    $html .= '
                    <strong>' . htmlspecialchars($customerName) . '</strong><br>
                    ' . htmlspecialchars($order['email']) . '<br>';

    if (!empty($order['phone'])) {
        $html .= 'Phone: ' . htmlspecialchars($order['phone']) . '<br>';
    }

    if (!empty($order['address']) || !empty($order['city']) || !empty($order['zip_code'])) {
        $html .= '<br><strong>Address:</strong><br>';
        if (!empty($order['address'])) {
            $html .= htmlspecialchars($order['address']) . '<br>';
        }
        if (!empty($order['city']) || !empty($order['zip_code'])) {
            $html .= htmlspecialchars(trim($order['city'] . ', ' . $order['zip_code'])) . '<br>';
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
    $html .= nl2br(htmlspecialchars($order['order_address']));
} else {
    $html .= '<em>No specific delivery address provided</em>';
}

if (!empty($order['message'])) {
    $html .= '<br><br><strong>Special Instructions:</strong><br>' . nl2br(htmlspecialchars($order['message']));
}

$html .= '
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Order Items</div>
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
                    <strong>' . htmlspecialchars($item['product_name']) . '</strong>';

    if (!empty($item['product_description'])) {
        $html .= '<br><small style="color: #666;">' . htmlspecialchars($item['product_description']) . '</small>';
    }

    // Add addons
    foreach ($item['addons'] as $addon) {
        $html .= '<br><div class="addon-item">+ ' . htmlspecialchars($addon['addon_name']) . ' — ' . displayPrice($addon['unit_price']) . '</div>';
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
    </table>

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
    </div>

</body>
</html>';

// Generate PDF
try {
    if ($useDomPDF) {
        // Use DomPDF
        $dompdf->loadHtml($html);
        $dompdf->render();
        $filename = 'Invoice_' . $order['id'] . '_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, array("Attachment" => false));
    } else {
        // Use mPDF
        $mpdf->SetTitle('Invoice #' . $order['id'] . ' - Palermo Restaurant');
        $mpdf->SetAuthor('Palermo Restaurant');
        $mpdf->WriteHTML($html);
        $filename = 'Invoice_' . $order['id'] . '_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'I'); // 'I' = inline display
    }
} catch (Exception $e) {
    // If all PDF generation fails, show error
    $_SESSION['error'] = 'PDF generation failed: ' . $e->getMessage();
    header('Location: order_show.php?id=' . $id);
    exit;
}
