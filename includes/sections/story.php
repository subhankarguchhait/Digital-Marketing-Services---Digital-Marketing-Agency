<?php defined('APP_NAME') || exit;
/** @var array $s */ ?>
<section class="section story" data-section="story">
  <div class="container story__grid">
    <div class="story__media reveal-clip"><?= img_tag(sv($s, 'image'), plain(sv($s, 'title'))) ?></div>
    <div>
      <?php sec_head($s, 'sec-head--light'); ?>
      <div class="prose prose--light reveal"><?= paras(sv($s, 'content')) ?></div>
      <ol class="timeline">
        <?php foreach (sl($s, 'milestones') as $i => $m): ?>
          <li class="reveal" style="--d:<?= ($i % 4) * 0.08 ?>s"><strong><?= e($m['year'] ?? '') ?></strong><h3><?= e($m['title'] ?? '') ?></h3><p><?= e($m['text'] ?? '') ?></p></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>
