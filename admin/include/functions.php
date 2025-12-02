<?php
function checkAdminLogin(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin(): void
{
    if (!checkAdminLogin()) {
        header('Location: login');
        exit;
    }
}

function getCurrentAdmin(PDO $pdo): ?array
{
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT admin_id, admin_name, admin_email, active, is_super_admin, last_log_date, last_log_ip, created_at FROM admins WHERE admin_id = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    return $admin !== false ? $admin : null;
}

function isCurrentSuperAdmin(?array $admin): bool
{
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
function buildPageUrl(int $page, ?string $base = null): string
{
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


function transliterateCyrillic(string $text): string
{
    $cyrillic = [
        'а', 'б', 'в', 'г', 'д', 'ѓ', 'е', 'ж', 'з', 'ѕ', 'и', 'ј', 'к', 'л', 'љ', 'м', 'н', 'њ', 'о', 'п', 'р', 'с', 'т', 'ќ', 'у', 'ф', 'х', 'ц', 'ч', 'џ', 'ш',
        'А', 'Б', 'В', 'Г', 'Д', 'Ѓ', 'Е', 'Ж', 'З', 'Ѕ', 'И', 'Ј', 'К', 'Л', 'Љ', 'М', 'Н', 'Њ', 'О', 'П', 'Р', 'С', 'Т', 'Ќ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш'
    ];
    $latin = [
        'a', 'b', 'v', 'g', 'd', 'gj', 'e', 'zh', 'z', 'dz', 'i', 'j', 'k', 'l', 'lj', 'm', 'n', 'nj', 'o', 'p', 'r', 's', 't', 'kj', 'u', 'f', 'h', 'c', 'ch', 'dj', 'sh',
        'A', 'B', 'V', 'G', 'D', 'Gj', 'E', 'Zh', 'Z', 'Dz', 'I', 'J', 'K', 'L', 'Lj', 'M', 'N', 'Nj', 'O', 'P', 'R', 'S', 'T', 'Kj', 'U', 'F', 'H', 'C', 'Ch', 'Dj', 'Sh'
    ];
    
    return str_replace($cyrillic, $latin, $text);
}

/**
 * Generate a URL-friendly slug from a string
 *
 * @param string $text The text to convert to slug
 * @return string Clean slug suitable for URLs
 */
function generateSlug(string $text): string
{
    $text = transliterateCyrillic($text);
    $text = strtolower($text);
    
    return trim(
        preg_replace(
            ['/[^a-z0-9\s-]/', '/\s+/', '/-+/', '/^-|-$/'],
            ['', '-', '-', ''],
            $text
        )
    );
}


/**
 * Upload multiple images to uploads/{year}/{month}/ and return their paths.
 * Stores relative paths (e.g., uploads/2025/10/filename.jpg) suitable for web and DB.
 * Saves files under project root, not under admin/.
 * @param string $inputName The name attribute of the input[type=file] (should be an array - use inputName[])
 * @param bool $isThumb Optional. If true, uploads to uploads/{year}/{month}/t1/. Default false.
 * @return array ['paths' => string[], 'errors' => string[]]
 */
function uploadMultiImage(string $inputName, bool $isThumb = false): array
{
    $paths = [];
    $errors = [];

    $year = date('Y');
    $month = date('m');

    $baseDirRel = "uploads/$year/$month";
    $targetDirRel = $isThumb ? "$baseDirRel/t1" : $baseDirRel;

    // Absolute filesystem base (project root)
    $projectRoot = dirname(__DIR__, 2);
    $targetDirFs = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $targetDirRel);

    if (!is_dir($targetDirFs)) {
        if (!mkdir($targetDirFs, 0777, true) && !is_dir($targetDirFs)) {
            $errors[] = 'Failed to create upload directory.';
            return ['paths' => $paths, 'errors' => $errors];
        }
    }

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
                $fileMime = @mime_content_type($fileTmp) ?: '';

                // Validate extension
                if (!in_array($fileExt, $allowedExtensions, true)) {
                    $errors[] = "File '$originalName' has an invalid file extension.";
                    continue;
                }
                // Validate MIME type
                if (!in_array($fileMime, $allowedMimeTypes, true)) {
                    $errors[] = "File '$originalName' has an invalid MIME type.";
                    continue;
                }
                // Validate file size
                if ($fileSize > $maxFileSize) {
                    $errors[] = "File '$originalName' exceeds the maximum allowed size of 2MB.";
                    continue;
                }

                $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-.]/', '_', $originalName);
                $destinationFs = $targetDirFs . DIRECTORY_SEPARATOR . $safeName;
                $destinationRel = $targetDirRel . '/' . $safeName;

                if (move_uploaded_file($fileTmp, $destinationFs)) {
                    $paths[] = $destinationRel;
                } else {
                    $errors[] = "File '$originalName' could not be uploaded due to a server error.";
                }
            } else {
                $errors[] = "File '" . ($files['name'][$i] ?? 'unknown') . "' failed to upload. Error code: " . ($files['error'][$i] ?? '');
            }
        }
    }

    return ['paths' => $paths, 'errors' => $errors];
}

