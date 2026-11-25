<?php defined('APP_NAME') || exit;
/** @var array $s */ $items = sl($s, 'items'); if (!$items) return; ?>
<section class="marquee" data-section="marquee" aria-label="What we do">
  <div class="marquee__track" style="--marquee-speed:<?= max(20, count($items) * 6) ?>s">
    <?php for ($r = 0; $r < 4; $r++): foreach ($items as $t): ?>
      <span class="marquee__item"<?= $r ? ' aria-hidden="true"' : '' ?>><?= e($t) ?><svg viewBox="0 0 48 48" aria-hidden="true"><use href="#ds-spark"></use></svg></span>
    <?php endforeach; endfor; ?>
  </div>
</section>
