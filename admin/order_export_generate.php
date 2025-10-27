<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../include/functions.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

requireAdminLogin();

$search = $_GET['q'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$whereSql = '';
$params = [];

if ($statusFilter === 'pending') {
    $pendingStatusStmt = $pdo->prepare('SELECT id FROM order_statuses WHERE LOWER(name) = "pending" LIMIT 1');
    $pendingStatusStmt->execute();
    $pendingStatus = $pendingStatusStmt->fetch(PDO::FETCH_ASSOC);

    if ($pendingStatus) {
        $whereSql = ' WHERE o.status_id = :pending_status_id';
        $params[':pending_status_id'] = $pendingStatus['id'];
    }
}

if ($search !== '') {
    if ($whereSql !== '') {
        $whereSql .= ' AND (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
    } else {
        $whereSql = ' WHERE (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
    }
    $params[':keyword'] = '%' . $search . '%';
    $params[':id'] = $search;
}

$dataSql = 'SELECT 
            o.id, 
            o.user_id,
            o.amount,
            o.status_id,
            o.created_at,
            CONCAT(u.first_name, " ", u.last_name) AS customer_name,
            u.email AS customer_email,
            u.phone AS customer_phone,
            os.name AS status_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_statuses os ON o.status_id = os.id '
    . $whereSql . ' ORDER BY o.id DESC';

$stmt = $pdo->prepare($dataSql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$spreadsheet->getProperties()
    ->setCreator('Palermo Restaurant')
    ->setTitle('Orders Export')
    ->setSubject('Orders List')
    ->setDescription('Exported orders from Palermo Restaurant');

$headers = ['Order ID', 'Customer Name', 'Email', 'Phone', 'Amount (BGN)', 'Amount (EUR)', 'Status', 'Order Date', 'Order Time'];
$sheet->fromArray($headers, NULL, 'A1');

$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(12);

// Add data rows
$row = 2;
foreach ($orders as $order) {
    $customerName = trim($order['customer_name']) !== '' ? $order['customer_name'] : 'Guest Customer';
    $amountBGN = $order['amount'];
    $amountEUR = convertToEuro($amountBGN);

    $sheet->setCellValue('A' . $row, $order['id']);
    $sheet->setCellValue('B' . $row, $customerName);
    $sheet->setCellValue('C' . $row, $order['customer_email'] ?? '');
    $sheet->setCellValue('D' . $row, $order['customer_phone'] ?? '');
    $sheet->setCellValue('E' . $row, number_format($amountBGN, 2));
    $sheet->setCellValue('F' . $row, number_format($amountEUR, 2));
    $sheet->setCellValue('G' . $row, $order['status_name'] ?? 'Unknown');
    $sheet->setCellValue('H' . $row, date('Y-m-d', strtotime($order['created_at'])));
    $sheet->setCellValue('I' . $row, date('H:i:s', strtotime($order['created_at'])));

    // Add borders to data cells
    $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ]
    ]);

    if ($row % 2 == 0) {
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2']
            ]
        ]);
    }

    // Align numeric columns to the right
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('H' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $row++;
}

// Add summary row
$totalOrders = count($orders);
$totalAmount = array_sum(array_column($orders, 'amount'));
$totalAmountEUR = convertToEuro($totalAmount);

$summaryRow = $row + 1;
$sheet->setCellValue('A' . $summaryRow, 'TOTAL');
$sheet->setCellValue('B' . $summaryRow, $totalOrders . ' orders');
$sheet->setCellValue('E' . $summaryRow, number_format($totalAmount, 2));
$sheet->setCellValue('F' . $summaryRow, number_format($totalAmountEUR, 2));

$sheet->getStyle('A' . $summaryRow . ':I' . $summaryRow)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 11
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E7E6E6']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_MEDIUM,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

$sheet->getStyle('E' . $summaryRow . ':F' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->setTitle('Orders');

$writer = new Xlsx($spreadsheet);

$filenamePrefix = $statusFilter === 'pending' ? 'pending_orders' : 'orders';
$filename = $filenamePrefix . '_export_' . date('Y-m-d_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
