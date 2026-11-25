<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="section intro" data-section="intro">
  <div class="container intro__grid">
    <div class="intro__text">
      <?php sec_head($s); ?>
      <div class="prose reveal"><?= paras(sv($s, 'content')) ?></div>
      <?php if (sv($s, 'signature') !== ''): ?><div class="intro__sign reveal"><span><?= e(sv($s, 'signature')) ?></span><small><?= e(sv($s, 'signature_role')) ?></small></div><?php endif; ?>
    </div>
    <div class="intro__media reveal-clip"><div data-parallax-inner="0.1"><?= img_tag(sv($s, 'image'), plain(sv($s, 'title'))) ?></div></div>
  </div>
</section>
