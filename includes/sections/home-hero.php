<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="hero" data-section="hero">
  <div class="hero__bg" data-parallax="0.35"><?= img_tag(sv($s, 'image'), '', 'fetchpriority="high"') ?></div>
  <div class="hero__veil"></div>
  <canvas class="particles particles--hero" data-particles="90" aria-hidden="true"></canvas>
  <div class="hero__orb" aria-hidden="true"></div>
  <div class="container hero__content">
    <?php if (sv($s, 'eyebrow') !== ''): ?><p class="eyebrow eyebrow--light reveal"><span class="eyebrow__line"></span><span><?= e(sv($s, 'eyebrow')) ?></span></p><?php endif; ?>
    <h1 class="hero__title reveal-words"><?= rich(sv($s, 'title')) ?></h1>
    <?php if (sv($s, 'content') !== ''): ?><p class="hero__sub reveal"><?= e(sv($s, 'content')) ?></p><?php endif; ?>
    <div class="hero__actions reveal">
      <?php cms_button(sv($s, 'button_text'), sv($s, 'button_url'), 'btn btn--spark btn--lg'); ?>
      <?php cms_button(sv($s, 'cta2_text'), sv($s, 'cta2_url'), 'btn btn--ghost-light btn--lg', false); ?>
    </div>
  </div>
  <?php if (sv($s, 'side_text') !== ''): ?><div class="hero__side reveal" aria-hidden="true"><span><?= e(sv($s, 'side_text')) ?></span></div><?php endif; ?>
  <a href="#after-hero" class="hero__scroll" aria-label="Scroll down"><span></span><small>Scroll</small></a>
</section>
<div id="after-hero"></div>
