<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="section about-split" id="about" data-section="about">
  <div class="container about-split__grid">
    <div class="about-split__media reveal-clip">
      <div class="about-split__img"><div data-parallax-inner="0.12"><?= img_tag(sv($s, 'image'), plain(sv($s, 'title'))) ?></div></div>
      <?php if (sv($s, 'badge_value') !== ''):
        preg_match('/^([^\d]*)([\d.,]+)(.*)$/u', sv($s, 'badge_value'), $m); ?>
        <div class="about-split__badge">
          <strong<?= $m ? ' class="counter" data-count="' . e(str_replace(',', '', $m[2])) . '" data-prefix="' . e($m[1]) . '" data-suffix="' . e($m[3]) . '"' : '' ?>><?= e(sv($s, 'badge_value')) ?></strong>
          <span><?= e(sv($s, 'badge_label')) ?></span>
        </div>
      <?php endif; ?>
    </div>
    <div class="about-split__text">
      <?php sec_head($s); ?>
      <div class="prose reveal"><?= paras(sv($s, 'content')) ?></div>
      <?php if ($points = sl($s, 'points')): ?>
        <ul class="ticks reveal"><?php foreach ($points as $p): ?><li><i class="fa-solid fa-check" aria-hidden="true"></i><span><?= e($p) ?></span></li><?php endforeach; ?></ul>
      <?php endif; ?>
      <div class="reveal"><?php cms_button(sv($s, 'button_text'), sv($s, 'button_url')); ?></div>
    </div>
  </div>
</section>
