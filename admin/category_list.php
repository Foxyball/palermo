<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

$whereSql = '';
$params = [];
if ($search !== '') {
    $whereSql = ' WHERE (name LIKE :keyword OR id = :id)';
    $params[':keyword'] = '%' . $search . '%';
    $params[':id'] = $search;
}

$countSql = 'SELECT COUNT(*) FROM categories' . $whereSql;
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$totalCategoriesCount = $stmtCount->fetchColumn();

$paginator = new Paginator($totalCategoriesCount, $page, $perPage);

$dataSql = 'SELECT id, name, active, created_at
            FROM categories' . $whereSql . ' ORDER BY id DESC LIMIT :lim OFFSET :off';

$stmt = $pdo->prepare($dataSql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':lim', $paginator->limit(), PDO::PARAM_INT);
$stmt->bindValue(':off', $paginator->offset(), PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php
headerContainer();
?>

<title>Categories | <?php echo SITE_TITLE; ?></title>

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
                                <li class="breadcrumb-item active" aria-current="page">Category List</li>
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
                                        <h3 class="card-title mb-0">All Categories</h3>
                                        <form class="d-flex" method="GET" action="category_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control" placeholder="Search name, id" value="<?php echo $search; ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="category_list" title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="card-tools ms-md-auto">
                                        <a href="category_add" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus"></i> Add New Category
                                        </a>
                                    </div>

                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Category</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($categories)) { ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="bi bi-person-x-fill fs-1"></i>
                                                                <p class="mt-2">No customers found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php foreach ($categories as $category) { ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong>#<?php echo $category['id']; ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo $category['name']; ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input js-category-status-toggle" type="checkbox" data-category-id="<?php echo $category['id']; ?>" <?php echo $category['active'] == '1' ? 'checked' : ''; ?>>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <small class="text-muted">
                                                                    <?php echo date('M j, Y', strtotime($category['created_at'])); ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <a href="category_edit?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger js-category-delete-btn"
                                                                        data-category-id="<?php echo $category['id']; ?>"
                                                                        data-category-name="<?php echo $category['name']; ?>">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if (!empty($categories)) { ?>
                                    <div class="card-footer">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">
                                                    <?php
                                                    $start = $paginator->offset() + 1;
                                                    $end = $paginator->offset() + count($categories);
                                                    ?>
                                                    <?php if ($totalCategoriesCount > 0) { ?>
                                                        Showing <?php echo $start; ?>–<?php echo $end; ?> of <?php echo $totalCategoriesCount; ?>
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
                                                            <a class="page-link" href="<?php echo buildPageUrl(max(1, $paginator->currentPage - 1), 'category_list'); ?>" tabindex="-1">&laquo;</a>
                                                        </li>
                                                        <?php foreach ($paginator->pages() as $pg) { ?>
                                                            <li class="page-item <?php echo ($pg === $paginator->currentPage) ? 'active' : ''; ?>">
                                                                <a class="page-link" href="<?php echo buildPageUrl($pg, 'category_list'); ?>"><?php echo $pg; ?></a>
                                                            </li>
                                                        <?php } ?>
                                                        <li class="page-item <?php echo !$paginator->hasNext() ? 'disabled' : ''; ?>">
                                                            <a class="page-link" href="<?php echo buildPageUrl(min($paginator->totalPages, $paginator->currentPage + 1), 'category_list'); ?>">&raquo;</a>
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
            $('.js-category-status-toggle:not(:disabled)').on('change', function() {
                const $cb = $(this);
                const categoryId = $cb.data('category-id');
                const originalChecked = !$cb.prop('checked');
                $cb.prop('disabled', true);

                $.ajax({
                        url: './include/ajax_category_toggle_status.php',
                        method: 'POST',
                        data: {
                            id: categoryId,
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
            $(document).on('click', '.js-category-delete-btn', async function(e) {
                e.preventDefault();
                const $btn = $(this);
                const categoryId = $btn.data('category-id');
                const categoryName = $btn.data('category-name');

                const confirmed = await Swal.fire({
                    title: 'Delete Category?',
                    html: `<p class="mb-1">You are about to delete <strong>${$('<div>').text(categoryName).html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
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
                        url: './include/ajax_category_delete.php',
                        method: 'POST',
                        data: {
                            id: categoryId,
                        },
                        dataType: 'json'
                    })
                    .done(function(resp) {
                        if (resp && resp.success) {
                            toastr.success('Category deleted');
                            // Remove row from table
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