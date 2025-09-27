<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$current_admin = getCurrentAdmin($pdo);
$current_admin_id = $current_admin['admin_id'] ?? null;
$is_current_super_admin = isCurrentSuperAdmin($current_admin);

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

$whereSql = '';
$params = [];
if ($search !== '') {
    $whereSql = ' WHERE (admin_name LIKE :keyword OR admin_email LIKE :keyword)';
    $params[':keyword'] = '%' . $search . '%';
}

$countSql = 'SELECT COUNT(*) FROM admins' . $whereSql;
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$total_admins_count = $stmtCount->fetchColumn();

$paginator = new Paginator($total_admins_count, $page, $perPage);

$dataSql = 'SELECT admin_id, admin_name, admin_email, active, is_super_admin, last_log_date, last_log_ip, created_at
            FROM admins' . $whereSql . ' ORDER BY admin_id DESC LIMIT :lim OFFSET :off';

$stmt = $pdo->prepare($dataSql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}

$stmt->bindValue(':lim', $paginator->limit(), PDO::PARAM_INT);
$stmt->bindValue(':off', $paginator->offset(), PDO::PARAM_INT);
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
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <h3 class="card-title mb-0">All Administrators</h3>
                                        <form class="d-flex" method="get" action="admin_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control" placeholder="Search name, email, id" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" />
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
                                    <?php if ($is_current_super_admin) { ?>
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
                                                                        <a href="admin_edit?id=<?php echo $admin['admin_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </a>
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
                                                                        <button type="button" class="btn btn-sm btn-outline-danger js-admin-delete-btn"
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
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">
                                                    <?php
                                                    $start = $paginator->offset() + 1;
                                                    $end = $paginator->offset() + count($admins);
                                                    ?>
                                                    <?php if ($total_admins_count > 0) { ?>
                                                        Showing <?php echo $start; ?>–<?php echo $end; ?> of <?php echo $total_admins_count; ?>
                                                    <?php } else { ?>
                                                        No results
                                                    <?php } ?>
                                                </small>
                                                <?php if ($search !== '') { ?>
                                                    <small class="text-muted">Filtered by "<?php echo $search; ?>"</small>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-4 text-md-center">
                                                <nav aria-label="Pagination">
                                                    <ul class="pagination pagination-sm mb-0 justify-content-center">
                                                        <li class="page-item <?php echo !$paginator->hasPrev() ? 'disabled' : ''; ?>">
                                                            <a class="page-link" href="<?php echo buildPageUrl(max(1, $paginator->currentPage - 1)); ?>" tabindex="-1">&laquo;</a>
                                                        </li>
                                                        <?php foreach ($paginator->pages() as $pg) { ?>
                                                            <li class="page-item <?php echo ($pg === $paginator->currentPage) ? 'active' : ''; ?>">
                                                                <a class="page-link" href="<?php echo buildPageUrl($pg); ?>"><?php echo $pg; ?></a>
                                                            </li>
                                                        <?php } ?>
                                                        <li class="page-item <?php echo !$paginator->hasNext() ? 'disabled' : ''; ?>">
                                                            <a class="page-link" href="<?php echo buildPageUrl(min($paginator->totalPages, $paginator->currentPage + 1)); ?>">&raquo;</a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                            <div class="col-md-4 text-md-end">
                                                <small class="text-muted d-block">Last updated: <?php echo date('M j, Y g:i A'); ?></small>
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


    <!-- AJAX Change Status -->
    <script>
        $(function() {
            $('.js-admin-status-toggle:not(:disabled)').on('change', function() {
                const $cb = $(this);
                const adminId = $cb.data('admin-id');
                const originalChecked = !$cb.prop('checked');
                $cb.prop('disabled', true);

                $.ajax({
                        url: './include/ajax_admin_toggle_status.php',
                        method: 'POST',
                        data: {
                            admin_id: adminId
                        },
                        dataType: 'json'
                    })
                    .done(function(resp) {
                        if (!resp || resp.success !== true) {
                            $cb.prop('checked', originalChecked);
                            toastr.error(resp && resp.message ? resp.message : 'Failed to update status');
                        } else {
                            toastr.success('Status updated');
                        }
                    })
                    .fail(function(xhr) {
                        $cb.prop('checked', originalChecked);
                        let msg = 'Network / server error';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    })
                    .always(function() {
                        $cb.prop('disabled', false);
                    });
            });

            // SweetAlert2 delete handler
            $(document).on('click', '.js-admin-delete-btn', async function(e) {
                e.preventDefault();
                const $btn = $(this);
                const adminId = $btn.data('admin-id');
                const adminName = $btn.data('admin-name');

                const confirmed = await Swal.fire({
                    title: 'Delete admin?',
                    html: `<p class="mb-1">You are about to delete <strong>${$('<div>').text(adminName).html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    reverseButtons: true,
                    focusCancel: true,
                }).then(r => r.isConfirmed);

                if (!confirmed) return;

                $btn.prop('disabled', true).addClass('opacity-50');

                $.ajax({
                        url: './include/ajax_admin_delete.php',
                        method: 'POST',
                        data: {
                            admin_id: adminId
                        },
                        dataType: 'json'
                    })
                    .done(function(resp) {
                        if (resp && resp.success) {
                            toastr.success('Administrator deleted');
                            // Remove row gracefully
                            const $row = $btn.closest('tr');
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            toastr.error(resp && resp.message ? resp.message : 'Delete failed');
                        }
                    })
                    .fail(function(xhr) {
                        let msg = 'Server error';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    });
            });
        });
    </script>
</body>

</html>