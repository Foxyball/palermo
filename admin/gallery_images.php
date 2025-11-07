<?php

require_once(__DIR__ . '/../include/connect.php');
/** @var PDO $pdo */
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$galleryId = $_GET['id'] ?? 0;
if ($galleryId <= 0) {
    $_SESSION['error'] = 'Invalid gallery selected.';
    header('Location: gallery_list');
    exit;
}

$stmt = $pdo->prepare('SELECT id, title, active, created_at, updated_at FROM galleries WHERE id = ? LIMIT 1');
$stmt->execute([$galleryId]);
$gallery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gallery) {
    $_SESSION['error'] = 'Gallery not found.';
    header('Location: gallery_list');
    exit;
}

$errors = [];
$messages = [];

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetGalleryId = $galleryId;

    $result = uploadMultiImage('images');

    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $e) {
            $errors[] = $e;
        }
    }

    if (!empty($result['paths'])) {
        try {
            $pdo->beginTransaction();
            $ins = $pdo->prepare('INSERT INTO gallery_images (gallery_id, image) VALUES (?, ?)');
            foreach ($result['paths'] as $path) {
                $ins->execute([$targetGalleryId, $path]);
            }
            $pdo->commit();
            $messages[] = count($result['paths']) . ' image(s) uploaded successfully.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $errors[] = 'There was an error saving uploaded images. Please try again.';
        }
    } else if (empty($result['errors'])) {
        $errors[] = 'Please select at least one image to upload.';
    }
}

$images = [];
try {
    $stmt = $pdo->prepare('SELECT id, image, created_at FROM gallery_images WHERE gallery_id = ? ORDER BY id DESC');
    $stmt->execute([$galleryId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $errors[] = 'Failed to load gallery images.';
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
                        <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Add Images</h3>
                        <div class="small text-secondary mt-1">
                            Gallery: <strong><?php echo htmlspecialchars($gallery['title']); ?></strong>
                            <span class="ms-2 badge text-bg-<?php echo ($gallery['active'] == '1') ? 'success' : 'secondary'; ?>">
                                <?php echo ($gallery['active'] == '1') ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                            <li class="breadcrumb-item"><a href="gallery_list">Gallery List</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Images</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Upload Images</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($messages)) { ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            <?php foreach ($messages as $msg) { ?>
                                                <li><?php echo htmlspecialchars($msg); ?></li>
                                            <?php } ?>
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($errors)) { ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $err) { ?>
                                                <li><?php echo htmlspecialchars($err); ?></li>
                                            <?php } ?>
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php } ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Images (jpg, jpeg, png, svg) - Max 2MB each</label>
                                            <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/svg+xml">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="gallery_list" class="btn btn-outline-secondary">Back to List</a>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Images in "<?php echo htmlspecialchars($gallery['title']); ?>"</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                <table class="table table-striped mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px;">ID</th>
                                                <th style="width: 120px;">Preview</th>
                                                <th>Path</th>
                                                <th style="width: 200px;">Uploaded</th>
                                                <th style="width: 120px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($images)) { ?>
                                            <tr><td colspan="5" class="text-center text-muted">No images found.</td></tr>
                                        <?php } else { foreach ($images as $img) { ?>
                                            <tr data-image-row-id="<?php echo (int)$img['id']; ?>">
                                                <td>#<?php echo (int)$img['id']; ?></td>
                                                <td>
                                                    <img src="<?php echo BASE_URL . htmlspecialchars($img['image']); ?>" alt="img" style="max-width:100px; max-height:80px; object-fit:cover;" />
                                                </td>
                                                <td class="text-break"><code class="d-inline-block" style="max-width: 520px; white-space: normal; word-break: break-all; overflow-wrap: anywhere;">&nbsp;<?php echo htmlspecialchars($img['image']); ?></code></td>
                                                <td><?php echo $img['created_at'] ? date('M j, Y g:i A', strtotime($img['created_at'])) : '-'; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger js-gallery-image-delete-btn" title="Delete" data-image-id="<?php echo (int)$img['id']; ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php }} ?>
                                        </tbody>
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

<script>
    $(function() {
        $(document).on('click', '.js-gallery-image-delete-btn', async function(e) {
            e.preventDefault();
            const $btn = $(this);
            const imageId = $btn.data('image-id');

            const confirmed = await Swal.fire({
                title: 'Delete Image?',
                text: 'This action cannot be undone.',
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
                url: './include/ajax_gallery_image_delete.php',
                method: 'POST',
                data: { id: imageId },
                dataType: 'json'
            })
            .done(function(resp) {
                if (resp && resp.success) {
                    toastr.success('Image deleted');
                    const $row = $(document).find(`tr[data-image-row-id="${imageId}"]`);
                    $row.fadeOut(250, function(){ $(this).remove(); });
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