/**
 * Upload single image to uploads/{year}/{month}/ and return its path.
 * Stores relative path (e.g., uploads/2025/10/filename.jpg) suitable for web and DB.
 * Saves files under project root, not under admin/.
 * @param string $inputName The name attribute of the input[type=file]
 * @param bool $isThumb Optional. If true, uploads to uploads/{year}/{month}/t1/
 * @return array ['path' => ?string, 'error' => ?string]
 */
function uploadImage(string $inputName, bool $isThumb = false): array
{
    $result = ['path' => null, 'error' => null];

    if (!isset($_FILES[$inputName]) || empty($_FILES[$inputName]['name'])) {
        return $result; // No file selected
    }

    $file = $_FILES[$inputName];

    if (!is_array($file) || !isset($file['error'])) {
        $result['error'] = 'Invalid upload.';
        return $result;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Upload failed. Error code: ' . $file['error'];
        return $result;
    }

    $year = date('Y');
    $month = date('m');

    $baseDirRel = "uploads/$year/$month";
    $targetDirRel = $isThumb ? "$baseDirRel/t1" : $baseDirRel;

    // Absolute filesystem base (project root)
    $projectRoot = dirname(__DIR__, 2);
    $targetDirFs = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $targetDirRel);

    if (!is_dir($targetDirFs)) {
        if (!mkdir($targetDirFs, 0777, true) && !is_dir($targetDirFs)) {
            $result['error'] = 'Failed to create upload directory.';
            return $result;
        }
    }

    // Allowed extensions and MIME types
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
    ];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    $originalName = basename($file['name']);
    $fileSize = $file['size'] ?? 0;
    $fileTmp = $file['tmp_name'] ?? '';
    $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $fileMime = $fileTmp ? (@mime_content_type($fileTmp) ?: '') : '';

    if (!in_array($fileExt, $allowedExtensions, true)) {
        $result['error'] = "Invalid file extension.";
        return $result;
    }
    if (!in_array($fileMime, $allowedMimeTypes, true)) {
        $result['error'] = "Invalid file type.";
        return $result;
    }
    if ($fileSize > $maxFileSize) {
        $result['error'] = "File exceeds the maximum allowed size of 2MB.";
        return $result;
    }

    $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-.]/', '_', $originalName);
    $destinationFs = $targetDirFs . DIRECTORY_SEPARATOR . $safeName;
    $destinationRel = $targetDirRel . '/' . $safeName;

    if (move_uploaded_file($fileTmp, $destinationFs)) {
        $result['path'] = $destinationRel;
    } else {
        $result['error'] = 'File could not be uploaded due to a server error.';
    }

    return $result;
}

