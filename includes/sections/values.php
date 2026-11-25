<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="section values" data-section="values">
  <div class="container">
    <?php sec_head($s, 'sec-head--center'); ?>
    <div class="values__grid">
      <?php foreach (sl($s, 'items') as $i => $it): ?>
        <article class="value reveal-card tilt" style="--d:<?= ($i % 4) * 0.08 ?>s"><i class="<?= e(safe_icon($it['icon'] ?? '')) ?>" aria-hidden="true"></i><h3><?= e($it['title'] ?? '') ?></h3><p><?= e($it['text'] ?? '') ?></p></article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
