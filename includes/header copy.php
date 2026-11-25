<?php
/** @var array $page  from get_page() */
defined('APP_NAME') || exit; // no direct access
$siteTitle = setting('seo_title', APP_NAME);
$metaTitle = trim((string)($page['meta']['title'] ?? ''));
$docTitle = $metaTitle !== '' ? $metaTitle . ' | ' . $siteTitle : $siteTitle;
$metaDesc = trim((string)($page['meta']['description'] ?? '')) ?: setting('seo_description');
$favicon = media_url(setting('favicon')) ?: url('assets/images/favicon.svg');
$ogImage = media_url(setting('og_image'));
$canonical = url($page['slug'] === 'home' ? '' : (page_schemas()[$page['slug']]['file'] ?? ''));
// Versioned asset URL (cache-busting) that works in every version of the project
$ver = function (string $path): string {
    $file = dirname(__DIR__) . '/' . $path;
    return url($path) . (is_file($file) ? '?v=' . filemtime($file) : '');
};
$cssFiles = array_filter(['assets/css/style.css', 'assets/css/animations.css', 'assets/css/responsive.css', 'assets/css/advanced.css', 'assets/css/navbar.css', 'assets/css/footer.css'],
    fn($f) => is_file(dirname(__DIR__) . '/' . $f));
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($docTitle) ?></title>
  <meta name="description" content="<?= e($metaDesc) ?>">
  <meta name="keywords" content="<?= e(setting('seo_keywords')) ?>">
  <link rel="canonical" href="<?= e($canonical) ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e($docTitle) ?>">
  <meta property="og:description" content="<?= e($metaDesc) ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <?php if ($ogImage): ?><meta property="og:image" content="<?= e($ogImage) ?>"><?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="theme-color" content="#0b1f18">
  <script>
    document.documentElement.classList.add('js');
    // Show the intro loader only once per visit — later pages open instantly
    try { if (sessionStorage.getItem('ds_seen')) document.documentElement.classList.add('no-preload'); } catch (e) {}
  </script>
  <meta name="view-transition" content="same-origin">
  <link rel="icon" href="<?= e($favicon) ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= e(url('assets/images/brand/favicon-32.png')) ?>">
  <link rel="apple-touch-icon" href="<?= e(url('assets/images/brand/apple-touch-icon.png')) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <?php $fonts = 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Poppins:wght@300;400;500;600&display=swap';
        $icons = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css'; ?>
  <!-- Fonts and icons load without blocking the first paint -->
  <link rel="preload" as="style" href="<?= e($fonts) ?>">
  <link rel="stylesheet" href="<?= e($fonts) ?>" media="print" onload="this.media='all'">
  <link rel="stylesheet" href="<?= e($icons) ?>" media="print" onload="this.media='all'" referrerpolicy="no-referrer">
  <noscript><link rel="stylesheet" href="<?= e($fonts) ?>"><link rel="stylesheet" href="<?= e($icons) ?>"></noscript>
  <?php if (function_exists('css_bundle')): ?>
  <?= css_bundle(array_values($cssFiles)) ?>
  <?php else: foreach ($cssFiles as $css): ?>
  <link rel="stylesheet" href="<?= e($ver($css)) ?>">
  <?php endforeach; endif; ?>

  <?php if ($page['slug'] === 'home' && is_file(dirname(__DIR__) . '/assets/images/brand/dream-spark-mark-lg.webp')): ?><link rel="preload" as="image" href="<?= e(url('assets/images/brand/dream-spark-mark-lg.webp')) ?>" type="image/webp"><?php endif; ?>
  <!-- Chrome/Edge: pages are pre-rendered when a visitor hovers a link, so they open instantly -->
  <script type="speculationrules">{"prerender":[{"where":{"and":[{"href_matches":"/*"},{"not":{"href_matches":"/admin/*"}},{"not":{"href_matches":"/*/admin/*"}},{"not":{"href_matches":"/*actions/*"}},{"not":{"selector_matches":"[target=_blank], [download]"}}]},"eagerness":"moderate"}]}</script>
  <script type="application/ld+json"><?= json_encode([
      '@context' => 'https://schema.org', '@type' => 'ProfessionalService', 'name' => setting('site_name'),
      'url' => url(), 'email' => setting('email'), 'telephone' => setting('phone'), 'address' => setting('address'),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
</head>
