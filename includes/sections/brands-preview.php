<?php defined('APP_NAME') || exit;
/** @var array $s */ $brands = public_collection('brands', max(1, (int)($s['limit'] ?? 3))); ?>
<section class="section brands-preview" data-section="brands">
  <div class="container">
    <div class="split-head">
      <?php sec_head($s, 'sec-head--light'); ?>
      <div class="reveal"><?php cms_button(sv($s, 'button_text'), sv($s, 'button_url'), 'btn btn--ghost-light'); ?></div>
    </div>
    <div class="brand-grid brand-grid--preview">
      <?php foreach ($brands as $i => $b) echo brand_card($b, $i, true); ?>
      <?php if (!$brands): ?><p class="empty-state"><i class="fa-regular fa-folder-open"></i>Brands will appear here once published.</p><?php endif; ?>
    </div>
  </div>
</section>
