<?php
require_once(__DIR__ . '/include/config.php');
require_once(__DIR__ . '/include/connect.php');

$stmt = $pdo->prepare("SELECT * FROM admins");
$stmt->execute();
$admins = $stmt->fetchAll();

foreach ($admins as $admin) {
    echo "Admin: " . ($admin['admin_name']) . "<br>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title><?php echo SITE_TITLE; ?></title>
</head>

<body>

</body>

</html>