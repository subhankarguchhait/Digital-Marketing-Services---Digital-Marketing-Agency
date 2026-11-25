<?php defined('APP_NAME') || exit;
/** @var array $s */ $dark = $dark ?? false; ?>
<section class="section why<?= $dark ? ' why--dark' : '' ?>" data-section="why">
  <div class="container why__grid">
    <div class="why__intro">
      <?php sec_head($s, $dark ? 'sec-head--light' : ''); ?>
      <div class="reveal"><?php cms_button(sv($s, 'button_text'), sv($s, 'button_url'), 'btn btn--outline'); ?></div>
    </div>
    <div class="why__items">
      <?php foreach (sl($s, 'items') as $i => $it): ?>
        <article class="why-item reveal-card" style="--d:<?= ($i % 4) * 0.08 ?>s">
          <i class="<?= e(safe_icon($it['icon'] ?? '')) ?>" aria-hidden="true"></i>
          <h3><?= e($it['title'] ?? '') ?></h3>
          <p><?= e($it['text'] ?? '') ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
