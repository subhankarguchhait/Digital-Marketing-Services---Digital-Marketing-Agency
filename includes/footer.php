<?php
/**
 * Magnetic footer — Dream Spark
 * Big magnetic "Let's talk" orb · scroll-reactive services marquee · magnetic socials & rolling links ·
 * copy-to-clipboard contacts · giant cursor-lit wordmark · live Kolkata clock · magnetic back-to-top.
 * Text comes from Admin → Settings (Footer, Contact, Social). Effects: assets/js/footer-magnetic.js
 * @var array $page
 */
defined('APP_NAME') || exit; // no direct access
$footerServices = public_collection('services', 6);
$marquee = array_slice(array_column(public_collection('services'), 'title'), 0, 10) ?: ['Brand Strategy', 'Social Media', 'Performance Ads', 'Websites', 'Photo & Video'];
$wa = contact_href('whatsapp', setting('whatsapp'));
$siteName = setting('site_name') ?: 'Dream Spark';
$ctaText = setting('footer_cta_text') ?: "Let's talk";
$ctaHref = link_href(setting('footer_cta_link', 'contact.php'));
$explore = [['', 'Home'], ['about.php', 'About Us'], ['brands.php', 'Our Brands'], ['contact.php', 'Contact']];
$contacts = array_filter([
    'email' => ['Email us', setting('email'), 'fa-regular fa-envelope'],
    'phone' => ['Call us', setting('phone'), 'fa-solid fa-phone'],
    'whatsapp' => ['WhatsApp', setting('whatsapp'), 'fa-brands fa-whatsapp'],
    'address' => ['Studio', setting('address'), 'fa-solid fa-location-dot'],
], fn($c) => $c[1] !== '');
$socials = array_filter(site_socials(), fn($u) => $u && filter_var($u, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $u));
// Helpers that also work on older copies of the project
$ver = function (string $path): string {
    $file = dirname(__DIR__) . '/' . $path;
    return url($path) . (is_file($file) ? '?v=' . filemtime($file) : '');
};
$footerLogo = setting('logo_stacked') ?: setting('logo');
$logoHtml = $footerLogo === '' ? '' : (function_exists('logo_picture')
    ? logo_picture($footerLogo, 'mf__logo-img', $siteName, 'loading="lazy" decoding="async"')
    : '<img class="mf__logo-img" src="' . e(media_url($footerLogo)) . '" alt="' . e($siteName) . '" loading="lazy" decoding="async">');
