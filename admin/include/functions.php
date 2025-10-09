<?php
function checkAdminLogin(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin(): void {
    if (!checkAdminLogin()) {
        header('Location: login');
        exit;
    }
}

function getCurrentAdmin(PDO $pdo): ?array {
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT admin_id, admin_name, admin_email, active, is_super_admin, last_log_date, last_log_ip, created_at FROM admins WHERE admin_id = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    return $admin !== false ? $admin : null;
}

function isCurrentSuperAdmin(?array $admin): bool {
    if (!$admin) {
        return false;
    }
    return isset($admin['is_super_admin']) && (int)$admin['is_super_admin'] === 1;
}

/**
 * Build a URL for paginated admin list (or similar) preserving existing query params.
 * Only keeps the search term 'q' and sets 'page' when > 1 to keep URL tidy.
 *
 * @param int $page Target page number (1-based)
 * @param string|null $base Base script path (defaults to 'admin_list')
 * @return string URL with query string
 */
function buildPageUrl(int $page, ?string $base = null): string {
    $base = $base ?: 'admin_list';
    $query = [];
    if (isset($_GET['q']) && $_GET['q'] !== '') {
        $query['q'] = $_GET['q'];
    }
    if ($page > 1) {
        $query['page'] = $page;
    }
    $qs = http_build_query($query);
    return $base . ($qs ? ('?' . $qs) : '');
}

/**
 * Generate a URL-friendly slug from a string
 * 
 * @param string $text The text to convert to slug
 * @return string Clean slug suitable for URLs
 */
function generateSlug(string $text): string {
    return strtolower(
        trim(
            preg_replace(
                ['/[^a-z0-9\s-]/', '/\s+/', '/-+/', '/^-|-$/'],
                ['', '-', '-', ''],
                $text
            )
        )
    );
}



/**
 * Upload multiple images to uploads/{year}/{month}/ and return their paths.
 * @param string $inputName The name attribute of the input[type=file] (should be an array - use inputName[])
 * @param bool $isThumb Optional. If true, uploads to uploads/{year}/{month}/t1/. Default false.
 * @return array List of uploaded file paths (relative to project root)
 */
function uploadMultiImage(string $inputName, bool $isThumb = false) : array
{
    $paths = [];
    $errors = [];

    $year = date('Y');
    $month = date('m');
    $baseDir = "uploads/$year/$month";
    $targetDir = $isThumb ? "$baseDir/t1" : $baseDir;

    // Make sure the directories exist
    if (!is_dir("uploads/$year")) mkdir("uploads/$year", 0777, true);
    if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);
    if ($isThumb && !is_dir($targetDir)) mkdir($targetDir, 0777, true);

    // Allowed extensions and MIME types
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
    ];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Check if files are uploaded
    if (!empty($_FILES[$inputName]['name'][0])) {
        $files = $_FILES[$inputName];
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $originalName = basename($files['name'][$i]);
                $fileSize = $files['size'][$i];
                $fileTmp = $files['tmp_name'][$i];
                $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $fileMime = mime_content_type($fileTmp);

                // Validate extension
                if (!in_array($fileExt, $allowedExtensions)) {
                    $errors[] = "File '$originalName' has an invalid file extension.";
                    continue;
                }
                // Validate MIME type
                if (!in_array($fileMime, $allowedMimeTypes)) {
                    $errors[] = "File '$originalName' has an invalid MIME type.";
                    continue;
                }
                // Validate file size
                if ($fileSize > $maxFileSize) {
                    $errors[] = "File '$originalName' exceeds the maximum allowed size of 2MB.";
                    continue;
                }

                $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-.]/', '_', $originalName);
                $destination = $targetDir . '/' . $safeName;

                if (move_uploaded_file($fileTmp, $destination)) {
                    $paths[] = $destination;
                } else {
                    $errors[] = "File '$originalName' could not be uploaded due to a server error.";
                }
            } else {
                $errors[] = "File '" . $files['name'][$i] . "' failed to upload. Error code: " . $files['error'][$i];
            }
        }
    }

    return ['paths' => $paths, 'errors' => $errors];
}