<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/UserRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$userId = $_GET['id'] ?? 0;
if ($userId <= 0) {
    $_SESSION['error'] = 'Invalid user selected.';
    header('Location: user_list');
    exit;
}

$userRepository = new UserRepository($pdo);
$userToEdit = $userRepository->findById($userId);

if (!$userToEdit) {
    $_SESSION['error'] = 'User not found.';
    header('Location: user_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? null);
    $lastName = trim($_POST['last_name'] ?? null);
    $address = trim($_POST['address'] ?? null);
    $city = trim($_POST['city'] ?? null);
    $phone = trim($_POST['phone'] ?? null);
    $zipCode = trim($_POST['zip_code'] ?? null);
    $email = trim($_POST['email'] ?? null);
    $password = $_POST['password'] ?? null;

    // Validation
    if ($firstName === '') {
        $errors[] = 'First name is required';
    }
    if ($lastName === '') {
        $errors[] = 'Last name is required';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    if ($email !== $userToEdit['email']) {
        if ($userRepository->emailExists($email, $userId)) {
            $errors[] = 'Email already in use by another user';
        }
    }

    $passwordHash = null;
    if ($password !== '') {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } else {
            $passwordHash = md5($password);
        }
    }

    if (empty($errors)) {
        try {
            $userRepository->update($userId, $firstName, $lastName, $email, $passwordHash, $address, $city, $phone, $zipCode);
            $_SESSION['success'] = 'User updated successfully';
            header('Location: user_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an unexpected error. Please try again.';
        }
    }
} else {
    $firstName = $userToEdit['first_name'];
    $lastName = $userToEdit['last_name'];
    $email = $userToEdit['email'];
    $address = $userToEdit['address'] ?? '';
    $city = $userToEdit['city'] ?? '';
    $phone = $userToEdit['phone'] ?? '';
    $zipCode = $userToEdit['zip_code'] ?? '';
}

headerContainer();
?>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php navbarContainer(); ?>
        <?php sidebarContainer(); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Edit User <?php echo ($userToEdit['first_name']); ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="user_list">User List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Update User</h3>
                                    <span class="badge text-bg-<?php echo ($userToEdit['active'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($userToEdit['active'] == '1') ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($errors)) { ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $err) { ?>
                                                    <li><?php echo htmlspecialchars($err); ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                    <form method="POST" novalidate>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">First Name *</label>
                                                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($firstName); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Last Name *</label>
                                                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($lastName); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email *</label>
                                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">New Password <small class="text-muted">(leave blank to keep unchanged)</small></label>
                                                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                    <small class="text-muted">Min 6 characters if changing</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Phone</label>
                                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Zip Code</label>
                                                    <input type="text" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($zipCode); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="user_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    Created: <?php echo date('M j, Y g:i A', strtotime($userToEdit['created_at'])); ?>
                                    | Last Updated: <?php echo date('M j, Y g:i A', strtotime($userToEdit['updated_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php footerContainer(); ?>
    </div>

</body>

</html>
<?php
