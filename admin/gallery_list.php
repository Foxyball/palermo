<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
require_once(__DIR__ . '/../repositories/admin/GalleryRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$galleryRepo = new GalleryRepository($pdo);

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

$totalGalleriesCount = $galleryRepo->countAll($search);

$paginator = new Paginator($totalGalleriesCount, $page, $perPage);

$galleries = $galleryRepo->findAll($search, $paginator->limit(), $paginator->offset());

?>

<?php
headerContainer();
?>

<title>Galleries | <?php echo SITE_TITLE; ?></title>

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
                                <li class="breadcrumb-item active" aria-current="page">Gallery List</li>
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
                                        <h3 class="card-title mb-0">All Galleries</h3>
                                        <form class="d-flex" method="get" action="gallery_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control" placeholder="Search title, id" value="<?php echo $search; ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="gallery_list" title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="card-tools ms-md-auto">
                                        <a href="gallery_add" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus"></i> Add New Gallery
                                        </a>
                                    </div>

                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Title</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($galleries)) { ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="bi bi-person-x-fill fs-1"></i>
                                                                <p class="mt-2">No galleries found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php foreach ($galleries as $gallery) { ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong>#<?php echo $gallery['id']; ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo $gallery['title']; ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input js-gallery-status-toggle" type="checkbox" data-gallery-id="<?php echo $gallery['id']; ?>" <?php echo $gallery['active'] == '1' ? 'checked' : ''; ?>>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <small class="text-muted">
                                                                    <?php echo date('M j, Y', strtotime($gallery['created_at'])); ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <a href="gallery_images?id=<?php echo $gallery['id']; ?>" class="btn btn-sm btn-outline-warning" title="Manage Images">
                                                                        <i class="bi bi-image"></i>
                                                                    </a>
                                                                    <a href="gallery_edit?id=<?php echo $gallery['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger js-gallery-delete-btn"
                                                                        data-gallery-id="<?php echo $gallery['id']; ?>"
                                                                        data-gallery-name="<?php echo ($gallery['title']); ?>">
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
                                <?php if (!empty($galleries)) { ?>
                                    <?php renderPagination($paginator, $totalGalleriesCount, count($galleries), 'gallery_list', $search); ?>
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