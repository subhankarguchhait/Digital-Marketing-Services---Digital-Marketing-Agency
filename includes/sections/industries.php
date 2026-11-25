<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="section industries" data-section="industries">
  <div class="container">
    <?php sec_head($s, 'sec-head--center sec-head--light'); ?>
    <div class="industries__grid">
      <?php foreach (sl($s, 'items') as $i => $it): ?>
        <article class="industry reveal-card tilt" style="--d:<?= ($i % 5) * 0.08 ?>s">
          <span class="industry__icon"><i class="<?= e(safe_icon($it['icon'] ?? '')) ?>" aria-hidden="true"></i></span>
          <h3><?= e($it['title'] ?? '') ?></h3>
          <p><?= e($it['text'] ?? '') ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
