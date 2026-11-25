<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="section expertise" data-section="expertise">
  <div class="container expertise__grid">
    <div><?php sec_head($s); ?><div class="prose reveal"><?= paras(sv($s, 'content')) ?></div></div>
    <div class="bars">
      <?php foreach (sl($s, 'items') as $i => $it): $p = max(0, min(100, (int)($it['percent'] ?? 0))); ?>
        <div class="bar reveal" style="--d:<?= ($i % 4) * 0.08 ?>s">
          <div class="bar__head"><span><?= e($it['title'] ?? '') ?></span><span><?= $p ?>%</span></div>
          <div class="bar__track" role="progressbar" aria-valuenow="<?= $p ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= e($it['title'] ?? '') ?>"><div class="bar__fill" data-w="<?= $p ?>"></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
