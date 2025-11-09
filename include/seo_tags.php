<?php
/**
 * SEO Meta Tags Component
 * 
 * Generates dynamic meta tags for SEO optimization including:
 * - Basic meta tags (title, description)
 * - Open Graph tags (Facebook, LinkedIn)
 * - Twitter Card tags
 * 
 * Usage:
 * renderSeoTags([
 *     'title' => 'Page Title',
 *     'description' => 'Page description',
 *     'image' => 'path/to/image.jpg',
 *     'url' => 'https://example.com/page'
 * ]);
 */

/**
 * Render SEO meta tags
 * 
 * @param array $seoData Array containing SEO data:
 *                       - title: Page title (required)
 *                       - description: Page description (optional)
 *                       - image: Page image URL (optional)
 *                       - url: Page canonical URL (optional)
 *                       - type: Open Graph type (optional, default: 'website')
 */
function renderSeoTags(array $seoData = []): void
{
    // Default values
    $defaults = [
        'title' => SITE_TITLE,
        'description' => 'Palermo - Authentic Italian Pizza & Grill Restaurant',
        'image' => BASE_URL . 'images/palermo_logo.png',
        'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'type' => 'website'
    ];
    
    // Merge with provided data
    $seo = array_merge($defaults, $seoData);
    
    // Sanitize data
    $seo['title'] = htmlspecialchars($seo['title'], ENT_QUOTES, 'UTF-8');
    $seo['description'] = htmlspecialchars($seo['description'], ENT_QUOTES, 'UTF-8');
    
    // Ensure image is absolute URL
    if (!empty($seo['image']) && !preg_match('/^https?:\/\//', $seo['image'])) {
        // Remove leading slash if present to avoid double slashes
        $seo['image'] = rtrim(BASE_URL, '/') . '/' . ltrim($seo['image'], '/');
    }
    
    // Output meta tags
    ?>
    
    <!-- Primary Meta Tags -->
    <meta name="title" content="<?php echo $seo['title']; ?>">
    <meta name="description" content="<?php echo $seo['description']; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $seo['type']; ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($seo['url'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?php echo $seo['title']; ?>">
    <meta property="og:description" content="<?php echo $seo['description']; ?>">
    <?php if (!empty($seo['image'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($seo['image'], ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($seo['url'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="twitter:title" content="<?php echo $seo['title']; ?>">
    <meta property="twitter:description" content="<?php echo $seo['description']; ?>">
    <?php if (!empty($seo['image'])): ?>
    <meta property="twitter:image" content="<?php echo htmlspecialchars($seo['image'], ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    
    <?php
}
