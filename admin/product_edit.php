<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$productId = $_GET['id'] ?? 0;
if ($productId <= 0) {
    $_SESSION['error'] = 'Invalid product selected.';
    header('Location: product_list');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$productId]);
$productToEdit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$productToEdit) {
    $_SESSION['error'] = 'Product not found.';
    header('Location: product_list');
    exit;
}

$errors = [];

$categoriesStmt = $pdo->prepare('SELECT id, name FROM categories WHERE active = "1" ORDER BY name ASC');
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$addonsStmt = $pdo->prepare('SELECT id, name, price FROM addons WHERE status = "1" ORDER BY name ASC');
$addonsStmt->execute();
$addons = $addonsStmt->fetchAll(PDO::FETCH_ASSOC);

$currentAddonsStmt = $pdo->prepare('SELECT addon_id FROM product_addons WHERE product_id = ?');
$currentAddonsStmt->execute([$productId]);
$currentAddons = $currentAddonsStmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $shortDescription = trim($_POST['short_description'] ?? '');
    $longDescription = trim($_POST['long_description'] ?? '');
    $selectedAddons = $_POST['addons'] ?? [];
    $slug = generateSlug($name);

    // Validation
    if ($name === '') {
        $errors[] = 'Product name is required';
    }

    if ($categoryId === '' || !is_numeric($categoryId)) {
        $errors[] = 'Please select a valid category';
    }

    if ($price === '' || !is_numeric($price) || floatval($price) < 0) {
        $errors[] = 'Please enter a valid price';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1');
        $stmt->execute([$slug, $productId]);
        if ($stmt->fetch()) {
            $errors[] = 'Product slug already exists. Please choose a different slug.';
        }
    }

    $imagePath = $productToEdit['image'];
    $hasFile = isset($_FILES['image']) && !empty($_FILES['image']['name']);

    if (empty($errors)) {
        if ($hasFile) {
            $upload = uploadImage('image',true);
            if (!empty($upload['path'])) {
                // Delete old image if exists
                if ($productToEdit['image']) {
                    deleteImageFile($productToEdit['image']);
                }
                $imagePath = $upload['path'];
            } elseif (!empty($upload['error'])) {
                $errors[] = $upload['error'];
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('UPDATE products SET category_id = ?, name = ?, slug = ?, image = ?, price = ?, short_description = ?, long_description = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([
                $categoryId,
                $name,
                $slug,
                $imagePath,
                $price,
                $shortDescription,
                $longDescription,
                $productId
            ]);

            // Delete existing product addons
            $stmt = $pdo->prepare('DELETE FROM product_addons WHERE product_id = ?');
            $stmt->execute([$productId]);

            if (!empty($selectedAddons)) {
                $addonStmt = $pdo->prepare('INSERT INTO product_addons (product_id, addon_id) VALUES (?, ?)');
                foreach ($selectedAddons as $addonId) {
                    if (is_numeric($addonId)) {
                        $addonStmt->execute([$productId, $addonId]);
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = 'Product updated successfully';
            header('Location: product_list');
            exit;
        } catch (PDOException $e) {
            $pdo->rollback();
            $errors[] = 'There was an error updating the product. Please try again.';
            error_log("Product update error: " . $e->getMessage());
        }
    }
}

headerContainer();
?>

<title>Edit Product | <?php echo SITE_TITLE; ?></title>
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
                            <h3 class="mb-0">Edit Product</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="product_list">Product List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="bi bi-pencil-square me-2"></i>
                                        Edit Product
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($errors)) { ?>
                                        <div class="alert alert-danger">
                                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Please fix the following errors:</h6>
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $err) { ?>
                                                    <li><?php echo htmlspecialchars($err); ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>

                                    <form method="post" enctype="multipart/form-data" novalidate>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Product Name *</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="<?php echo htmlspecialchars($_POST['name'] ?? $productToEdit['name']); ?>" required
                                                        placeholder="e.g. Margherita Pizza">
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Category *</label>
                                                            <select name="category_id" class="form-select" required>
                                                                <option value="">Select Category</option>
                                                                <?php foreach ($categories as $category) { ?>
                                                                    <option value="<?php echo $category['id']; ?>"
                                                                        <?php echo (($_POST['category_id'] ?? $productToEdit['category_id']) == $category['id']) ? 'selected' : ''; ?>>
                                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Price *</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" name="price" class="form-control" step="0.01" min="0"
                                                                    value="<?php echo htmlspecialchars($_POST['price'] ?? $productToEdit['price']); ?>" required
                                                                    placeholder="0.00">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Short Description</label>
                                                    <textarea name="short_description" class="form-control" rows="2"
                                                        placeholder="Brief description for listings"><?php echo htmlspecialchars($_POST['short_description'] ?? $productToEdit['short_description']); ?></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Long Description</label>
                                                    <textarea name="long_description" class="form-control" rows="5"
                                                        placeholder="Detailed product description"><?php echo htmlspecialchars($_POST['long_description'] ?? $productToEdit['long_description']); ?></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-4">

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Product Image</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                    <?php if ($productToEdit['image']): ?>
                                                        <div class="mt-2">
                                                            <img src="<?php echo BASE_URL . htmlspecialchars($productToEdit['image']); ?>"
                                                                alt="Current product image"
                                                                style="max-width: 100px; max-height: 100px; object-fit: cover; margin-top: 5px;">
                                                        </div>
                                                    <?php endif; ?>
                                                    <small class="text-muted">JPG, PNG, or SVG (max 2MB)</small>
                                                </div>

                                                <?php if (!empty($addons)) { ?>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Available Addons</label>
                                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                                            <?php foreach ($addons as $addon) { ?>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="addons[]" value="<?php echo $addon['id']; ?>"
                                                                        id="addon_<?php echo $addon['id']; ?>"
                                                                        <?php echo in_array($addon['id'], $_POST['addons'] ?? $currentAddons) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="addon_<?php echo $addon['id']; ?>">
                                                                        <?php echo htmlspecialchars($addon['name']); ?>
                                                                        <small class="text-muted">(+$<?php echo number_format($addon['price'], 2); ?>)</small>
                                                                    </label>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <small class="text-muted">Select addons available for this product</small>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="product_list" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-1"></i>
                                                Back to Products
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-pencil-square me-1"></i>
                                                Update Product
                                            </button>
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

    <script>
        $(function() {
            $('form').on('submit', function(e) {
                let isValid = true;

                $('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                let price = $('input[name="price"]').val();
                if (price && (isNaN(price) || parseFloat(price) < 0)) {
                    isValid = false;
                    $('input[name="price"]').addClass('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid:first').offset().top - 100
                    }, 500);
                }
            });

            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });
        });
    </script>

</body>

</html>