<?php

function renderSeoTags(array $seoData = []): void
{
    $defaults = [
        'title' => SITE_TITLE,
        'description' => 'Palermo - Authentic Italian Pizza & Grill Restaurant',
        'image' => BASE_URL . 'images/palermo_logo.png',
        'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'type' => 'website'
    ];
    
    $seo = array_merge($defaults, $seoData);
    
    $seo['title'] = htmlspecialchars($seo['title'], ENT_QUOTES, 'UTF-8');
    $seo['description'] = htmlspecialchars($seo['description'], ENT_QUOTES, 'UTF-8');
    
    if (!empty($seo['image']) && !preg_match('/^https?:\/\//', $seo['image'])) {
        $seo['image'] = rtrim(BASE_URL, '/') . '/' . ltrim($seo['image'], '/');
    }
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
