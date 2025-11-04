<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/BlogRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$blogRepo = new BlogRepository($pdo);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $categoryId = $_POST['category_id'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $galleryId = $_POST['gallery_id'] ?? null;

    $slug = generateSlug($title);

    if ($title === '') {
        $errors[] = 'Title is required';
    }
    if ($categoryId === '') {
        $errors[] = 'Category is required';
    }
    if ($description === '') {
        $errors[] = 'Content is required';
    }
    if ($galleryId === '') {
        $galleryId = null;
    }

    $imagePath = null;
    $imageUrl = trim($_POST['image_url'] ?? '');
    $hasFile = isset($_FILES['image']) && !empty($_FILES['image']['name']);
    $hasUrl = $imageUrl !== '';

    if ($hasFile && $hasUrl) {
        $errors[] = 'Please provide either a featured image file or an image URL, not both.';
    }

    if (empty($errors)) {
        if ($hasFile) {
            $upload = uploadImage('image', true);
            if (!empty($upload['path'])) {
                $imagePath = $upload['path'];
            } elseif (!empty($upload['error'])) {
                $errors[] = $upload['error'];
            }
        } elseif ($hasUrl) {
            $result = getImageFromUrl($imageUrl, true);
            if (!empty($result['path'])) {
                $imagePath = $result['path'];
            } elseif (!empty($result['error'])) {
                $errors[] = $result['error'];
            }
        }
    }

    if (empty($errors)) {

        try {
            $blogRepo->create(
                $_SESSION['admin_id'],
                (int)$categoryId,
                $galleryId ? (int)$galleryId : null,
                $imagePath,
                $title,
                $slug,
                $description
            );
            $_SESSION['success'] = 'Blog post created successfully';
            header('Location: blog_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error creating the blog post: ' . $e->getMessage();
        }
    }
}

$blogCategories = $blogRepo->getActiveBlogCategories();
$galleries = $blogRepo->getActiveGalleries();

headerContainer();
?>

</head>
<style>
    .ck-editor__editable_inline {
        min-height: 300px;
    }
</style>


<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php navbarContainer(); ?>
        <?php sidebarContainer(); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Add New Blog Post</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="blog_list">Blog List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Blog Post</li>
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
                                <div class="card-header">
                                    <h3 class="card-title">Create Blog Post</h3>
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

                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label" for="title">Title *</label>
                                                <input type="text" id="title" name="title" class="form-control"
                                                    value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                                    placeholder="e.g. Our Autumn Menu Is Here" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="category_id">Category *</label>
                                                <select id="category_id" name="category_id" class="form-select">
                                                    <option value="">-- Select a category --</option>
                                                    <?php
                                                    foreach ($blogCategories as $cat) {
                                                        echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="gallery_id">Gallery</label>
                                                <select id="gallery_id" name="gallery_id" class="form-select">
                                                    <option value="">-- Select a gallery --</option>
                                                    <?php
                                                    foreach ($galleries as $gallery) {
                                                        echo '<option value="' . $gallery['id'] . '">' . htmlspecialchars($gallery['title']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label" for="description">Content *</label>
                                                <textarea id="description" name="description" class="form-control ckeditor-textarea" rows="8"
                                                    placeholder="Write your post content here..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="image">Featured Image *</label>
                                                <input type="file" id="image" name="image" class="form-control"
                                                    accept="image/*" />
                                                <small class="text-muted">Recommended: JPG/PNG, up to 2MB.</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="image_url">Or Image URL</label>
                                                <input type="url" id="image_url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>" />
                                                <small class="text-muted">Paste a direct image URL (JPG, PNG, SVG, max 2MB).</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Preview</label><br>
                                                <img class="js-image-preview" src="#" alt="Image preview" style="max-width:220px; max-height:180px; display:none; border:1px solid #ccc; background:#fafafa;" />
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="blog_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Create Post</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php footerContainer(); ?>
    </div>

    <script src="./js/bundle.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            function showPreview(src) {
                if (src) {
                    $('.js-image-preview').attr('src', src).show();
                } else {
                    $('.js-image-preview').hide();
                }
            }
            $('#image').change(function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        showPreview(e.target.result);
                    }
                    reader.readAsDataURL(file);
                } else {
                    showPreview(null);
                }
            });
            $('#image_url').on('input', function() {
                var url = $(this).val().trim();
                if (url && (url.match(/^https?:\/\//i))) {
                    showPreview(url);
                } else if (!$('#image').val()) {
                    showPreview(null);
                }
            });
            // On page load, show preview if editing/returning to form
            if ($('#image_url').val()) {
                showPreview($('#image_url').val());
            }
        });
    </script>

</body>

</html>