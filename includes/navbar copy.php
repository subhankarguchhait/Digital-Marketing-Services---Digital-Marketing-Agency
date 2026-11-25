<?php
/**
 * Advanced navbar — Dream Spark
 * Transparent on the hero → floating glass pill on scroll · sliding gold indicator · rolling link text ·
 * magnetic CTA · reading-progress line · full-screen circular-reveal mobile menu.
 * Ids #nav / #navToggle / #mobileMenu are kept so main.js (scroll state, open/close, Esc) keeps working.
 * Styles: assets/css/navbar.css · Effects: assets/js/navbar.js
 * @var array $page
 */
defined('APP_NAME') || exit; // no direct access
$navItems = [
    'home' => ['index.php', 'Home'],
    'about' => ['about.php', 'About'],
    'brands' => ['brands.php', 'Our Brands'],
    'contact' => ['contact.php', 'Contact'],
];
$siteName = setting('site_name') ?: 'Dream Spark';
// logo helper that also works on older copies of the project (no logo_picture())
$logoImg = function (string $path, string $class, string $alt, string $attrs = ''): string {
    if ($path === '' || media_url($path) === '') return '';
    if (function_exists('logo_picture')) return logo_picture($path, $class, $alt, $attrs);
    return '<img class="' . e($class) . '" src="' . e(media_url($path)) . '" alt="' . e($alt) . '" ' . $attrs . '>';
};
$brandHtml = $logoImg(setting('logo'), 'xn__logo', $siteName, 'fetchpriority="high" decoding="async"')
    ?: '<svg class="xn__mark" aria-hidden="true"><use href="#ds-spark"></use></svg><span class="xn__word">' . site_name_html() . '</span>';
$ctaText = setting('nav_cta_text') ?: 'Start a Project';
$ctaHref = link_href(setting('nav_cta_link', 'contact.php'));
$wa = contact_href('whatsapp', setting('whatsapp'));
$href = fn(string $file): string => url($file === 'index.php' ? '' : $file);
?>
<body data-page="<?= e($page['slug']) ?>">
  <svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
      <linearGradient id="dsSparkGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f3dca0"/><stop offset="1" stop-color="#d9b26a"/></linearGradient>
      <symbol id="ds-spark" viewBox="0 0 48 48">
        <circle cx="24" cy="24" r="22" fill="none" stroke="#c9a45c" stroke-width="1.2" opacity=".7"/>
        <path d="M24 5 C25.6 17 31 22.4 43 24 C31 25.6 25.6 31 24 43 C22.4 31 17 25.6 5 24 C17 22.4 22.4 17 24 5Z" fill="url(#dsSparkGrad)"/>
      </symbol>
    </defs>
  </svg>

  <div class="preloader" id="preloader" aria-hidden="true">
    <div class="preloader__inner">
      <?php if ($pre = $logoImg(setting('logo_stacked') ?: setting('logo'), 'preloader__logo', '', 'fetchpriority="high"')): ?>
        <?= $pre ?>
      <?php else: ?>
        <svg class="preloader__mark"><use href="#ds-spark"></use></svg>
        <div class="preloader__word"><?= site_name_html() ?></div>
      <?php endif; ?>
      <div class="preloader__bar"><span></span></div>
    </div>
  </div>
  <div class="cursor" aria-hidden="true"><span class="cursor__dot"></span><span class="cursor__ring"></span></div>
  <div class="scroll-progress" aria-hidden="true"></div>
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="xn" id="nav" data-xn>
    <div class="xn__bar">
      <a href="<?= e(url()) ?>" class="xn__brand" aria-label="<?= e($siteName) ?> home"><?= $brandHtml ?></a>

      <nav class="xn__links" aria-label="Main navigation" data-xn-links>
        <span class="xn__pill" aria-hidden="true" data-xn-pill></span>
        <?php foreach ($navItems as $key => [$file, $label]): $on = $key === $page['slug']; ?>
          <a class="xn__link<?= $on ? ' is-active' : '' ?>" href="<?= e($href($file)) ?>"<?= $on ? ' aria-current="page"' : '' ?>>
            <span class="xn__roll" data-t="<?= e($label) ?>"><span><?= e($label) ?></span></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="xn__actions">
        <?php if ($wa): ?><a class="xn__icon" href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" data-xn-mag="0.35"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a><?php endif; ?>
        <a class="xn__cta" href="<?= e($ctaHref) ?>" data-xn-mag="0.3">
          <span class="xn__cta-bg" aria-hidden="true"></span>
          <span class="xn__roll" data-t="<?= e($ctaText) ?>"><span><?= e($ctaText) ?></span></span>
          <span class="xn__cta-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <button class="xn__toggle" id="navToggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
          <span class="xn__toggle-lines" aria-hidden="true"><i></i><i></i></span>
          <span class="xn__toggle-text" aria-hidden="true"><span>Menu</span><span>Close</span></span>
        </button>
      </div>
      <span class="xn__progress" aria-hidden="true"><i></i></span>
    </div>
  </header>

  <div class="xm" id="mobileMenu" aria-hidden="true">
    <div class="xm__bg" aria-hidden="true"></div>
    <div class="xm__inner">
      <p class="xm__eyebrow">Navigation</p>
      <nav class="xm__links" aria-label="Mobile navigation">
        <?php $i = 0; foreach ($navItems as $key => [$file, $label]): ?>
          <a href="<?= e($href($file)) ?>" style="--i:<?= $i ?>"<?= $key === $page['slug'] ? ' class="is-active" aria-current="page"' : '' ?>>
            <small>0<?= ++$i ?></small><span><?= e($label) ?></span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="xm__foot" style="--i:<?= $i ?>">
        <a class="xm__cta" href="<?= e($ctaHref) ?>"><?= e($ctaText) ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        <div class="xm__contact">
          <?php if (setting('email')): ?><a href="<?= e(contact_href('email', setting('email'))) ?>"><?= e(setting('email')) ?></a><?php endif; ?>
          <?php if (setting('phone')): ?><a href="<?= e(contact_href('phone', setting('phone'))) ?>"><?= e(setting('phone')) ?></a><?php endif; ?>
        </div>
        <?php social_links(site_socials(), 'xm__socials'); ?>
      </div>
    </div>
    <div class="xm__word" aria-hidden="true"><?= e(mb_strtoupper($siteName)) ?></div>
  </div>

  <main id="main">
