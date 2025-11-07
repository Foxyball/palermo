<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/GalleryRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$galleryRepo = new GalleryRepository($pdo);

$galleryId = $_GET['id'] ?? 0;
if ($galleryId <= 0) {
    $_SESSION['error'] = 'Invalid gallery selected.';
    header('Location: gallery_list');
    exit;
}

$gallery_to_edit = $galleryRepo->findById($galleryId);

if (!$gallery_to_edit) {
    $_SESSION['error'] = 'Gallery not found.';
    header('Location: gallery_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');

    // Validation
    if ($title === '') {
        $errors[] = 'Gallery title is required';
    }

    if (empty($errors)) {
        if ($galleryRepo->titleExists($title, $galleryId)) {
            $errors[] = 'Gallery name already exists. Please choose a different name.';
        }
    }

    if (empty($errors)) {
        try {
            $galleryRepo->update($galleryId, $title);
            $_SESSION['success'] = 'Gallery updated successfully';
            header('Location: gallery_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating the gallery. Please try again.';
        }
    }
} else {
    $title = $gallery_to_edit['title'];
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Edit Gallery</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="gallery_list">Gallery List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Gallery</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Update Gallery</h3>
                                    <span class="badge text-bg-<?php echo ($gallery_to_edit['active'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($gallery_to_edit['active'] == '1') ? 'Active' : 'Inactive'; ?>
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
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Gallery Title *</label>
                                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="gallery_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update Gallery</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    Created: <?php echo date('M j, Y g:i A', strtotime($gallery_to_edit['created_at'])); ?>
                                    | Last Updated: <?php echo date('M j, Y g:i A', strtotime($gallery_to_edit['updated_at'])); ?>
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