/**
 * Download an image from a URL and save it to uploads/{year}/{month}/ (or t1/ for thumbs).
 * Returns ['path' => ?string, 'error' => ?string]
 * @param string $imageUrl The URL of the image to download
 * @param bool $isThumb Optional. If true, uploads to uploads/{year}/{month}/t1/
 * @return array ['path' => ?string, 'error' => ?string]
 */
function getImageFromUrl(string $imageUrl, bool $isThumb = false): array
{
    $result = ['path' => null, 'error' => null];
    if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        $result['error'] = 'Invalid image URL.';
        return $result;
    }
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
    ];
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    // Get image content
    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
        $result['error'] = 'Could not download image.';
        return $result;
    }
    if (strlen($imageData) > $maxFileSize) {
        $result['error'] = 'Image exceeds the maximum allowed size of 2MB.';
        return $result;
    }
    $urlPath = parse_url($imageUrl, PHP_URL_PATH);
    $ext = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($imageData);
        $ext = array_search($mime, [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
        ], true);
        if ($ext === false) {
            $result['error'] = 'Invalid or unsupported image type.';
            return $result;
        }
    } else {
        // Validate mime type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($imageData);
        if (!in_array($mime, $allowedMimeTypes, true)) {
            $result['error'] = 'Invalid or unsupported image type.';
            return $result;
        }
    }
    $year = date('Y');
    $month = date('m');
    $baseDirRel = "uploads/$year/$month";
    $targetDirRel = $isThumb ? "$baseDirRel/t1" : $baseDirRel;
    $projectRoot = dirname(__DIR__, 2);
    $targetDirFs = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $targetDirRel);
    if (!is_dir($targetDirFs)) {
        if (!mkdir($targetDirFs, 0777, true) && !is_dir($targetDirFs)) {
            $result['error'] = 'Failed to create upload directory.';
            return $result;
        }
    }
    $safeName = uniqid('url_') . '.' . $ext;
    $destinationFs = $targetDirFs . DIRECTORY_SEPARATOR . $safeName;
    $destinationRel = $targetDirRel . '/' . $safeName;
    if (file_put_contents($destinationFs, $imageData) !== false) {
        $result['path'] = $destinationRel;
    } else {
        $result['error'] = 'Failed to save image.';
    }
    return $result;
}

/**
 * Delete a single file by its relative path under the project root uploads.
 * Mirrors the simplicity of Laravel's File::delete approach.
 * @param string $relativePath e.g. 'uploads/2025/10/image.jpg'
 * @return bool True if deleted (or file missing), false if exists but could not be deleted
 */
function deleteImageFile(string $relativePath): bool
{
    $relativePath = trim($relativePath);
    if ($relativePath === '') {
        return false;
    }

    $projectRoot = dirname(__DIR__, 2);
    $rootReal = realpath($projectRoot) ?: $projectRoot;

    $candidate = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    $fileReal = @realpath($candidate) ?: $candidate;

    if (strpos($fileReal, $rootReal) !== 0) {
        return false;
    }

    if (!file_exists($fileReal)) {
        return true;
    }

    if (is_file($fileReal) && is_writable($fileReal)) {
        return @unlink($fileReal);
    }

    return false;
}




function getStatusBadge(string $statusName): string
{
    $statusLower = strtolower($statusName ?? 'Unknown');

    $statusMapping = [
        'pending' => 'bg-warning',
        'confirmed' => 'bg-info',
        'preparing' => 'bg-info',
        'ready' => 'bg-primary',
        'out for delivery' => 'bg-primary',
        'delivered' => 'bg-success',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger',
        'canceled' => 'bg-danger'
    ];

    $statusClass = 'bg-secondary';
    foreach ($statusMapping as $keyword => $class) {
        if (strpos($statusLower, $keyword) !== false) {
            $statusClass = $class;
            break;
        }
    }

    return '<span class="badge ' . $statusClass . ' text-white">' . htmlspecialchars($statusName ?? 'Unknown') . '</span>';
}