$roll = fn(string $t): string => '<span class="mf-roll" data-t="' . e($t) . '"><span>' . e($t) . '</span></span>';
?>
  </main>

  <footer class="mf" data-mf>
    <div class="mf__aurora" aria-hidden="true"><span></span><span></span><span></span></div>
    <div class="mf__grain" aria-hidden="true"></div>

    <!-- 1. Call to action with the magnetic orb -->
    <section class="container mf__hero" aria-labelledby="mf-title">
      <div class="mf__intro">
        <p class="mf__eyebrow"><span class="mf__dot" aria-hidden="true"></span>Taking new projects for <?= date('Y') ?></p>
        <h2 class="mf__headline" id="mf-title"><?= rich(setting('footer_headline') ?: "Let's ignite your *next chapter.*") ?></h2>
        <p class="mf__lead">Tell us where your brand wants to be. We'll reply within one working day with ideas — not a sales script.</p>
      </div>
      <a class="mf__orb" href="<?= e($ctaHref) ?>" data-mag="0.42" aria-label="<?= e($ctaText) ?>">
        <svg class="mf__orb-ring" viewBox="0 0 200 200" aria-hidden="true">
          <defs><path id="mfCircle" d="M100,100 m-82,0 a82,82 0 1,1 164,0 a82,82 0 1,1 -164,0"/></defs>
          <text><textPath href="#mfCircle" startOffset="0">START A PROJECT ✦ IGNITE YOUR GROWTH ✦ START A PROJECT ✦ IGNITE YOUR GROWTH ✦</textPath></text>
        </svg>
        <span class="mf__orb-core" data-mag-inner>
          <span class="mf__orb-label"><?= e($ctaText) ?></span>
          <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </span>
      </a>
    </section>

    <!-- 2. Services marquee (speeds up & reverses with scroll) -->
    <div class="mf__marquee" data-mf-marquee aria-hidden="true">
      <div class="mf__track">
        <?php for ($k = 0; $k < 2; $k++): ?><div class="mf__group"><?php foreach ($marquee as $i => $m): ?><span class="<?= $i % 2 ? 'is-outline' : '' ?>"><?= e($m) ?></span><i>✦</i><?php endforeach; ?></div><?php endfor; ?>
      </div>
    </div>

    <!-- 3. Columns -->
    <div class="container mf__grid">
      <div class="mf__brand">
        <a href="<?= e(url()) ?>" class="mf__logo" aria-label="<?= e($siteName) ?> home" data-mag="0.18">
          <?php if ($logoHtml !== ''): ?><?= $logoHtml ?>
          <?php else: ?><span class="brand__text"><?= site_name_html() ?></span><?php endif; ?>
        </a>
        <p><?= e(setting('footer_about')) ?></p>
        <?php if ($socials): ?>
          <div class="mf__socials">
            <?php foreach ($socials as $k => $u): ?>
              <a class="mf__social" href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($k)) ?>" data-mag="0.5"><span data-mag-inner><i class="<?= SOCIAL_ICONS[$k] ?>" aria-hidden="true"></i></span></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <nav class="mf__col" aria-label="Footer">
        <h3 class="mf__title">Explore</h3>
        <ul>
          <?php foreach ($explore as [$href, $label]): $active = basename($_SERVER['SCRIPT_NAME'] ?? '') === ($href ?: 'index.php'); ?>
            <li><a class="mf__link<?= $active ? ' is-current' : '' ?>" href="<?= e(url($href)) ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= $roll($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <div class="mf__col">
        <h3 class="mf__title">Services</h3>
        <ul>
          <?php foreach ($footerServices as $svc): ?>
            <li><a class="mf__link" href="<?= e(url('contact.php?service=' . rawurlencode($svc['title']))) ?>"><?= $roll($svc['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="mf__col mf__col--contact">
        <h3 class="mf__title">Get in touch</h3>
        <ul class="mf__contacts">
          <?php foreach ($contacts as $type => [$label, $val, $icon]): $ext = in_array($type, ['whatsapp', 'address'], true); ?>
            <li class="mf__contact">
              <span class="mf__contact-ico" aria-hidden="true"><i class="<?= $icon ?>"></i></span>
              <span class="mf__contact-body">
                <small><?= e($label) ?></small>
                <a href="<?= e(contact_href($type, $val)) ?>"<?= $ext ? ' target="_blank" rel="noopener"' : '' ?>><?= e($val) ?></a>
              </span>
              <?php if (in_array($type, ['email', 'phone'], true)): ?>
                <button type="button" class="mf__copy" data-copy="<?= e($val) ?>" aria-label="Copy <?= e(strtolower($label === 'Email us' ? 'email address' : 'phone number')) ?>"><i class="fa-regular fa-copy" aria-hidden="true"></i><span>Copy</span></button>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- 4. Giant cursor-lit wordmark -->
    <div class="mf__word" data-mf-word aria-hidden="true">
      <?php foreach (preg_split('//u', mb_strtoupper($siteName), -1, PREG_SPLIT_NO_EMPTY) as $ch): ?><span class="<?= $ch === ' ' ? 'is-space' : '' ?>"><?= $ch === ' ' ? '&nbsp;' : e($ch) ?></span><?php endforeach; ?>
    </div>

    <!-- 5. Bottom bar -->
    <div class="container mf__bottom">
      <p><?= e(setting('footer_copyright')) ?></p>
      <p class="mf__clock"><span class="mf__dot" aria-hidden="true"></span>Kolkata, IN · <time data-mf-clock>--:--</time></p>
      <p class="mf__tag"><?= e(setting('footer_tagline')) ?></p>
      <button type="button" class="mf__top" data-mf-top data-mag="0.45" aria-label="Back to top"><span data-mag-inner><i class="fa-solid fa-arrow-up" aria-hidden="true"></i></span></button>
    </div>
  </footer>

  <?php if ($wa): ?><a class="wa-float magnetic" href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a><?php endif; ?>
  <button class="to-top" type="button" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
  <div class="toast-zone" id="toastZone" aria-live="polite"></div>

  <?php foreach (['assets/js/animations.js', 'assets/js/main.js', 'assets/js/advanced.js', 'assets/js/navbar.js', 'assets/js/footer-magnetic.js'] as $js): if (is_file(dirname(__DIR__) . '/' . $js)): ?>
  <script src="<?= e($ver($js)) ?>" defer></script>
  <?php endif; endforeach; ?>
  <?php if (($page['slug'] ?? '') === 'contact'): ?><script src="<?= e($ver('assets/js/contact.js')) ?>" defer></script><?php endif; ?>
</body>
</html>
