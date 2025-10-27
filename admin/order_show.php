<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../include/functions.php');
include(__DIR__ . '/include/html_functions.php');

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
        oi.unit_price,
        oi.qty,
        oi.subtotal,
        p.name as product_name
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
');
$itemsStmt->execute([$id]);
$orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

$addonsData = [];
if (!empty($orderItems)) {
    $orderItemIds = array_column($orderItems, 'order_item_id');
    $placeholders = str_repeat('?,', count($orderItemIds) - 1) . '?';

    $addonsStmt = $pdo->prepare("
        SELECT 
            oia.order_item_id,
            oia.price as addon_price,
            a.name as addon_name
        FROM order_item_addons oia
        LEFT JOIN addons a ON oia.addon_id = a.id
        WHERE oia.order_item_id IN ($placeholders)
        ORDER BY oia.order_item_id, a.name
    ");
    $addonsStmt->execute($orderItemIds);
    $addons = $addonsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Group addons by order_item_id
    foreach ($addons as $addon) {
        $addonsData[$addon['order_item_id']][] = $addon;
    }
}

?>

<?php
headerContainer();
?>

<title>Order #<?php echo $order['id']; ?> | <?php echo SITE_TITLE; ?></title>

</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php navbarContainer(); ?>

        <?php sidebarContainer(); ?>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Order Details</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="order_list">Orders</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Order #<?php echo $order['id']; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Order #<?php echo $order['id']; ?></h4>
                                    <div class="d-flex gap-2">
                                        <?php
                                        $statusClass = '';
                                        $statusName = $order['status_name'] ?? 'Unknown';
                                        $statusLower = strtolower($statusName);
                                        if (strpos($statusLower, 'pending') !== false) {
                                            $statusClass = 'bg-warning';
                                        } elseif (strpos($statusLower, 'confirmed') !== false || strpos($statusLower, 'preparing') !== false) {
                                            $statusClass = 'bg-info';
                                        } elseif (strpos($statusLower, 'ready') !== false || strpos($statusLower, 'out for delivery') !== false) {
                                            $statusClass = 'bg-primary';
                                        } elseif (strpos($statusLower, 'delivered') !== false || strpos($statusLower, 'completed') !== false) {
                                            $statusClass = 'bg-success';
                                        } elseif (strpos($statusLower, 'cancelled') !== false || strpos($statusLower, 'canceled') !== false) {
                                            $statusClass = 'bg-danger';
                                        } else {
                                            $statusClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?> text-white fs-6 px-3 py-2">
                                            <?php echo htmlspecialchars($statusName); ?>
                                        </span>
                                        <a href="order_pdf_generate.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn btn-danger">
                                            <i class="bi bi-file-earmark-pdf"></i> Generate PDF Invoice
                                        </a>
                                        <a href="order_list" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left"></i> Back to Orders
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Customer Information -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-person-circle"></i> Billed To
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($order['user_id']) { ?>
                                                <div class="mb-2">
                                                    <strong>Name:</strong>
                                                    <?php echo htmlspecialchars(trim($order['first_name'] . ' ' . $order['last_name'])); ?>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Email:</strong>
                                                    <?php echo htmlspecialchars($order['email']); ?>
                                                </div>
                                                <?php if ($order['phone']) { ?>
                                                    <div class="mb-2">
                                                        <strong>Phone:</strong>
                                                        <?php echo htmlspecialchars($order['phone']); ?>
                                                    </div>
                                                <?php } ?>
                                                <div class="mb-0">
                                                    <strong>Address:</strong>
                                                    <div class="mt-1">
                                                        <?php if ($order['order_address']) { ?>
                                                            <?php echo nl2br(htmlspecialchars($order['order_address'])); ?>
                                                        <?php } elseif ($order['address'] || $order['city'] || $order['zip_code']) { ?>
                                                            <?php echo htmlspecialchars($order['address']); ?>
                                                            <?php if ($order['city']) { ?>
                                                                <br><?php echo htmlspecialchars($order['city']); ?>
                                                            <?php } ?>
                                                            <?php if ($order['zip_code']) { ?>
                                                                <?php echo htmlspecialchars($order['zip_code']); ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <span class="text-muted">No address provided</span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-muted">
                                                    <i class="bi bi-person-x"></i> Guest Customer
                                                    <p class="mb-0 mt-2">No customer account associated with this order.</p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-info-circle"></i> Order Information
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Status:</strong>
                                                <span class="badge <?php echo $statusClass; ?> text-white">
                                                    <?php echo htmlspecialchars($statusName); ?>
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Order Date:</strong>
                                                <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                                            </div>
                                            <div class="mb-0">
                                                <strong>Total Amount:</strong>
                                                <span class="text-success fw-bold fs-5"><?php echo formatOrderPrice($order['amount']); ?></span>
                                            </div>
                                            <?php if ($order['message']) { ?>
                                                <hr>
                                                <div class="mb-0">
                                                    <strong>Order Notes:</strong>
                                                    <div class="mt-1 p-2 bg-light rounded">
                                                        <?php echo nl2br(htmlspecialchars($order['message'])); ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-basket"></i> Order Items
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Addons</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($orderItems)) { ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">
                                                            <i class="bi bi-basket-x fs-1"></i>
                                                            <p class="mt-2 mb-0">No items found for this order</p>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php $grandTotal = 0; ?>
                                                    <?php foreach ($orderItems as $item) { ?>
                                                        <?php
                                                        $itemAddons = $addonsData[$item['order_item_id']] ?? [];
                                                        
                                                        $addonTotal = calculateAddonTotal($itemAddons);
                                                        $effectiveUnitPrice = calculateEffectiveUnitPrice($item['unit_price'], $itemAddons);
                                                        $calculatedSubtotal = calculateLineTotal($effectiveUnitPrice, $item['qty']);
                                                        
                                                        $lineTotal = $item['subtotal'] ?: $calculatedSubtotal;
                                                        
                                                        // Check for mismatches
                                                        $hasMismatch = hasPriceDiscrepancy($calculatedSubtotal, $lineTotal);
                                                        ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong><?php echo htmlspecialchars($item['product_name'] ?: 'Unknown Product'); ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <?php if (!empty($itemAddons)) { ?>
                                                                    <div class="small">
                                                                        <?php foreach ($itemAddons as $addon) { ?>
                                                                            <span class="badge bg-light text-dark me-1 mb-1">
                                                                                <?php echo htmlspecialchars($addon['addon_name']); ?>
                                                                                <?php if ($addon['addon_price'] > 0) { ?>
                                                                                    (+<?php echo formatOrderPrice($addon['addon_price'], false); ?>)
                                                                                <?php } ?>
                                                                            </span>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <span class="text-muted">—</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <?php if ($addonTotal > 0) { ?>
                                                                    <div class="small text-muted">Base: <?php echo formatOrderPrice($item['unit_price'], false); ?></div>
                                                                    <div class="small text-muted">Addons: +<?php echo formatOrderPrice($addonTotal, false); ?></div>
                                                                    <strong><?php echo formatOrderPrice($effectiveUnitPrice); ?></strong>
                                                                <?php } else { ?>
                                                                    <?php echo formatOrderPrice($item['unit_price']); ?>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <span class="badge bg-primary"><?php echo $item['qty']; ?></span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <strong><?php echo formatOrderPrice($lineTotal); ?></strong>
                                                                <?php if ($hasMismatch) { ?>
                                                                    <div class="small text-warning">
                                                                        <i class="bi bi-exclamation-triangle"></i>
                                                                        Calc: <?php echo formatOrderPrice($calculatedSubtotal, false); ?>
                                                                    </div>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <?php $grandTotal += $lineTotal; ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                            <?php if (!empty($orderItems)) { ?>
                                                <tfoot>
                                                    <tr class="table-secondary">
                                                        <th colspan="4" class="text-end">Total Price:</th>
                                                        <th class="text-end">
                                                            <span class="fs-5 text-success"><?php echo formatOrderPrice($grandTotal); ?></span>
                                                            <?php if (hasPriceDiscrepancy($grandTotal, $order['amount'])) { ?>
                                                                <div class="small text-warning">
                                                                    <i class="bi bi-exclamation-triangle"></i>
                                                                    Order Total: <?php echo formatOrderPrice($order['amount']); ?>
                                                                </div>
                                                            <?php } ?>
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
        }

        .card-title {
            color: #495057;
        }

        .badge {
            font-size: 0.75rem;
        }

        .table-secondary th {
            background-color: #e9ecef !important;
            border-color: #dee2e6 !important;
        }


    </style>

</body>

</html>