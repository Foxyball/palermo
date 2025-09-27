<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$current_admin = getCurrentAdmin($pdo);
$current_admin_id = $current_admin['admin_id'] ?? null;
$is_current_super_admin = isCurrentSuperAdmin($current_admin);

$stmt = $pdo->prepare("SELECT admin_id, admin_name, admin_email, active, is_super_admin, last_log_date, last_log_ip, created_at FROM admins ORDER BY admin_id DESC");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<?php
headerContainer();
?>
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
                            <h3 class="mb-0">Admin Management</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Admin List</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">All Administrators</h3>
                                    <?php if ($is_current_super_admin) { ?>
                                        <div class="card-tools">
                                            <a href="admin_add" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus"></i> Add New Admin
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                    <th>Last Login</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($admins)) { ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="bi bi-person-x-fill fs-1"></i>
                                                                <p class="mt-2">No administrators found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php foreach ($admins as $admin) { ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong>#<?php echo $admin['admin_id']; ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                                        <?php echo strtoupper(substr($admin['admin_name'], 0, 1)); ?>
                                                                    </div>
                                                                    <strong><?php echo $admin['admin_name']; ?></strong>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <a href="mailto:<?php echo $admin['admin_email']; ?>" class="text-decoration-none">
                                                                    <?php echo $admin['admin_email']; ?>
                                                                </a>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input admin-status-toggle" type="checkbox"
                                                                        data-admin-id="<?php echo $admin['admin_id']; ?>"
                                                                        <?php echo $admin['active'] == '1' ? 'checked' : ''; ?>
                                                                        <?php
                                                                        if ($admin['admin_id'] == $current_admin_id || !$is_current_super_admin) {
                                                                            echo 'disabled';
                                                                        }
                                                                        ?>>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <?php if ($admin['last_log_date']) { ?>
                                                                    <small class="text-muted">
                                                                        <?php echo date('M j, Y g:i A', strtotime($admin['last_log_date'])); ?>
                                                                    </small>
                                                                <?php } else { ?>
                                                                    <small class="text-muted">Never</small>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="align-middle">
                                                                <small class="text-muted">
                                                                    <?php echo date('M j, Y', strtotime($admin['created_at'])); ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <?php if ($is_current_super_admin || $admin['admin_id'] == $current_admin_id) { ?>
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                    <?php } ?>

                                                                    <?php
                                                                    if ($admin['admin_id'] == $current_admin_id && !$is_current_super_admin) {
                                                                    ?>
                                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                                            <i class="bi bi-shield-lock"></i>
                                                                        </button>
                                                                    <?php
                                                                    } else if ($is_current_super_admin && $admin['admin_id'] != $current_admin_id) {
                                                                    ?>
                                                                        <button type="button" class="btn btn-sm btn-outline-danger admin-delete-btn"
                                                                            data-admin-id="<?php echo $admin['admin_id']; ?>"
                                                                            data-admin-name="<?php echo $admin['admin_name']; ?>">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    <?php
                                                                    } else if ($admin['admin_id'] != $current_admin_id) {
                                                                    ?>
                                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                                            <i class="bi bi-lock"></i>
                                                                        </button>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php }; ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if (!empty($admins)) { ?>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col-sm-6">
                                                <small class="text-muted">
                                                    Showing <?php echo count($admins); ?> administrator<?php echo count($admins) !== 1 ? 's' : ''; ?>
                                                </small>
                                            </div>
                                            <div class="col-sm-6 text-end">
                                                <small class="text-muted">
                                                    Last updated: <?php echo date('M j, Y g:i A'); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <style>
        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .small-box {
            border-radius: 0.375rem;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        .small-box .inner {
            position: relative;
            z-index: 2;
        }

        .small-box .inner h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .small-box .inner p {
            margin-bottom: 0;
            opacity: 0.8;
        }

        .small-box-icon {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.3;
        }

        .form-switch .form-check-input {
            cursor: pointer;
        }

        .form-switch .form-check-input:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .form-switch .form-check-input {
            cursor: pointer;
        }

        .form-switch .form-check-input:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
    </style>



</body>

</html>