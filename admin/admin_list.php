<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
require_once(__DIR__ . '/../repositories/admin/AdminRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$currentAdmin = getCurrentAdmin($pdo);
$currentAdminID = $currentAdmin['admin_id'] ?? null;
$isCurrentSuperAdmin = isCurrentSuperAdmin($currentAdmin);

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

// Use repository
$adminRepository = new AdminRepository($pdo);
$totalAdminsCount = $adminRepository->countAll($search);

$paginator = new Paginator($totalAdminsCount, $page, $perPage);
$admins = $adminRepository->findAll($search, $paginator->limit(), $paginator->offset());




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
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <h3 class="card-title mb-0">All Administrators</h3>
                                        <form class="d-flex" method="get" action="admin_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control" placeholder="Search name, email, id" value="<?php echo $search; ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="admin_list" title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>
                                    <?php if ($isCurrentSuperAdmin) { ?>
                                        <div class="card-tools ms-md-auto">
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
                                                                    <input class="form-check-input js-admin-status-toggle" type="checkbox"
                                                                        data-admin-id="<?php echo $admin['admin_id']; ?>"
                                                                        <?php echo $admin['active'] == '1' ? 'checked' : ''; ?>
                                                                        <?php
                                                                        if ($admin['admin_id'] == $currentAdminID || !$isCurrentSuperAdmin) {
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
                                                                    <?php if ($isCurrentSuperAdmin && $admin['admin_id'] != $currentAdminID) { ?>
                                                                        <a href="admin_edit?id=<?php echo $admin['admin_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </a>
                                                                    <?php } ?>

                                                                    <?php
                                                                    if ($admin['admin_id'] == $currentAdminID && !$isCurrentSuperAdmin) {
                                                                    ?>
                                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                                            <i class="bi bi-shield-lock"></i>
                                                                        </button>
                                                                    <?php
                                                                    } else if ($isCurrentSuperAdmin && $admin['admin_id'] != $currentAdminID) {
                                                                    ?>
                                                                        <button type="button" class="btn btn-sm btn-outline-danger js-admin-delete-btn"
                                                                            data-admin-id="<?php echo $admin['admin_id']; ?>"
                                                                            data-admin-name="<?php echo $admin['admin_name']; ?>">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    <?php
                                                                    } else if ($admin['admin_id'] != $currentAdminID) {
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
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if (!empty($admins)) { ?>
                                    <?php renderPagination($paginator, $totalAdminsCount, count($admins), 'admin_list', $search); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <script src="js/palermoAdminCrud.js"></script>
</body>

</html>